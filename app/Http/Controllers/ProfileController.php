<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function indexProfile()
    {
        $user = auth()->user()->load('whatsapp'); // pastikan relasi 'whatsapp' dimuat
        return Inertia::render('Profile', [
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'different:current_password'],
            'confirm_password' => ['same:new_password'],
        ]);

        // Tambahan: pastikan password baru juga tidak sama dengan yang di-hash sekarang (lebih aman)
        if (Hash::check($request->new_password, $request->user()->password)) {
            return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function destroyProfile(Request $request)
    {
        $user = $request->user();
        $user->delete();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}
