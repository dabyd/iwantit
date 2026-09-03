<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxons', function (Blueprint $table) {
            $table->id();
            $table->string('taxonomy', 50);
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('taxons')
                ->onDelete('set null');

            $table->index(['taxonomy', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxons');
    }
};
