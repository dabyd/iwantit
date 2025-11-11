<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_projects_table.php
    public function up(): void
    {
        Schema::create('versions_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained('users'); // Usuario creador (cliente)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions_roles');
    }
};
