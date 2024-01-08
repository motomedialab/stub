<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Redis extends DockerService
{
    public int $order = 40;

    public string $name = 'Redis';
    public string $description = 'Super fast in memory store used for caching';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # Binds to port 6379 of your local machine
    redis:
        image: redis:alpine3.19
        ports:
          - "6379:6379"
YAML;

    public ?string $config = <<<TEXT
# Configure Redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
TEXT;


}
