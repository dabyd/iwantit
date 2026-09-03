<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appearance_id');
            $table->string('status', 50)->default('unvalidated');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('appearance_id')
                ->references('id')
                ->on('appearances')
                ->onDelete('cascade');

            $table->foreign('actor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index('appearance_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
