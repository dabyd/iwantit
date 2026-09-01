<?php

namespace App\Models;

use App\Enums\Vertical;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppearanceRelevance extends Model
{
    use HasFactory;

    protected $fillable = ['appearance_id', 'vertical', 'created_by'];

    protected $casts = [
        'vertical' => Vertical::class,
    ];

    public function appearance(): BelongsTo
    {
        return $this->belongsTo(Appearance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
