<?php

namespace Motomedialab\Stub\Services;

use Illuminate\Support\Str;
use Motomedialab\Stub\Concerns\DockerService;

class Meilisearch extends DockerService
{
    public int $order = 20;
    public string $name = 'MeiliSearch';
    public string $description = 'Smart search engine for use with Laravel Scout';

    public ?array $requires = [
        'laravel/scout'
    ];

    public ?string $composeStub = <<<TEXT
    meilisearch:
        image: getmeili/meilisearch:v1.2
        environment:
            - MEILI_MASTER_KEY={{MEILISEARCH_KEY}}
        ports:
            - "7700:7700"
TEXT;



    public ?string $config = <<<TEXT
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY={{MEILISEARCH_KEY}}
TEXT;

    public function __construct(array $variables = [])
    {
        parent::__construct([
            'MEILISEARCH_KEY' => Str::random('40'),
        ]);
    }
}
