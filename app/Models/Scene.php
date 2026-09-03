<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Scene extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'position', 'start_time', 'end_time', 'name'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function appearances(): HasMany
    {
        return $this->hasMany(Appearance::class);
    }

    public function contextualRelationships(): HasMany
    {
        return $this->hasMany(ContextualRelationship::class);
    }

    public function taxonAssignments(): MorphMany
    {
        return $this->morphMany(TaxonAssignment::class, 'assignable');
    }

    public function taxons(): MorphToMany
    {
        return $this->morphToMany(Taxon::class, 'assignable', 'taxon_assignments');
    }
}
