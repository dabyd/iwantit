<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DatisionObjectsIaClass extends Model
{
    protected $table   = 'datision_objects_ia_classes';   // nombre real de la tabla
    public    $timestamps = true;                // usa created_at / updated_at

    protected $guarded = [];                     // o $fillable = ['name'];

    /**
     * Productos que tienen asociada esta clase IA.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'products_datision_objects_ia_classes',
            'datision_objects_ia_classes_id',
            'products_id'
        )->withTimestamps();
    }
}