<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'name', 'unit'];

    /**
     * Relasi ke model Device.
     * Satu sensor hanya dimiliki oleh satu device.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relasi ke model SensorData.
     * Satu sensor dapat memiliki banyak data.
     */
    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }
}
