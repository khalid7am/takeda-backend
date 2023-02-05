<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'takeda-backend');

// Project repository
set('repository', 'git@gitlab.com:weborigo/stilldesign/takeda/backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['storage']);

// Writable dirs by web server
add('writable_dirs', ['shared', 'storage', 'vendor']);
set('allow_anonymous_stats', false);

// Currently only deploying to XBody staging
host('46.101.194.225')
    ->set('deploy_path', '/var/www/takeda/backend/{{branch}}')
    ->set('remote_user', 'gitlab')
    ->set('docker_path', '/var/www/docker')
    ->set('working_dir_during_deploy', '/var/www/takeda/backend/{{branch}}/release')
    ->set('working_dir_after_deploy', '/var/www/takeda/backend/{{branch}}/current')
    ->set('container_name', 'takeda_workspace_1')
    ->set('update_code_strategy', 'clone')
    ->set('keep_releases', 5)
    ->set('http_user', '1000');

// Tasks
task('docker:deploy:vendors', function() {
    // Note currently it is not necessary to move dirs,
    // but the style might be something like this
    /// when we make docker-compose work here.
    run('cd {{docker_path}}');
    run('docker exec -u 1000 -w {{working_dir_during_deploy}} {{container_name}} composer install --optimize-autoloader --no-dev');
});

task('docker:artisan:migrate', function () {
    run('cd {{docker_path}}');
    run('docker exec -w {{working_dir_during_deploy}} {{container_name}} php artisan migrate --force');

    if (get('branch') !== 'master') {
        run('docker exec -w {{working_dir_during_deploy}} {{container_name}} php artisan db:seed');
    }
});

task('deploy', [
    // Clone the code
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    // Installs deps inside container
    'docker:deploy:vendors',
    // Only then make everything writable
    'deploy:writable',
    // Migrate
    'docker:artisan:migrate',
    // Symlink shared dirs and files, unlock deploy, remove old releases
    'deploy:publish',
]);

task('configure', function () {
    run('cd {{docker_path}}');
    run('docker exec -w {{working_dir_after_deploy}} {{container_name}} php artisan storage:link');
    run('docker exec -w {{working_dir_after_deploy}} {{container_name}} php artisan cache:clear');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
