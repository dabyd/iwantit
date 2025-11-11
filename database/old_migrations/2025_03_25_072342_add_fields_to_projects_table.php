<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProjectsTable extends Migration {
    public function up() {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default('Film'); // or 'Serie'
            $table->integer('season')->nullable();
            $table->integer('episode')->nullable();
        });
    }

    public function down() {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['type', 'season', 'episode']);
        });
    }
}
