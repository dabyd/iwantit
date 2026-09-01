<?php

namespace App\Models;

use App\Enums\Taxonomy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxon extends Model
{
    use HasFactory;

    protected $table = 'taxons';

    protected $fillable = ['taxonomy', 'name', 'parent_id'];

    protected $casts = [
        'taxonomy' => Taxonomy::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxon::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxon::class, 'parent_id');
    }

    public function taxonAssignments(): HasMany
    {
        return $this->hasMany(TaxonAssignment::class);
    }
}
