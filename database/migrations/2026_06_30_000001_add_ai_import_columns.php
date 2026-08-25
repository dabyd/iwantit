<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotpoints', function (Blueprint $table) {
            $table->boolean('is_ai_imported')->default(false);
            $table->timestamp('ai_imported_at')->nullable();
            $table->unsignedBigInteger('datision_detection_id')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_ai_generated')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('hotpoints', function (Blueprint $table) {
            $table->dropColumn(['is_ai_imported', 'ai_imported_at', 'datision_detection_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_ai_generated');
        });
    }
};
