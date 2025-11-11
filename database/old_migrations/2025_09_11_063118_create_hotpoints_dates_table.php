<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('hotpoints_dates', function (Blueprint $table) {
            $table->integer('project_id');
            $table->integer('product_id');
            $table->integer('id');
            $table->date('date_in');
            $table->date('date_out');
            $table->float('price', 8, 2); // 8 dígitos totales, 2 decimales
            $table->string('url');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            
            // Definir la clave primaria compuesta
            $table->primary(['project_id', 'product_id', 'id']);
            
            // Índices para mejorar el rendimiento
            $table->index('date_in');
            $table->index('date_out');
            $table->index('estado');
            $table->index(['project_id', 'product_id']); // Índice para consultas por proyecto y producto
        });
    }

    /**
     * Reversar las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotpoints_dates');
    }
};