<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Threshold extends Model
{
    use HasFactory;

    protected $fillable = ['sensor_id', 'waspada', 'bahaya'];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
