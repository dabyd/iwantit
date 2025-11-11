<?php

// app/Models/DatisionResult.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatisionResult extends Model
{
    protected $fillable = [
        'datision_id',
        'id_object',
        'class',
    ];

    public function datision(): BelongsTo
    {
        return $this->belongsTo(Datision::class);
    }

    public function detections(): HasMany
    {
//        return $this->hasMany(DatisionDetection::class);
        return $this->hasMany(DatisionDetection::class, 'datision_result_id');
    }

    public function detectionsWithFrameInfo()
    {
        return $this->detections()
            ->orderBy('frame')
            ->get()
            ->map(function ($detection) {
                return [
                    'id' => $detection->id,
                    'frame' => $detection->frame,
                    'x1' => $detection->x1,
                    'y1' => $detection->y1,
                    'x2' => $detection->x2,
                    'y2' => $detection->y2,
                    'width' => $detection->x2 - $detection->x1,
                    'height' => $detection->y2 - $detection->y1,
                ];
            });
    }
}
