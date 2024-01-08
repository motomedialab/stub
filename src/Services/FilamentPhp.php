<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class FilamentPhp extends DockerService
{
    public int $order = 22;

    public string $name = 'FilamentPHP';

    public string $description = 'Rapid development using the TALL stack';

    public function build(string &$composeFile): void
    {
        exec('composer require filament/filament:"^3.1" -W');

        exec('php artisan filament:install --panels');
    }

}
