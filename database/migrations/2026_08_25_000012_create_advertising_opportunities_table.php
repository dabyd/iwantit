<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertising_opportunities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('appearance_id')->nullable();
            $table->unsignedBigInteger('scene_id')->nullable();
            $table->string('value_level', 50)->default('medium');
            $table->text('rationale')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('appearance_id')
                ->references('id')
                ->on('appearances')
                ->onDelete('set null');

            $table->foreign('scene_id')
                ->references('id')
                ->on('scenes')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['project_id', 'value_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_opportunities');
    }
};
