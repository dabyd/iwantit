<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncProjectPermissions extends Command
{
    protected $signature = 'app:sync-project-permissions';

    protected $description = 'Sync projects_users to project_user_permissions';

    public function handle(): int
    {
        DB::table('project_user_permissions')->truncate();

        $links = DB::table('projects_users')
            ->join('users', 'projects_users.users_id', '=', 'users.id')
            ->where('users.role', '!=', 'admin')
            ->select('projects_users.users_id', 'projects_users.projects_id', 'projects_users.as_owner')
            ->get();

        $count = 0;
        foreach ($links as $link) {
            $accessLevel = match ($link->as_owner) {
                'S' => 'write',
                default => 'read',
            };

            DB::table('project_user_permissions')->insert([
                'user_id' => $link->users_id,
                'project_id' => $link->projects_id,
                'access_level' => $accessLevel,
            ]);
            $count++;
        }

        $this->info("Synced {$count} project permissions.");

        return Command::SUCCESS;
    }
}
