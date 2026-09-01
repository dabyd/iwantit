<?php

namespace App\Models;

use App\Enums\ValidationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['appearance_id', 'status', 'actor_id', 'reason'];

    protected $casts = [
        'status' => ValidationStatus::class,
    ];

    public function appearance(): BelongsTo
    {
        return $this->belongsTo(Appearance::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
