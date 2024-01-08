<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'Laravel Application');
set('repository', '{{REPOSITORY}}');
set('http_user', '{{HTTP_USER}}');

set('bin/composer', '{{bin/php}} /usr/local/bin/composer');
set('git_tty', true);
set('bin/service', fn() => locateBinaryPath('service'));

host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('hostname', '{{HOSTNAME}}')
    ->set('branch', 'main')
    ->set('remote_user', '{{http_user}}')
    ->set('deploy_path', '/home/{{HTTP_USER}}/htdocs/{{DOMAIN}}/production');

// Shared files/dirs between deploys
set('shared_files', ['.env']);
set('shared_dirs', []);
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// ensure we have a tag on production.
task('validate:tag', function () {
    if (input()->getOption('tag')) {
        return;
    }

    throw new \Exception('You must supply a valid tag to deploy to this environment');
})->select('stage=production');

// check if we are compiling resources
task('compile_resources', function () {
    $compile_resources = ask('Would you like to compile resources [y|yes]?');
    $compile_resources = in_array(strtolower($compile_resources), ['y', 'yes']);
    set('compile_resources', $compile_resources);
});
before('deploy', 'validate:tag');
after('validate:tag', 'compile_resources');

// Build our assets locally
task('npm:build', function () {
    if (get('compile_resources') || !test('[ -d {{previous_release}}/public ]')) {
        writeLn("Compiling assets locally. This can take a while...");
        runLocally('npm run build -s');
    } else {
        writeLn("Skipping asset compilation.");
    }
})->desc('Compile npm files locally');
after('deploy:shared', 'npm:build');


//// Task to load in our remote .env file for local compilation
//task('env:load', function () {
//    runLocally('cp .env .env.deployer');
//    download('{{deploy_path}}/shared/.env', '.env');
//})->desc('Load the contents of our remote .env file so we can compile with it locally');

//// Task to restore our local env file once it has been changed by env:load
//task('env:restore', function () {
//    if (testLocally('[ -f ./.env.deployer ]')) {
//        runLocally('mv .env.deployer .env');
//    }
//})->desc('Recover local environment file after env:load has been used');

//before('npm:build', 'env:load');
//after('npm:build', 'env:restore');

// Upload our locally compiled assets
task('deploy:upload-public', function () {
    if (get('compile_resources') || !test('[ -d {{previous_release}}/public ]')) {
        upload('public', '{{release_path}}', [
            'options' => ['--exclude=hot']
        ]);
    } else {
        writeLn('Copying public directory from previous release');
        run('cp -R {{previous_release}}/public {{release_path}}');
    }
})->desc('Upload the public directory to the environment');
after('npm:build', 'deploy:upload-public');

task('artisan:migrate', fn() => writeLn('Skipping migrations'));

task('deploy:public', function () {
    upload('public', '{{release_path}}', [
        'options' => ['--exclude=hot']
    ]);
})->desc('Upload the public directory to the environment');
after('deploy:shared', 'deploy:public');

task('bun:install', function () {
    run('cd {{release_path}} && bun install');
})->desc('Install composer dependencies');
after('deploy:vendors', 'bun:install');


// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'env:restore');

// handle queue restarts
//after('deploy:success', 'artisan:queue:restart');
after('deploy:success', 'artisan:cache:clear');
after('rollback', 'artisan:queue:restart');
