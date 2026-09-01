<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaxonAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['taxon_id', 'assignable_type', 'assignable_id', 'created_by'];

    public function taxon(): BelongsTo
    {
        return $this->belongsTo(Taxon::class);
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
