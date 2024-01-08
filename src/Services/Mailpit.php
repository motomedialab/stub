<?php

namespace Motomedialab\Stub\Services;

use Motomedialab\Stub\Concerns\DockerService;

class Mailpit extends DockerService
{
    public int $order = 15;
    public string $name = 'MailPit';
    public string $description = 'Fake mailbox for receiving email';

    public ?string $composeStub = <<<YAML
    # {{DESCRIPTION}}
    # web interface available on bound port 8025 of your local machine
    mailpit:
        networks:
            default:
                aliases:
                    - mailhog
        image: axllent/mailpit:latest
        ports:
            - "8025:8025"

YAML;

    public ?string $config = <<<TEXT
# Mail setup
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@{{DOMAIN}}
MAIL_FROM_NAME="\${APP_NAME}"
TEXT;


}
