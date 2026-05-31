<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->boolean('can_manage_all_users')->default(false)->after('guard_name');
            $table->boolean('can_manage_own_users')->default(true)->after('can_manage_all_users');
        });

        DB::table('roles')->insert([
            ['name' => 'Admin', 'description' => 'Acceso total al sistema', 'guard_name' => 'web', 'can_manage_all_users' => true, 'can_manage_own_users' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'description' => 'Acceso a proyectos asignados con gestión', 'guard_name' => 'web', 'can_manage_all_users' => false, 'can_manage_own_users' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Editor', 'description' => 'Acceso a proyectos asignados con creación', 'guard_name' => 'web', 'can_manage_all_users' => false, 'can_manage_own_users' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'can_manage_all_users', 'can_manage_own_users']);
        });
    }
};
