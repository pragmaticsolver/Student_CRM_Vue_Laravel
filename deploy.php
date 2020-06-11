<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project repository
set('repository', 'git@gitlab.com:vinaysudani/bce.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

set('ssh_multiplexing', true);

set('keep_releases', 4);

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', [
    'storage/app/public/books',
    'storage/app/public/lesson/video',
    'storage/app/public/lesson/thumbnail',
    'storage/app/public/lesson_files',
    'storage/app/public/student_paper_test_files',
    'storage/app/temp_uploads',
    'storage/app/public/todo_files',
    'storage/app/public/students',
    'storage/app/public/course',
    'resources/generated', // ziggy.js file is created in this folder
    'storage/app/public/schedule_files'
]);

// Hosts

host('setuptest')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/uteach.jp/httpdocs/setuptest_code')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'uteach')
    ->set('http_group', 'psacln')
    ->set('node_path', '/opt/plesk/node/10/bin/');

host('test')
    ->hostname('bce_server')
    ->set('branch', 'test')
    ->set('deploy_path', '/var/www/vhosts/uteach.jp/httpdocs/test_code')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'uteach')
    ->set('http_group', 'psacln')
    ->set('node_path', '/opt/plesk/node/10/bin/');

host('pacificenglish')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/uteach.jp/httpdocs/pacificenglish_code')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'uteach')
    ->set('http_group', 'psacln')
    ->set('node_path', '/opt/plesk/node/10/bin/');

host('waxtakumidou')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/uteach.jp/httpdocs/waxtakumidou_code')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'uteach')
    ->set('http_group', 'psacln')
    ->set('node_path', '/opt/plesk/node/10/bin/');

host('englishstudio')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/uteach.jp/httpdocs/englishstudio_code')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'uteach')
    ->set('http_group', 'psacln')
    ->set('node_path', '/opt/plesk/node/10/bin/');

// https://app.benchanjr.com/
host('bcejr')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/benchanjr.com/bcejr')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'benchanjr')
    ->set('http_group', 'psaserv')
    ->set('node_path', '/opt/plesk/node/10/bin/');


// https://app.benchaneikaiwa.com/
host('bcekt')
    ->hostname('bce_server')
    ->set('branch', 'bcekt')
    ->set('deploy_path', '/var/www/vhosts/benchaneikaiwa.com/bcekt')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'benchaneikaiwa')
    ->set('http_group', 'psaserv')
    ->set('node_path', '/opt/plesk/node/10/bin/');

// https://online.benchaneikaiwa.com/
host('bce_online')
    ->hostname('bce_server')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/benchaneikaiwa.com/bce_online')
    ->set('writable_mode', 'chown')
    ->set('composer_options', 'install --no-scripts')
    ->set('bin/composer', "composer")
    ->set('bin/php', "/opt/plesk/php/7.2/bin/php")
    ->set('http_user', 'benchaneikaiwa')
    ->set('http_group', 'psaserv')
    ->set('node_path', '/opt/plesk/node/10/bin/');

// Tasks

task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate --force');
})->once();

task('artisan:seed', function () {
    run('{{bin/php}} {{release_path}}/artisan db:seed --force');
})->once();

task('firstdeploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'create_env',
    'deploy:unlock',
]);

task('create_env', function () {
    run('cd {{release_path}} && sudo cp -R .env.example .env');
    run('cd {{release_path}} && {{bin/php}} artisan key:generate');
});

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'ziggy_file',
    'npm_install',
    'npm_prod',
    'artisan:view:cache',
    //'artisan:config:cache',
    //'artisan:optimize',
    'artisan:migrate',
    'deployment_specific_code',
    'artisan:seed',
    'clear_permission_cache',
    'deploy:symlink',
    'deploy:unlock',
    'artisan:queue:restart',
    'cleanup',
]);
after('deploy', 'success');

task('clear_permission_cache', function () {
    run('cd {{release_path}} && {{bin/php}} artisan permission:cache-reset');
});

task('deployment_specific_code', function () {
    // run('cd {{release_path}} && {{bin/php}} artisan onetime:special_deployment');
});

task('ziggy_file', function () {
    run('cd {{release_path}} && {{bin/php}} artisan ziggy:generate "resources/generated/ziggy.js"');
});

task('npm_install', function () {
    run('cd {{release_path}} && export PATH=$PATH:{{node_path}} && npm install');
});
task('npm_prod', function () {
    run('cd {{release_path}} && export PATH=$PATH:{{node_path}} && npm run prod');
});

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');