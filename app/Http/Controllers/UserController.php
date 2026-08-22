<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Regency;
use Illuminate\Support\Facades\Hash;

/**
 * Controller Manajemen Pengguna & Role
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi)
 * Mengelola pendaftaran dan hak akses role Pengguna (Administrasi Provinsi, Admin Kabupaten, Operator, Tamu).
 */
class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = User::with('regency')->latest();

        // Scope isolasi Admin Kabupaten / Kota
        if ($user && $user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        }

        $users = $query->get();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::all();

        return view('users.index', compact('users', 'regencies'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,operator',
            'regency_id' => 'nullable|exists:regencies,id',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
        ]);

        $regencyId = $request->regency_id ?: null;
        if ($user && $user->isAdminKabupaten()) {
            $regencyId = $user->regency_id; // Lock otomatis ke Kabupaten Admin terkait
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => strtolower($request->role),
            'regency_id' => $regencyId,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();
        $targetUser = User::findOrFail($id);

        if ($currentUser && $currentUser->isAdminKabupaten() && $targetUser->regency_id != $currentUser->regency_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah data user dari wilayah lain.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,operator',
            'regency_id' => 'nullable|exists:regencies,id',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string',
        ]);

        $regencyId = $request->regency_id ?: null;
        if ($currentUser && $currentUser->isAdminKabupaten()) {
            $regencyId = $currentUser->regency_id;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => strtolower($request->role),
            'regency_id' => $regencyId,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $targetUser->update($data);
        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $currentUser = auth()->user();
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($currentUser && $currentUser->isAdminKabupaten() && $targetUser->regency_id != $currentUser->regency_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus user dari wilayah lain.');
        }

        $targetUser->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
