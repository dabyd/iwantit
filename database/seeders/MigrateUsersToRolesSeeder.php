<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MigrateUsersToRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roleMapping = [
            'admin' => 'Admin',
            'super' => 'Supervisor',
            'editor' => 'Editor',
        ];

        $migratedCount = 0;

        User::whereNotNull('role')->chunkById(100, function ($users) use ($roleMapping, &$migratedCount) {
            foreach ($users as $user) {
                if (isset($roleMapping[$user->role])) {
                    $role = Role::where('name', $roleMapping[$user->role])->first();
                    if ($role) {
                        $user->assignRole($role);
                        $migratedCount++;
                        $this->command->info("Assigned role '{$role->name}' to user '{$user->name}' (ID: {$user->id})");
                    }
                }
            }
        });

        $usersWithoutRole = User::whereNull('role')->count();
        $this->command->info("Migration complete. {$migratedCount} users migrated, {$usersWithoutRole} users without role.");
        $this->command->info('Users without role will need to be assigned manually or through another process.');
    }
}
