<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appearances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('scene_id');
            $table->unsignedBigInteger('start_time');
            $table->unsignedBigInteger('end_time');
            $table->decimal('pos_x', 5, 2)->default(0);
            $table->decimal('pos_y', 5, 2)->default(0);
            $table->decimal('w', 5, 2)->default(0);
            $table->decimal('h', 5, 2)->default(0);
            $table->string('source', 50)->default('manual');
            $table->string('provenance')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');

            $table->foreign('scene_id')
                ->references('id')
                ->on('scenes')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['scene_id', 'start_time']);
            $table->index(['inventory_item_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appearances');
    }
};
