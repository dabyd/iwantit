<?php

namespace App\Models;

use App\Enums\AppearanceSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Appearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'scene_id',
        'start_time',
        'end_time',
        'pos_x',
        'pos_y',
        'w',
        'h',
        'source',
        'provenance',
        'created_by',
    ];

    protected $casts = [
        'source' => AppearanceSource::class,
        'pos_x' => 'decimal:2',
        'pos_y' => 'decimal:2',
        'w' => 'decimal:2',
        'h' => 'decimal:2',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function scene(): BelongsTo
    {
        return $this->belongsTo(Scene::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function relevances(): HasMany
    {
        return $this->hasMany(AppearanceRelevance::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    public function evidence(): MorphMany
    {
        return $this->morphMany(Evidence::class, 'evidenceable');
    }
}
