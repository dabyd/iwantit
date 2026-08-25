<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertising_opportunity_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertising_opportunity_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->timestamps();

            $table->foreign('advertising_opportunity_id', 'aoe_opportunity_fk')
                ->references('id')
                ->on('advertising_opportunities')
                ->onDelete('cascade');

            $table->foreign('inventory_item_id', 'aoe_item_fk')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');

            $table->unique(['advertising_opportunity_id', 'inventory_item_id'], 'aoe_opportunity_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_opportunity_elements');
    }
};
