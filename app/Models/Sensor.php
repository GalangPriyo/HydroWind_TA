<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'sensors';

    protected $fillable = ['device_id', 'name'];

    /**
     * Relasi ke device
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    /**
     * Mendapatkan data sensor terbaru
     */
    public function latestData()
    {
        return $this->hasOne(SensorData::class)->latestOfMany('timestamp');
    }
}
