<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectPermissionHelper
{
    private static $levels = [
        'none' => 0,
        'read' => 1,
        'write' => 2,
        'create' => 3,
    ];

    public static function canAccess(User $user, Project $project, string $level): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($project->users_id == $user->id) {
            return true;
        }

        $permission = DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if (! $permission) {
            return false;
        }

        $userLevel = self::$levels[$permission->access_level] ?? 0;
        $requiredLevel = self::$levels[$level] ?? 99;

        return $userLevel >= $requiredLevel;
    }

    public static function canView(User $user, Project $project): bool
    {
        return self::canAccess($user, $project, 'read');
    }

    public static function canEdit(User $user, Project $project): bool
    {
        return self::canAccess($user, $project, 'write');
    }

    public static function canCreate(User $user, Project $project): bool
    {
        return self::canAccess($user, $project, 'create');
    }

    public static function getAccessLevel(User $user, Project $project): ?string
    {
        if ($project->users_id == $user->id) {
            return 'create';
        }

        return DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->value('access_level');
    }

    public static function setAccessLevel(User $user, Project $project, string $level): void
    {
        if (! isset(self::$levels[$level])) {
            throw new \InvalidArgumentException("Invalid access level: {$level}");
        }

        DB::table('project_user_permissions')->updateOrInsert(
            ['user_id' => $user->id, 'project_id' => $project->id],
            ['access_level' => $level, 'updated_at' => now()]
        );
    }

    public static function removeAccess(User $user, Project $project): void
    {
        DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->delete();
    }

    public static function getProjectsByAccessLevel(User $user, string $level): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->hasRole('Admin')) {
            return Project::all();
        }

        $projectIds = DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('access_level', '!=', 'none')
            ->pluck('project_id');

        return Project::whereIn('id', $projectIds)->get();
    }
}
