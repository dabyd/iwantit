<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'client_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects() {
        if ($this->role === 'admin') {
            return Project::query(); // admin accede a todos
        }

        if ($this->role === 'super') {
            return $this->hasMany(Project::class);
        }

        if ($this->role === 'editor') {
            return Project::whereHas('permissions', function ($query) {
                $query->where('user_id', $this->id);
            });
        }
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function users() {
        return $this->hasMany(User::class, 'client_id');
    }
}
