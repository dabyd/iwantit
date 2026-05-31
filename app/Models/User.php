<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

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
        'client_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    public function enableTwoFactor(string $secret, array $recoveryCodes): void
    {
        $this->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    public function disableTwoFactor(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function recoveryCodes(): array
    {
        return json_decode($this->two_factor_recovery_codes ?? '[]', true);
    }

    public function replaceRecoveryCode(string $used): ?array
    {
        $codes = $this->recoveryCodes();

        $index = array_search($used, $codes);
        if ($index === false) {
            return null;
        }

        unset($codes[$index]);
        $codes = array_values($codes);

        $this->forceFill([
            'two_factor_recovery_codes' => json_encode($codes),
        ])->save();

        return $codes;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects()
    {
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

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'client_id');
    }
}
