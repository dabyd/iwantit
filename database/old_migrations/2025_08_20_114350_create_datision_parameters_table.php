<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datision_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('machine_url', 2048); // supports long URLs
            $table->unsignedInteger('threshold_sec');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datision_parameters');
    }
};