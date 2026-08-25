<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxon_id');
            $table->morphs('assignable');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('taxon_id')
                ->references('id')
                ->on('taxons')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unique(['taxon_id', 'assignable_type', 'assignable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_assignments');
    }
};
