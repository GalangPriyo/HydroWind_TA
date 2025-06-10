<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'name'];

    /**
     * Nilai sensor yang valid
     */
    public static $validSensorTypes = [
        'curah_hujan',
        'ketinggian_air',
        'kecepatan_angin',
        'arah_angin',
        'tekanan_udara'
    ];

    /**
     * Relasi ke device
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relasi ke data sensor
     */
    public function data()
    {
        return $this->hasMany(SensorData::class);
    }

    /**
     * Mendapatkan data sensor terbaru
     */
    public function getLatestDataAttribute()
    {
        return $this->data()->latest('timestamp')->first();
    }

    public function latestData()
    {
        return $this->hasOne(SensorData::class)->latestOfMany('timestamp');
    }
}
