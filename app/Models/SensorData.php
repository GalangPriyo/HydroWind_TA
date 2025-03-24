<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_datas';

    protected $fillable = ['sensor_id', 'value', 'timestamp'];

    /**
     * Relasi ke model Sensor.
     * Satu data hanya milik satu sensor.
     */
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
