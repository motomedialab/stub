<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class PhpFpm extends DockerService
{
    public int $order = 5;
    public string $name = 'PHP-FPM';
    public string $description = 'Handles inbound PHP requests routed from NGINX';

    public ?string $dockerStubDir = 'php-fpm';

    public ?string $nginxConfig = <<<TEXT
location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
    }
TEXT;

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
