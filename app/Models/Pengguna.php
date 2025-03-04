<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'users'; // Menggunakan tabel 'users'

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    public function whatsapp()
    {
        return $this->hasOne(Whatsapp::class, 'user_id');
    }
}
