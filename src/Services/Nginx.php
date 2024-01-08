<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Nginx extends DockerService
{
    public int $order = 0;

    public string $name = 'NGINX Web Server';
    public string $description = 'Handles inbound HTTP/S requests, SSL and proxying';
    public ?string $dockerStubDir = 'nginx';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # bind to ports 80 (HTTP) & 443 (HTTPS) of your local machine
    nginx:
        build: docker/nginx
        networks:
            default:
                aliases:
                    - {{DOMAIN}}
        environment:
            - SSL_DOMAINS=localhost,{{DOMAIN}},*.{{DOMAIN}}
        entrypoint: [ "sh", "-c", "chmod +x /entrypoint.sh && sh /entrypoint.sh" ]
        volumes:
            - .:/var/www:delegated # map our project to /var/www
            - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf:delegated # map our nginx config file
            - ./docker/nginx/entrypoint.sh:/entrypoint.sh # map our nginx entrypoint
            - ./docker/certificates:/certs:delegated # map our local project keys
            - ~/.mkcert-keys:/root/.local/share/mkcert:delegated # map our authority keys (need to create ~/.mkcert-keys locally)
        ports:
            - "80:80"
            - "443:443"
YAML;
}
