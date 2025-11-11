<?php

// app/Models/DatisionDetection.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatisionDetection extends Model
{
    protected $fillable = [
        'datision_result_id',
        'frame',
        'x1',
        'y1',
        'x2',
        'y2',
    ];

    protected $casts = [
        'frame' => 'integer',
        'x1' => 'integer',
        'y1' => 'integer',
        'x2' => 'integer',
        'y2' => 'integer',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(DatisionResult::class, 'datision_result_id');
    }

    // Calcular ancho y alto del objeto detectado
    public function getWidthAttribute()
    {
        return $this->x2 - $this->x1;
    }

    public function getHeightAttribute()
    {
        return $this->y2 - $this->y1;
    }

    // Calcular centro del objeto
    public function getCenterXAttribute()
    {
        return ($this->x1 + $this->x2) / 2;
    }

    public function getCenterYAttribute()
    {
        return ($this->y1 + $this->y2) / 2;
    }
}