<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_import_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->json('previous_editor_json')->nullable();
            $table->json('created_product_ids')->nullable();
            $table->json('created_brand_ids')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });

        Schema::table('hotpoints', function (Blueprint $table) {
            $table->foreign('ai_import_batch_id')
                ->references('id')
                ->on('ai_import_batches')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_import_batches');
    }
};
