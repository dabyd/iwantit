<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appearance_relevances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appearance_id');
            $table->string('vertical', 50);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('appearance_id')
                ->references('id')
                ->on('appearances')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unique(['appearance_id', 'vertical']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appearance_relevances');
    }
};
