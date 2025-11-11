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
        Schema::create('datision_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datision_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('id_object');
            $table->string('class');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datision_results');
    }
};
