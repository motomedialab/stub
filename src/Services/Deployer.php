<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

use function Laravel\Prompts\text;

class Deployer extends DockerService
{
    public int $order = 21;

    public string $name = 'Deployer';

    public string $description = 'SSH Atomic Deployment tool';

    public function build(string &$composeFile): void
    {
        exec('composer require --dev deployer/deployer');

        \Laravel\Prompts\info('Basic information for deployer. Can be configured at a later date...');

        $hostname = text(
            'Optional server hostname for deploy file',
            'E.g. myserver.testing.net',
        );
        $username = text(
            'Optional http username for production environment',
            'E.g. myuser',
        );

        preg_match('/url = (.*)\n/i', file_get_contents(base_path('.git/config')), $matches);

        $contents = file_get_contents(__DIR__ . '/../../stubs/deploy.php');
        $contents = str_replace(
            ['{{REPOSITORY}}', '{{HTTP_USER}}', '{{HOSTNAME}}', '{{DOMAIN}}'],
            [
                $matches[1] ?? '{{REPOSITORY}}',
                $username === "" ? 'example_usern' : $username,
                $hostname === "" ? 'example.hostname' : $hostname,
                $this->getVariable('DOMAIN')
            ],
            $contents
        );

        file_put_contents(base_path('deploy.php'), $contents);
    }
}
