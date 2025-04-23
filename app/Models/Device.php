<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['identifier', 'mac_address', 'name', 'location', 'latitude', 'longitude', 'token'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($device) {
            $device->token = Str::random(32); // Generate token otomatis

            // Generate identifier otomatis jika belum diisi
            if (empty($device->identifier)) {
                $device->identifier = strtoupper(Str::uuid()); // Contoh: UUID
            }
        });
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}
