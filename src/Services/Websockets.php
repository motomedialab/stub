<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Websockets extends DockerService
{
    public int $order = 70;

    public string $name = 'Websockets';

    public string $description = 'Soketi service to handle websockets';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # binds to ports 6001 & 9601 (web interface) of your local machine
    soketi:
        depends_on:
          - nginx
        image: quay.io/soketi/soketi:1.5-16-debian
        networks:
          alltrac:
            aliases:
              - websockets
              - pusher
        command: soketi start --config=/config/config.json
        volumes:
          - .:/var/www:delegated
          - ./docker/soketi:/config
          - ~/.mkcert-keys:/mkcert:delegated # map our global mkcert key
        environment:
          - SOKETI_SSL_CA=/mkcert/rootCA-key.pem
        ports:
          - "6001:6001"
          - "9601:9601"
YAML;

    public ?string $config = <<<TEXT
# Pusher setup
PUSHER_APP_ID={{APP_ID}}
PUSHER_APP_KEY={{APP_KEY}}
PUSHER_APP_SECRET={{APP_SECRET}}
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=ws.{{DOMAIN}}
PUSHER_PORT=443
PUSHER_SCHEME=https

# Vite Pusher config
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
TEXT;

    public array $variables = [
        'APP_ID' => 'appId',
        'APP_KEY' => 'appKey',
        'APP_SECRET' => 'appSecret',
    ];

    public function files(): array
    {
        return [
            'docker/soketi/config.json' => json_encode([
                'debug' => true,
                'port' => 6001,
                'appManager.array.apps' => [
                    'id' => $this->getVariable('APP_ID'),
                    'key' => $this->getVariable('APP_KEY'),
                    'secret' => $this->getVariable('APP_SECRET'),
                    'webhooks' => [],
                ]
            ], JSON_PRETTY_PRINT)
        ];
    }


}
