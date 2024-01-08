## MotoMediaLab Stub

Stubs out Laravel projects with relevant packages and Docker configuration to
skip the setup process and dive right into code. Based upon our commonly used
packages and configurations.

This package makes some assumptions:

1. You have docker installed and understand how to use it
2. You have dnsmasq configured to route any .test domains to your local machine
3. You are installing this as a development repository into a **fresh** laravel project
4. You have already / will add `~/.mkcert-keys/rootCA.pem` to your Keychain or Firefox

If you haven't already created your Laravel project, you should do so using the command below:

```bash
composer create-project laravel/laravel example-app
```

Then you can install and run this package using:

```bash
composer require motomedialab/stub --dev

php artisan motomedialab:stub
```

We also recommend having the below in your `/etc/hosts` file for easier communication with the Docker containers:

```text
127.0.0.1       mariadb mysql php-fpm mailhog mailpit nginx soketi
```
