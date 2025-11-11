<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatisionParameters extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'datision_parameters';

    /**
     * Los campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'machine_url',
        'threshold_sec',
        'frames',
        'x1',
        'y1',
        'low_price',
        'medium_price',
        'high_price',
        'extra_price',
    ];

    /**
     * Los campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'threshold_sec' => 'integer',
        'frames' => 'integer',
        'x1' => 'integer',
        'y1' => 'integer',
        'low_price' => 'float',
        'medium_price' => 'float',
        'high_price' => 'float',
        'extra_price' => 'float',
    ];

    // Scopes útiles
    /**
     * Scope para obtener parámetros con precios definidos
     */
    public function scopeWithPrices($query)
    {
        return $query->whereNotNull('low_price')
                    ->whereNotNull('medium_price')
                    ->whereNotNull('high_price');
    }

    /**
     * Scope para obtener parámetros con coordenadas definidas
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('x1')->whereNotNull('y1');
    }

    /**
     * Scope para obtener por URL de máquina
     */
    public function scopeByMachineUrl($query, $url)
    {
        return $query->where('machine_url', $url);
    }

    // Accessors y Mutators
    /**
     * Accessor para obtener el threshold en minutos
     */
    public function getThresholdMinutesAttribute()
    {
        return round($this->threshold_sec / 60, 2);
    }

    /**
     * Accessor para verificar si tiene coordenadas definidas
     */
    public function getHasCoordinatesAttribute()
    {
        return !is_null($this->x1) && !is_null($this->y1);
    }

    /**
     * Accessor para verificar si tiene frames definidos
     */
    public function getHasFramesAttribute()
    {
        return !is_null($this->frames) && $this->frames > 0;
    }

    /**
     * Accessor para obtener todos los precios formateados
     */
    public function getPricesFormattedAttribute()
    {
        return [
            'low_price' => $this->low_price ? number_format($this->low_price, 2) : null,
            'medium_price' => $this->medium_price ? number_format($this->medium_price, 2) : null,
            'high_price' => $this->high_price ? number_format($this->high_price, 2) : null,
            'extra_price' => $this->extra_price ? number_format($this->extra_price, 2) : null,
        ];
    }

    /**
     * Accessor para obtener las coordenadas como array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->has_coordinates) {
            return [
                'x1' => $this->x1,
                'y1' => $this->y1,
            ];
        }
        return null;
    }

    // Métodos personalizados
    /**
     * Obtener el precio según el tipo especificado
     */
    public function getPriceByType($type)
    {
        $priceField = $type . '_price';
        
        if (in_array($priceField, ['low_price', 'medium_price', 'high_price', 'extra_price'])) {
            return $this->{$priceField};
        }
        
        return null;
    }

    /**
     * Verificar si la URL de la máquina es válida
     */
    public function isValidMachineUrl()
    {
        return filter_var($this->machine_url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Obtener el dominio de la URL de la máquina
     */
    public function getMachineDomain()
    {
        if ($this->isValidMachineUrl()) {
            return parse_url($this->machine_url, PHP_URL_HOST);
        }
        return null;
    }
}