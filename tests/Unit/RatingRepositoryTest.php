<?php

namespace Tests\Unit;

use App\Repositories\RatingRepository;
use App\Support\JsonFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class RatingRepositoryTest extends TestCase
{
    private string $path;

    private RatingRepository $ratings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('framework/testing/ratings-'.Str::random(8).'.json');
        JsonFile::write($this->path, ['version' => 1, 'votes' => []]);

        $this->ratings = new RatingRepository($this->path);
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        JsonFile::forget();

        parent::tearDown();
    }

    public function test_it_aggregates_the_seed_histogram(): void
    {
        $stats = $this->ratings->stats('case', [2, 1, 0, 0, 0]);

        $this->assertSame(3, $stats['count']);
        $this->assertSame(14, $stats['sum']);
        $this->assertSame(4.67, $stats['average']);
        $this->assertSame([67, 33, 0, 0, 0], $stats['percent']);
    }

    public function test_a_vote_is_added_to_the_histogram(): void
    {
        $stats = $this->ratings->addVote('case', 5, 'voter-a', [2, 1, 0, 0, 0]);

        $this->assertSame(4, $stats['count']);
        $this->assertSame([3, 1, 0, 0, 0], $stats['histogram']);
        $this->assertSame(5, $this->ratings->userVote('case', 'voter-a'));
    }

    public function test_the_same_voter_updates_the_existing_vote(): void
    {
        $this->ratings->addVote('case', 5, 'voter-a', []);
        $stats = $this->ratings->addVote('case', 2, 'voter-a', []);

        $this->assertSame(1, $stats['count']);
        $this->assertSame([0, 0, 0, 1, 0], $stats['histogram']);
        $this->assertSame(2, $this->ratings->userVote('case', 'voter-a'));
    }

    public function test_votes_are_isolated_per_project(): void
    {
        $this->ratings->addVote('one', 5, 'voter-a', []);
        $this->ratings->addVote('two', 1, 'voter-a', []);

        $this->assertSame(5, $this->ratings->userVote('one', 'voter-a'));
        $this->assertSame(1, $this->ratings->userVote('two', 'voter-a'));
    }
}
