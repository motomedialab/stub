<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Meilisearch extends DockerService
{
    public int $order = 20;
    public string $name = 'MeiliSearch';
    public string $description = 'Smart search engine for use with Laravel Scout';

    public ?array $requires = [
        'laravel/scout'
    ];

    public ?string $config = <<<TEXT
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=ecd0c20e81b33a4246fa90a7c3513704c3fea09a
TEXT;
}
