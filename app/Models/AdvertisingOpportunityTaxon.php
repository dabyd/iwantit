<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisingOpportunityTaxon extends Model
{
    use HasFactory;

    protected $fillable = ['advertising_opportunity_id', 'taxon_id'];

    public function advertisingOpportunity(): BelongsTo
    {
        return $this->belongsTo(AdvertisingOpportunity::class);
    }

    public function taxon(): BelongsTo
    {
        return $this->belongsTo(Taxon::class);
    }
}
