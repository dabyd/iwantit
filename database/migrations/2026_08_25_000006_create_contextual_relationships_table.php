<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contextual_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('source_item_id');
            $table->unsignedBigInteger('target_item_id');
            $table->string('relationship_type', 50);
            $table->unsignedBigInteger('scene_id');
            $table->unsignedBigInteger('evidence_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('source_item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');

            $table->foreign('target_item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');

            $table->foreign('scene_id')
                ->references('id')
                ->on('scenes')
                ->onDelete('cascade');

            $table->foreign('evidence_id')
                ->references('id')
                ->on('evidence')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['source_item_id', 'target_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contextual_relationships');
    }
};
