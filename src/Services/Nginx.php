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

    private string $baseNginxConfig = <<<CONF
server {
    # listen on port 80 & 443
    listen 80 default_server;
    listen 443 ssl http2 default_server;

    # increase our max body size
    client_max_body_size 5M;

    # define the path to our certificates
    ssl_certificate /certs/dev.pem;
    ssl_certificate_key /certs/dev.key;
    ssl_session_timeout 5m;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv3:+EXP;
    ssl_prefer_server_ciphers on;

    # force rewrite to SSL
    if (\$scheme = http) {
        return 302 https://\$host\$request_uri;
    }

    index index.php index.html;
    server_name localhost;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;

    root /var/www/public;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    {{LocalStackS3}}
    
    {{Websockets}}
    
    {{PhpFpm}}
}
CONF;


    public function build(string &$composeFile): void
    {
        parent::build($composeFile);

        $this->configureNginxConfig();
    }


    protected function configureNginxConfig(): void
    {
        // loop through our services
        foreach($this->command->chosenServices as $service) {
            $find = '{{' . class_basename($service) . '}}';
            $this->baseNginxConfig = $service->nginxConfig
                ? str_replace($find, $service->nginxConfig, $this->baseNginxConfig)
                : str_replace($find, '', $this->baseNginxConfig);
        }

        // persist our updated file
        file_put_contents(base_path('docker/nginx/nginx.conf'), $this->baseNginxConfig);
    }

}
