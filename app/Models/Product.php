<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\DatisionObjectsIaClass;   // <<–– IMPORTANTE

class Product extends Model
{
    use HasFactory;

    /** Campos que se pueden rellenar en masa */
    protected $fillable = [
        'name',
        'description',
        'disabled',
        'brands_id',
        'url',
        'filename',
        'icono',
        'auto_open',
    ];

    /**
     * Clases IA asociadas a este producto (tabla pivote).
     */
    public function iaClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            DatisionObjectsIaClass::class,          // modelo relacionado
            'products_datision_objects_ia_classes', // tabla pivote
            'products_id',                          // FK pivote → products.id
            'datision_objects_ia_classes_id'        // FK pivote → ia_classes.id
        )->withTimestamps();
    }
}