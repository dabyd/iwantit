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
set('default_stage', 'uat');

// Usar composer del sistema (evitar descargarlo cada vez)
set('bin/composer', 'composer');

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

// Configuración de permisos (usar chmod en lugar de ACL, con sudo)
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0775');
set('writable_use_sudo', true);
set('http_user', 'www-data');

// Composer sin dependencias de desarrollo
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// Allow anonymous stats (optional)
set('allow_anonymous_stats', false);

// Hosts (Deployer 6.x syntax)
host('uat')
    ->hostname('uat.i-want-it.es')
    ->user('ubuntu')
    ->set('deploy_path', '/var/www2/iwantit')
    ->set('php_fpm_service', 'php8.4-fpm')
    ->stage('uat');

host('production')
    ->hostname('platform.i-want-it.es')
    ->user('ubuntu')
    ->set('deploy_path', '/var/www/iwantit')
    ->set('php_fpm_service', 'php8.3-fpm')
    ->stage('production');

// Sobrescribir artisan:view:cache para evitar errores de componentes Blade
desc('Skip view cache - views compiled on-demand');
task('artisan:view:cache', function () {
    writeln('<comment>Skipping view:cache - views will compile on-demand</comment>');
});

// Sobrescribir artisan:route:cache para evitar errores de rutas duplicadas
desc('Skip route cache - routes loaded on-demand');
task('artisan:route:cache', function () {
    writeln('<comment>Skipping route:cache - routes will load on-demand</comment>');
});

// Tasks

// Task para limpiar cachés de Laravel
desc('Clear all Laravel caches');
task('artisan:cache:clear:all', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
    run('{{bin/php}} {{release_path}}/artisan config:clear');
    run('{{bin/php}} {{release_path}}/artisan route:clear');
    run('{{bin/php}} {{release_path}}/artisan view:clear');
});

// Task para optimizar Laravel (solo config:cache, sin routes ni views)
desc('Optimize Laravel');
task('artisan:optimize', function () {
    run('{{bin/php}} {{release_path}}/artisan config:cache');
    // route:cache omitido - hay rutas duplicadas que causan error
    // view:cache omitido - las vistas se compilan on-demand
});

// Task para reiniciar PHP-FPM
desc('Restart PHP-FPM');
task('php-fpm:restart', function () {
    run('sudo systemctl restart php8.4-fpm');
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

// Task para corregir permisos del release (al final del deploy)
desc('Fix release directory permissions');
task('deploy:fix-permissions', function () {
    // Cambiar grupo a www-data pero mantener ubuntu como propietario
    // Así ubuntu puede borrar en cleanup y www-data puede leer/escribir
    run('sudo chown -R ubuntu:www-data {{release_path}}');
    run('sudo chmod -R 775 {{release_path}}');
    writeln('<info>✅ Permisos corregidos (ubuntu:www-data)</info>');
});

// Task para limpiar releases antiguas con sudo (porque pueden tener permisos restrictivos)
desc('Cleanup old releases with sudo');
task('deploy:cleanup:sudo', function () {
    $releases = get('releases_list');
    $keep = get('keep_releases');
    
    if ($keep > 0) {
        $releasesToDelete = array_slice($releases, $keep);
        foreach ($releasesToDelete as $release) {
            run("sudo rm -rf {{deploy_path}}/releases/$release");
        }
    }
    
    writeln('<info>✅ Releases antiguas limpiadas</info>');
});

// Task para ejecutar migraciones
desc('Run database migrations');
task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate --force');
});

// Hooks - añadir tareas al flujo de deploy
// Reiniciar servicios después del symlink
after('deploy:symlink', 'services:restart');

// Ejecutar fix-permissions después de reiniciar servicios
after('services:restart', 'deploy:fix-permissions');

// Usar cleanup con sudo en lugar del cleanup normal
after('deploy:fix-permissions', 'deploy:cleanup:sudo');

// Deshabilitar el cleanup normal de deployer (lo hacemos nosotros con sudo)
task('deploy:cleanup', function () {
    writeln('<comment>Cleanup handled by deploy:cleanup:sudo</comment>');
});

// Hooks
after('deploy:failed', 'deploy:unlock');

// Información tras el deploy
after('deploy', 'deploy:success');

desc('Deploy completed successfully');
task('deploy:success', function () {
    $stage = get('stage', 'uat');
    $url = $stage === 'production' ? 'https://platform.i-want-it.es' : 'https://uat.i-want-it.es';
    writeln('<info>✅ Deploy completed successfully!</info>');
    writeln("<info>🌐 URL: $url</info>");
});
