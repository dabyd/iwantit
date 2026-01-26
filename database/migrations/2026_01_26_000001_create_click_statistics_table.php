<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('click_statistics', function (Blueprint $table) {
            $table->id();
            
            // Tipo de evento: 'view' (visualización API) o 'click' (clic en producto/marca)
            $table->enum('type', ['view', 'click'])->default('view');
            
            // Referencias al proyecto y contenido
            $table->unsignedBigInteger('versions_id')->nullable();
            $table->unsignedBigInteger('products_id')->nullable();
            $table->unsignedBigInteger('brands_id')->nullable();
            
            // Tiempo del vídeo cuando se hizo la petición
            $table->decimal('video_time', 10, 4)->nullable();
            
            // Información del cliente
            $table->string('ip_address', 45)->nullable(); // Soporta IPv6
            $table->text('user_agent')->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('device', 100)->nullable(); // desktop, mobile, tablet
            
            // Información adicional
            $table->text('referer')->nullable();
            $table->string('license_key', 255)->nullable(); // Key usada para la petición
            
            $table->timestamps();
            
            // Índices para consultas frecuentes
            $table->index('type');
            $table->index('versions_id');
            $table->index('products_id');
            $table->index('brands_id');
            $table->index('created_at');
            $table->index(['versions_id', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_statistics');
    }
};
