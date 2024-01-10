<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class LocalStackS3 extends DockerService
{
    public int $order = 30;
    public string $name = 'Amazon S3 Clone';
    public string $description = 'Amazon S3 style filesystem';

    public ?string $nginxConfig = <<<TEXT
location /s3bucket {
        proxy_pass             http://localstack-s3:4566;
        proxy_read_timeout     60;
        proxy_connect_timeout  60;
        proxy_redirect         off;

        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_cache_bypass \$http_upgrade;
    }
TEXT;

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # binds to port 4566 of your local machine
    localstack-s3:
        image: localstack/localstack
        environment:
            - SERVICES=s3
            - BUCKET_NAME={{BUCKET_NAME}}
            - AWS_DEFAULT_REGION=eu-west-1
            - AWS_ACCESS_KEY_ID=key
            - AWS_SECRET_ACCESS_KEY=secret
        volumes:
            - ./docker/localstack/entrypoint.sh:/etc/localstack/init/ready.d/start-localstack.sh
        ports:
            - "4566:4566"
YAML;

    public function getVariables(): array
    {
        return [
            ...parent::getVariables(),
            'BUCKET_NAME' => 's3bucket',
        ];
    }

    public ?string $config = <<<TEXT
# S3/LocalStack storage
AWS_ACCESS_KEY_ID=key
AWS_SECRET_ACCESS_KEY=secret
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET={{BUCKET_NAME}}
AWS_ENDPOINT=https://{{DOMAIN}}/s3
AWS_USE_PATH_STYLE_ENDPOINT=true
TEXT;

    public ?string $dockerStubDir = 'localstack';
}
