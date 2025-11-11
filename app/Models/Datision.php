<?php

// app/Models/Datision.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Datision extends Model
{
    protected $fillable = [
        'id_project',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(DatisionResult::class);
    }

    public function detections()
    {
        return $this->hasManyThrough(DatisionDetection::class, DatisionResult::class);
    }

    // Obtener objetos agrupados por clase para este proyecto
    public function getGroupedObjects()
    {
        return $this->results()
            ->select('class', \DB::raw('COUNT(*) as count'))
            ->groupBy('class')
            ->get();
    }
}