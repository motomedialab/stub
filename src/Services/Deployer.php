<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Deployer extends DockerService
{
    public int $order = 21;

    public string $name = 'Deployer';

    public string $description = 'SSH Atomic Deployment tool';

    public function build(string $dockerComposeFile): void
    {
        exec('composer require --dev deployer/deployer');
        exec('vendor/bin/dep init');
    }

}
