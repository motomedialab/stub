<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Pest extends DockerService
{
    public int $order = 18;

    public string $name = 'PEST Testing';

    public string $description = 'Modern Testing Suite';

    public function build(string &$composeFile): void
    {
        exec('composer require pestphp/pest --dev --with-all-dependencies --no-interaction');
        exec('./vendor/bin/pest --init');
    }
}
