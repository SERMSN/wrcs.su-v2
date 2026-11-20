<?php

namespace Tests\Unit;

use App\Support\JsonFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class JsonFileTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('framework/testing/json-'.Str::random(8).'.json');
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        JsonFile::forget();

        parent::tearDown();
    }

    public function test_it_writes_and_reads_a_json_file(): void
    {
        JsonFile::write($this->path, ['name' => 'кова.ops', 'votes' => [5, 4, 4]]);

        $this->assertFileExists($this->path);
        $this->assertSame(['name' => 'кова.ops', 'votes' => [5, 4, 4]], JsonFile::read($this->path, fresh: true));
    }

    public function test_update_keeps_existing_data(): void
    {
        JsonFile::write($this->path, ['votes' => ['a' => 5]]);

        JsonFile::update($this->path, function (array $data): array {
            $data['votes']['b'] = 3;

            return $data;
        });

        $this->assertSame(['votes' => ['a' => 5, 'b' => 3]], JsonFile::read($this->path, fresh: true));
    }

    public function test_update_creates_the_file_when_missing(): void
    {
        JsonFile::update($this->path, fn (array $data): array => $data + ['votes' => []]);

        $this->assertSame(['votes' => []], JsonFile::read($this->path, fresh: true));
    }
}
