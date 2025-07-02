<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Whatsapp;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan Dashboard User
    public function dashboard()
    {
        $whatsapp = Whatsapp::where('user_id', Auth::id())->first();

        return Inertia::render('User/UserDashboard', [
            'user' => Auth::user(),
            'whatsapp' => $whatsapp,
        ]);
    }
}
