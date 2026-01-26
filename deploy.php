<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('repository', 'git@github.com:dabyd/iwantit.git');
set('branch', 'main');

// Configuración PHP
set('php_version', '8.4');
set('bin/php', '/usr/bin/php');

// Stage por defecto (para que funcione `dep deploy` sin especificar stage)
set('default_stage', 'production');

// Shared files/dirs between deploys
add('shared_files', [
    '.env',
]);

add('shared_dirs', [
    'storage',
    'public/uploads',
]);

// Writable dirs by web server
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'public/uploads',
]);

// Número de releases a mantener
set('keep_releases', 5);

// Configuración de permisos (usar chmod en lugar de ACL)
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0775');
set('http_user', 'www-data');

// Allow anonymous stats (optional)
set('allow_anonymous_stats', false);

// Hosts (Deployer 6.x syntax)
host('production')
    ->hostname('uat.i-want-it.es')
    ->user('ubuntu')
    ->set('deploy_path', '/var/www2/iwantit')
    ->stage('production');

// Tasks

// Task para limpiar cachés de Laravel
desc('Clear all Laravel caches');
task('artisan:cache:clear:all', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
    run('{{bin/php}} {{release_path}}/artisan config:clear');
    run('{{bin/php}} {{release_path}}/artisan route:clear');
    run('{{bin/php}} {{release_path}}/artisan view:clear');
});

// Task para optimizar Laravel (cachear config, routes, views)
desc('Optimize Laravel');
task('artisan:optimize', function () {
    run('{{bin/php}} {{release_path}}/artisan config:cache');
    run('{{bin/php}} {{release_path}}/artisan route:cache');
    run('{{bin/php}} {{release_path}}/artisan view:cache');
});

// Task para reiniciar PHP-FPM
desc('Restart PHP-FPM');
task('php-fpm:restart', function () {
    run('sudo systemctl restart php8.2-fpm');
})->once();

// Task para reiniciar Nginx
desc('Restart Nginx');
task('nginx:restart', function () {
    run('sudo systemctl restart nginx');
})->once();

// Task para reiniciar servicios
desc('Restart services (PHP-FPM & Nginx)');
task('services:restart', [
    'php-fpm:restart',
    'nginx:restart',
]);

// Task para ejecutar migraciones
desc('Run database migrations');
task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate --force');
});

// Hooks - añadir tareas al flujo de deploy
after('deploy:symlink', 'artisan:cache:clear:all');
after('artisan:cache:clear:all', 'artisan:optimize');
after('artisan:optimize', 'services:restart');

// Hooks
after('deploy:failed', 'deploy:unlock');

// Información tras el deploy
after('deploy', 'deploy:success');

desc('Deploy completed successfully');
task('deploy:success', function () {
    writeln('<info>✅ Deploy completed successfully!</info>');
    writeln('<info>🌐 URL: https://uat.i-want-it.es</info>');
});
