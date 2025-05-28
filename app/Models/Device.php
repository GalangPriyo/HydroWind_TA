<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'latitude', 'longitude', 'node_id', 'token', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($device) {
            // Jika token kosong, generate token
            if (empty($device->token)) {
                $device->token = Str::random(32);
            }

            // Generate node_id jika belum diisi
            if (empty($device->node_id)) {
                // Cari node_id terbesar yang ada
                $lastDevice = self::orderBy('id', 'desc')->first();
                $lastNumber = 0;

                if ($lastDevice) {
                    // Ekstrak nomor dari node_id terakhir (NODE-001 -> 1)
                    preg_match('/NODE-(\d+)/', $lastDevice->node_id, $matches);
                    if (isset($matches[1])) {
                        $lastNumber = (int)$matches[1];
                    }
                }

                // Buat node_id baru dengan format NODE-001, NODE-002, dll
                $newNumber = $lastNumber + 1;
                $device->node_id = 'NODE-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

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
