<?php

namespace App\Models;

use App\Enums\InventoryItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'type', 'brand_id', 'canonical_id', 'created_by'];

    protected $casts = [
        'type' => InventoryItemType::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appearances(): HasMany
    {
        return $this->hasMany(Appearance::class);
    }

    public function taxonAssignments(): MorphMany
    {
        return $this->morphMany(TaxonAssignment::class, 'assignable');
    }

    public function taxons(): MorphToMany
    {
        return $this->morphToMany(Taxon::class, 'assignable', 'taxon_assignments');
    }

    public function relationshipsAsSource(): HasMany
    {
        return $this->hasMany(ContextualRelationship::class, 'source_item_id');
    }

    public function relationshipsAsTarget(): HasMany
    {
        return $this->hasMany(ContextualRelationship::class, 'target_item_id');
    }
}
