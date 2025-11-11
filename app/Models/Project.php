<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    use HasFactory;
    protected $fillable = ['name', 'filename', 'territories_id', 'users_id', 'type', 'season', 'episode'];

    public function projects_users() {
        return $this->hasMany(ProjectsUsers::class, 'projects_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'projects_users', 'projects_id', 'users_id');
    }
}
