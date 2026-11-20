<?php

namespace Tests\Feature;

use App\Support\JsonFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    private string $dataDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataDir = storage_path('framework/testing/portfolio-'.Str::random(8));
        File::ensureDirectoryExists($this->dataDir);

        foreach (['profile.json', 'projects.json'] as $file) {
            File::copy(base_path('data/'.$file), $this->dataDir.'/'.$file);
        }

        File::put($this->dataDir.'/ratings.json', json_encode(['version' => 1, 'votes' => []], JSON_THROW_ON_ERROR));

        config(['portfolio.path' => $this->dataDir]);
        JsonFile::forget();

        // Feature tests send an en-US Accept-Language header by default.
        $this->withHeader('Accept-Language', 'ru-RU,ru;q=0.9,en;q=0.5');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dataDir);
        JsonFile::forget();

        parent::tearDown();
    }

    public function test_the_home_page_renders_in_russian_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Инфраструктура без сюрпризов')
            ->assertSee('Избранные кейсы')
            ->assertSee('wrcs-theme', false);
    }

    public function test_the_language_switch_persists_the_locale(): void
    {
        $this->get('/lang/en')->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Selected work')
            ->assertSee('Infrastructure without surprises');
    }

    public function test_a_project_page_renders(): void
    {
        $this->get('/projects/pipeline-2')
            ->assertOk()
            ->assertSee('Pipeline 2.0')
            ->assertSee('Sibline Cloud')
            ->assertSee('Оценка кейса')
            ->assertSee('параллельные стадии');
    }

    public function test_an_unknown_project_returns_404(): void
    {
        $this->get('/projects/does-not-exist')->assertNotFound();
    }

    public function test_a_visitor_can_rate_a_project(): void
    {
        $response = $this->postJson('/projects/pipeline-2/rate', ['value' => 5]);

        $response->assertOk()
            ->assertJsonPath('value', 5)
            ->assertJsonPath('ok', true);

        $votes = JsonFile::read($this->dataDir.'/ratings.json', fresh: true)['votes']['pipeline-2'] ?? [];

        $this->assertCount(1, $votes);
        $this->assertSame(5, array_values($votes)[0]);
        $this->assertSame(24, $response->json('stats.histogram.0'));
    }

    public function test_the_same_visitor_updates_the_rating_instead_of_adding_one(): void
    {
        $this->postJson('/projects/pipeline-2/rate', ['value' => 4])->assertOk();
        $this->postJson('/projects/pipeline-2/rate', ['value' => 2])->assertOk();

        $votes = JsonFile::read($this->dataDir.'/ratings.json', fresh: true)['votes']['pipeline-2'] ?? [];

        $this->assertCount(1, $votes);
        $this->assertSame(2, array_values($votes)[0]);
    }

    public function test_invalid_rating_values_are_rejected(): void
    {
        $this->postJson('/projects/pipeline-2/rate', ['value' => 6])->assertUnprocessable();
        $this->postJson('/projects/pipeline-2/rate', ['value' => 0])->assertUnprocessable();
        $this->postJson('/projects/pipeline-2/rate', ['value' => 'five'])->assertUnprocessable();
    }

    public function test_unknown_projects_cannot_be_rated(): void
    {
        $this->postJson('/projects/nope/rate', ['value' => 5])->assertNotFound();
    }
}
