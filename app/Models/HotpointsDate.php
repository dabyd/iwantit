<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HotpointsDate extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'hotpoints_dates';

    /**
     * Clave primaria compuesta
     */
    protected $primaryKey = ['project_id', 'product_id', 'id'];
    
    /**
     * Indica que la clave primaria no es auto-incremental
     */
    public $incrementing = false;

    /**
     * Los campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'project_id',
        'product_id',
        'id',
        'date_in',
        'date_out',
        'price',
        'url',
        'estado',
    ];

    /**
     * Los campos que deben ser tratados como fechas
     */
    protected $dates = [
        'date_in',
        'date_out',
        'created_at',
        'updated_at',
    ];

    /**
     * Los campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'project_id' => 'integer',
        'product_id' => 'integer',
        'id' => 'integer',
        'date_in' => 'date',
        'date_out' => 'date',
        'price' => 'float',
        'estado' => 'boolean',
    ];

    /**
     * Método para obtener la clave primaria completa del modelo
     */
    public function getKey()
    {
        $key = [];
        foreach ($this->primaryKey as $keyName) {
            $key[$keyName] = $this->getAttribute($keyName);
        }
        return $key;
    }

    /**
     * Método para establecer la clave primaria del modelo
     */
    public function setKeysForSaveQuery($query)
    {
        foreach ($this->primaryKey as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    /**
     * Método estático para actualizar o crear un registro con clave primaria compuesta
     */
    public static function updateOrCreateByKeys($project_id, $product_id, $id, $data)
    {
        return static::updateOrCreate(
            [
                'project_id' => $project_id,
                'product_id' => $product_id,
                'id'         => $id,                
            ],
            $data
        );
    }

    // Scopes útiles
    /**
     * Scope para obtener solo registros activos
     */
    public function scopeActive($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para obtener registros por rango de fechas
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_in', [$startDate, $endDate]);
    }

    /**
     * Scope para obtener registros disponibles (fecha de entrada en el futuro)
     */
    public function scopeAvailable($query)
    {
        return $query->where('date_in', '>=', now()->toDateString());
    }

    // Accessors y Mutators
    /**
     * Accessor para formatear el precio con 2 decimales
     */
    public function getPriceFormattedAttribute(float $seconds = 0, $decimals = 2)
    {
        return number_format($this->price * $seconds, $decimals);
    }

    public function getPriceRawRounded()
    {
        return ceil($this->price);
    }

    /**
     * Accessor para obtener la duración en días
     */
    public function getDurationAttribute()
    {
        if ($this->date_in && $this->date_out) {
            return Carbon::parse($this->date_in)->diffInDays(Carbon::parse($this->date_out));
        }
        return 0;
    }

    /**
     * Accessor para obtener el estado en texto
     */
    public function getEstadoTextAttribute()
    {
        return $this->estado ? 'Enabled' : 'Disabled';
    }

    /**
     * Mutator para convertir fechas en formato dd/mm/yyyy a Y-m-d
     */
    public function setDateInAttribute($value)
    {
        if ($value && $value !== '---') {
            // Si viene en formato dd/mm/yyyy, convertir a Y-m-d
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                $this->attributes['date_in'] = $date ? $date->format('Y-m-d') : null;
            } else {
                $this->attributes['date_in'] = $value;
            }
        } else {
            $this->attributes['date_in'] = null;
        }
    }

    /**
     * Mutator para convertir fechas en formato dd/mm/yyyy a Y-m-d
     */
    public function setDateOutAttribute($value)
    {
        if ($value && $value !== '---') {
            // Si viene en formato dd/mm/yyyy, convertir a Y-m-d
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                $this->attributes['date_out'] = $date ? $date->format('Y-m-d') : null;
            } else {
                $this->attributes['date_out'] = $value;
            }
        } else {
            $this->attributes['date_out'] = null;
        }
    }

    // Métodos personalizados para formatear fechas
    /**
     * Obtener date_in formateada como dd/mm/aaaa
     */
    public function get_date_in()
    {
        return $this->date_in ? $this->date_in->format('d/m/Y') : null;
    }

    /**
     * Obtener date_out formateada como dd/mm/aaaa
     */
    public function get_date_out()
    {
        return $this->date_out ? $this->date_out->format('d/m/Y') : null;
    }
}