<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'file_path',
        'timecode',
        'note',
        'source',
        'provider',
        'model',
        'generated_at',
        'validation_status',
        'created_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function evidenceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
