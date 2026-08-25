<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotpoints', function (Blueprint $table) {
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('datision_result_id')->nullable();
            $table->unsignedBigInteger('ai_import_batch_id')->nullable();

            $table->foreign('datision_result_id')
                ->references('id')
                ->on('datision_results')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hotpoints', function (Blueprint $table) {
            $table->dropForeign(['datision_result_id']);
            $table->dropColumn(['status', 'datision_result_id', 'ai_import_batch_id']);
        });
    }
};
