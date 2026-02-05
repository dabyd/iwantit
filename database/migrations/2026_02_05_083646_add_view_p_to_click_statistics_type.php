<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE click_statistics MODIFY COLUMN type ENUM('view', 'click', 'view_p') NOT NULL DEFAULT 'view'");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE click_statistics MODIFY COLUMN type ENUM('view', 'click') NOT NULL DEFAULT 'view'");
    }

};
