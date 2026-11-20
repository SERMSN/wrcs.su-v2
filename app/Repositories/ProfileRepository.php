<?php

namespace App\Repositories;

use App\Support\JsonFile;

final class ProfileRepository
{
    public function __construct(private readonly ?string $path = null) {}

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return JsonFile::read($this->file());
    }

    /**
     * @return array<string, mixed>
     */
    public function identity(): array
    {
        return $this->get()['identity'] ?? [];
    }

    public function experienceYears(): int
    {
        $since = (int) ($this->identity()['since'] ?? now()->year);

        return max(1, (int) now()->year - $since);
    }

    private function file(): string
    {
        return $this->path ?? rtrim((string) config('portfolio.path'), '/').'/'.config('portfolio.files.profile');
    }
}
