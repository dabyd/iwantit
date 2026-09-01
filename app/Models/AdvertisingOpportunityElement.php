<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisingOpportunityElement extends Model
{
    use HasFactory;

    protected $fillable = ['advertising_opportunity_id', 'inventory_item_id'];

    public function advertisingOpportunity(): BelongsTo
    {
        return $this->belongsTo(AdvertisingOpportunity::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
