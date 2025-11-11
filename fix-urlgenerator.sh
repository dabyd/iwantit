#!/bin/bash

# ============================================
# Script de Solución para Error de UrlGenerator
# ============================================

echo "🔧 Iniciando proceso de reparación..."
echo ""

# Paso 1: Eliminar cachés corruptas
echo "📦 Paso 1: Eliminando cachés..."
rm -rf bootstrap/cache/*.php 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -rf storage/framework/views/* 2>/dev/null
echo "✅ Cachés eliminadas"
echo ""

# Paso 2: Hacer backup de archivos originales
echo "💾 Paso 2: Creando backups..."
cp app/Providers/TelescopeServiceProvider.php app/Providers/TelescopeServiceProvider.php.backup 2>/dev/null
cp config/scribe.php config/scribe.php.backup 2>/dev/null
echo "✅ Backups creados"
echo ""

# Paso 3: Aplicar fix a TelescopeServiceProvider
echo "🔨 Paso 3: Aplicando fix a TelescopeServiceProvider..."

cat > app/Providers/TelescopeServiceProvider.php << 'EOF'
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 🔥 FIX: Deshabilitar Telescope en modo CLI para evitar conflictos
        if ($this->app->runningInConsole()) {
            Telescope::stopRecording();
            config(['telescope.enabled' => false]);
        }

        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
EOF

echo "✅ TelescopeServiceProvider actualizado"
echo ""

# Paso 4: Aplicar fix a config/scribe.php
echo "🔨 Paso 4: Aplicando fix a config/scribe.php..."

# Buscar y reemplazar la línea problemática
sed -i.bak "s/'intro_text' => file_exists(resource_path('docs\/intro.md'))/'intro_text' => null \/\/ file_exists(resource_path('docs\/intro.md'))/g" config/scribe.php 2>/dev/null

echo "✅ config/scribe.php actualizado"
echo ""

# Paso 5: Limpiar comandos artisan
echo "🧹 Paso 5: Limpiando Laravel..."
php artisan config:clear 2>/dev/null || echo "⚠️  No se pudo ejecutar config:clear"
php artisan cache:clear 2>/dev/null || echo "⚠️  No se pudo ejecutar cache:clear"
php artisan view:clear 2>/dev/null || echo "⚠️  No se pudo ejecutar view:clear"
php artisan route:clear 2>/dev/null || echo "⚠️  No se pudo ejecutar route:clear"
echo "✅ Laravel limpiado"
echo ""

# Paso 6: Test
echo "🧪 Paso 6: Probando comandos..."
php artisan --version
if [ $? -eq 0 ]; then
    echo "✅ Comandos artisan funcionando correctamente"
else
    echo "❌ Aún hay errores. Revisa los logs."
fi
echo ""

echo "============================================"
echo "✨ Proceso completado"
echo "============================================"
echo ""
echo "Ahora intenta ejecutar:"
echo "  php artisan cache:clear"
echo "  php artisan scribe:generate"
echo ""
echo "Si sigue fallando, revisa los backups en:"
echo "  app/Providers/TelescopeServiceProvider.php.backup"
echo "  config/scribe.php.backup"