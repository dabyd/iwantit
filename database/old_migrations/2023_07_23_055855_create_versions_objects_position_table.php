<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions_objects_position', function (Blueprint $table) {
            $table->id();
            $table->bigInteger( 'versions_objects_id' );
            $table->time( 'time' );
            $table->float('position_x');
            $table->float('position_y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('versions_objects_position');
    }
};
