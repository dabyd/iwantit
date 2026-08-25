<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertising_opportunity_taxons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertising_opportunity_id');
            $table->unsignedBigInteger('taxon_id');
            $table->timestamps();

            $table->foreign('advertising_opportunity_id', 'aot_opportunity_fk')
                ->references('id')
                ->on('advertising_opportunities')
                ->onDelete('cascade');

            $table->foreign('taxon_id', 'aot_taxon_fk')
                ->references('id')
                ->on('taxons')
                ->onDelete('cascade');

            $table->unique(['advertising_opportunity_id', 'taxon_id'], 'aot_opportunity_taxon_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_opportunity_taxons');
    }
};
