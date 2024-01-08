<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class PhpFpm extends DockerService
{
    public int $order = 5;
    public string $name = 'PHP-FPM';
    public string $description = 'Handles inbound PHP requests routed from NGINX';

    public ?string $dockerStubDir = 'php-fpm';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    php-fpm:
        build: docker/php-fpm
        volumes:
            - .:/var/www:delegated # map our project to /var/www
            - ./docker/php-fpm/php.ini:/usr/local/etc/php/conf.d/99-overrides.ini:delegated # map our PHP configuration
            - ~/.mkcert-keys:/mkcert:delegated # map our authority keys for curl to respect
YAML;
}
