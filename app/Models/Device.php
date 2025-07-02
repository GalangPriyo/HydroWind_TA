<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = ['name', 'location', 'latitude', 'longitude', 'node_id', 'status'];

    /**
     * Relasi ke sensor
     */
    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    /**
     * Relasi ke semua data sensor melalui relasi sensor
     */
    public function sensorData()
    {
        return $this->hasManyThrough(SensorData::class, Sensor::class);
    }

    public function latestBattery()
    {
        return $this->hasOne(Battery::class)->latestOfMany();
    }
}
