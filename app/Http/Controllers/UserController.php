<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan Dashboard User
    public function dashboard()
    {
        return Inertia::render('User/UserDashboard', [
            'user' => Auth::user()
        ]);
    }

    // Menampilkan Riwayat Aktivitas User
    public function history()
    {
        return Inertia::render('User/History', [
            'user' => Auth::user()
        ]);
    }

    // FITUR WHATSAPP
    // GET Index Form
    public function indexWhatsapp()
    {
        $whatsapp = Whatsapp::where('user_id', Auth::id())->first();

        return Inertia::render('User/Whatsapp/Index', [
            'whatsapp' => $whatsapp,
            'user' => Auth::user()
        ]);
    }

    // GET Create Whatsapp
    public function createWhatsapp()
    {
        return Inertia::render('User/Whatsapp/Create', [
            'user' => Auth::user(),
        ]);
    }

    // POST Store Whatsapp
    public function storeWhatsapp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:15|regex:/^[0-9]+$/|unique:whatsapps,phone_number'

        ]);

        try {
            Whatsapp::create([
                'user_id' => Auth::id(),
                'phone_number' => $request->phone_number
            ]);

            return redirect()->route('user.whatsapp')->with('success', 'Nomor WhatsApp berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan nomor WhatsApp.');
        }
    }

    // GET Edit Whatsapp
    public function editWhatsapp()
    {
        $whatsapp = Whatsapp::where('user_id', Auth::id())->first();

        return Inertia::render('User/Whatsapp/Edit', [
            'whatsapp' => $whatsapp,
            'user' => Auth::user()
        ]);
    }

    // PUT Update Whatsapp
    public function updateWhatsapp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:15|regex:/^[0-9]+$/|unique:whatsapps,phone_number,' . Auth::id() . ',user_id'
        ]);

        try {
            $whatsapp = Whatsapp::where('user_id', Auth::id())->first();
            if ($whatsapp) {
                $whatsapp->update([
                    'phone_number' => $request->phone_number
                ]);
            } else {
                Whatsapp::create([
                    'user_id' => Auth::id(),
                    'phone_number' => $request->phone_number
                ]);
            }

            return redirect()->route('user.whatsapp')->with('success', 'Nomor WhatsApp berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui nomor WhatsApp.');
        }
    }

    public function deleteWhatsapp()
    {
        try {
            $whatsapp = Whatsapp::where('user_id', Auth::id())->first();

            if ($whatsapp) {
                $whatsapp->delete();
                return redirect()->route('user.whatsapp')->with('success', 'Nomor WhatsApp berhasil dihapus.');
            }

            return back()->with('error', 'Nomor WhatsApp tidak ditemukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus nomor WhatsApp.');
        }
    }
}
