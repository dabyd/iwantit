<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatisionParameter extends Model
{
    // 👇 permite asignación masiva en estos campos
    protected $fillable = [
        'machine_url',
        'threshold_sec',
    ];
}