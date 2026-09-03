<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analysis_run_id');
            $table->string('class', 100);
            $table->unsignedBigInteger('start_time');
            $table->unsignedBigInteger('end_time');
            $table->decimal('pos_x', 5, 2)->default(0);
            $table->decimal('pos_y', 5, 2)->default(0);
            $table->decimal('w', 5, 2)->default(0);
            $table->decimal('h', 5, 2)->default(0);
            $table->float('confidence')->nullable();
            $table->string('status', 50)->default('pending');
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('analysis_run_id')
                ->references('id')
                ->on('analysis_runs')
                ->onDelete('cascade');

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['analysis_run_id', 'class']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_candidates');
    }
};
