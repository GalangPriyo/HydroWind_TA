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

    /**
     * Mendapatkan label nama sensor dalam bahasa Indonesia
     */
    public function getNameLabelAttribute()
    {
        $labels = [
            'curah_hujan' => 'Curah Hujan',
            'ketinggian_air' => 'Ketinggian Air',
            'kecepatan_angin' => 'Kecepatan Angin',
            'arah_angin' => 'Arah Angin',
            'tekanan_udara' => 'Tekanan Udara'
        ];

        return $labels[$this->name] ?? $this->name;
    }

    /**
     * Mendapatkan satuan sensor
     */
    public function getUnitAttribute()
    {
        $units = [
            'curah_hujan' => 'mm',
            'ketinggian_air' => 'm',
            'kecepatan_angin' => 'm/s',
            'arah_angin' => '°',
            'tekanan_udara' => 'hPa'
        ];

        return $units[$this->name] ?? '';
    }
}
