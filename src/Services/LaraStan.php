<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class LaraStan extends DockerService
{
    public int $order = 20;

    public string $name = 'Larastan';

    public string $description = 'Static analysis/code quality tool';

    public function files(): array
    {
        return [
            'phpstan.neon' => <<<TEXT
includes:
    - vendor/larastan/larastan/extension.neon

parameters:

    paths:
        - app/

    # Level 9 is the highest level
    level: 5

#    ignoreErrors:
#        - '#PHPDoc tag @var#'
#
#    excludePaths:
#        - ./*/*/FileToBeExcluded.php
#
#    checkMissingIterableValueType: false
TEXT
        ];
    }

    public function build(string $dockerComposeFile): void
    {
        exec('composer require larastan/larastan:^2.0 --dev');
    }
}
