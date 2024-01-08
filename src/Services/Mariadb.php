<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Mariadb extends DockerService
{
    public int $order = 10;
    public string $name = 'MariaDB';
    public string $description = 'Database server for persisting data';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # binds to port 3306 of your local machine
    mariadb:
        image: mariadb:11.2
        environment:
            - MYSQL_ROOT_PASSWORD=password
            - MYSQL_DATABASE=laravel
            - MYSQL_USER=user
            - MYSQL_PASSWORD=password
        ports:
            - "3306:3306"
YAML;

    public ?string $config = <<<TEXT
# Database setup
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=password
TEXT;

}
