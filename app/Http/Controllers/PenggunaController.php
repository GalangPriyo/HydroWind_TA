<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Pengguna;
use App\Models\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class PenggunaController extends Controller
{
    // GET Index Form
    public function indexPengguna(Request $request)
    {
        $query = Pengguna::where('role', 'user')
            ->with('whatsapp')
            ->latest();

        if ($request->has('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('whatsapp', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(phone_number) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        $users = $query->paginate(10)->withQueryString(); // Tetap pakai pagination

        return Inertia::render('Admin/DaftarPengguna/Index', [
            'user' => Auth::user(),
            'users' => $users,
            'search' => $request->search ?? '',
            'stats' => [
                'totalUsers' => User::where('role', 'user')->count(),
                'usersWithWhatsapp' => User::whereHas('whatsapp')->count(),
            ],
        ]);
    }


    public function downloadPengguna()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Nomor WA');

        // Ambil data
        $users = \App\Models\User::with('whatsapp')->get();

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->name);
            $sheet->setCellValue('B' . $row, $user->email);
            $sheet->setCellValue('C' . $row, $user->whatsapp->phone_number ?? '-');
            $row++;
        }

        // Simpan ke file sementara
        $fileName = 'daftar_pengguna.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp_file);

        // Kirim response download
        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }


    // GET Create Form
    public function createPengguna()
    {
        return Inertia::render('Admin/DaftarPengguna/Create', [
            'user' => Auth::user(),
        ]);
    }

    // GET Store Form
    public function storePengguna(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:whatsapps,phone_number',
        ]);

        // Buat user baru dengan password default & akun terverifikasi
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('12345678'), // Password default
            'role' => 'user', // Role otomatis user
            'email_verified_at' => Auth::user()->role === 'admin' ? now() : null, // Otomatis terverifikasi
        ]);

        // Simpan nomor WhatsApp terkait user yang baru dibuat
        Whatsapp::create([
            'user_id' => $user->id,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('admin.pengguna')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // GET Edit Form
    public function editPengguna($id)
    {
        $pengguna = User::where('id', $id)->where('role', 'user')->firstOrFail();
        $whatsapp = Whatsapp::where('user_id', $id)->first();

        return Inertia::render('Admin/DaftarPengguna/Edit', [
            'pengguna' => $pengguna,
            'whatsapp' => $whatsapp,
            'user' => Auth::user(),
        ]);
    }

    public function updatePengguna(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|unique:whatsapps,phone_number,' . $id . ',user_id',
        ]);

        $pengguna = User::where('id', $id)->where('role', 'user')->firstOrFail();
        $pengguna->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        Whatsapp::updateOrCreate(
            ['user_id' => $id],
            ['phone_number' => $request->phone_number]
        );

        return redirect()->route('admin.pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // DELETE Delete Form
    public function deletePengguna($id)
    {
        $pengguna = User::where('id', $id)->where('role', 'user')->firstOrFail();
        $pengguna->delete();

        return redirect()->route('admin.pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }

    // public function deletePengguna($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();

    //     return redirect()->route('admin.pengguna')->with('success', 'Pengguna berhasil dihapus');
    // }
}
