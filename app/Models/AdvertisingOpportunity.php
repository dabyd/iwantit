<?php

namespace App\Models;

use App\Enums\ValueLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdvertisingOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'appearance_id',
        'scene_id',
        'value_level',
        'rationale',
        'created_by',
    ];

    protected $casts = [
        'value_level' => ValueLevel::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function appearance(): BelongsTo
    {
        return $this->belongsTo(Appearance::class);
    }

    public function scene(): BelongsTo
    {
        return $this->belongsTo(Scene::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function elements(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'advertising_opportunity_elements')->withTimestamps();
    }

    public function taxons(): BelongsToMany
    {
        return $this->belongsToMany(Taxon::class, 'advertising_opportunity_taxons')->withTimestamps();
    }
}
