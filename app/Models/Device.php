<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'latitude', 'longitude', 'token'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($device) {
            $device->token = Str::random(32); // Generate token otomatis
        });
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}
