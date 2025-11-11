<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectsUsers extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'projects_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function option()
    {
        return $this->belongsTo(Options::class);
    }
}