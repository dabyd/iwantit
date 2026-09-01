<?php

namespace App\Models;

use App\Enums\DetectionCandidateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_run_id',
        'class',
        'start_time',
        'end_time',
        'pos_x',
        'pos_y',
        'w',
        'h',
        'confidence',
        'status',
        'inventory_item_id',
        'created_by',
    ];

    protected $casts = [
        'status' => DetectionCandidateStatus::class,
        'confidence' => 'float',
        'pos_x' => 'decimal:2',
        'pos_y' => 'decimal:2',
        'w' => 'decimal:2',
        'h' => 'decimal:2',
    ];

    public function analysisRun(): BelongsTo
    {
        return $this->belongsTo(AnalysisRun::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
