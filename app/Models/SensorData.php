<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_datas';

    protected $fillable = ['sensor_id', 'value', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime',
        'value' => 'float',
    ];

    /**
     * Relasi ke sensor
     */
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    /**
     * Relasi langsung ke device melalui sensor
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id', 'sensors');
    }

    /**
     * Membuat data sensor baru dari perangkat
     */
    public static function createFromDevice($nodeId, $sensorName, $value)
    {
        $device = Device::where('node_id', $nodeId)->first();

        if (!$device) {
            return false;
        }

        $sensor = $device->sensors()->where('name', $sensorName)->first();

        if (!$sensor) {
            return false;
        }

        return self::create([
            'sensor_id' => $sensor->id,
            'value' => $value,
            'timestamp' => now(),
        ]);
    }
}
