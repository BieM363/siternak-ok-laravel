@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Scope Regional')

@section('content')
<div class="space-y-6" x-data="{ openAddUserModal: false, openEditUserModal: false, selectedRole: 'operator', editUserData: { id: '', name: '', email: '', role: 'operator', regency_id: '', phone: '' } }">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-emerald-500"></i>
                Kelola Pengguna, Role & Scope Wilayah (Provinsi vs Kab/Kota)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pengaturan akun resmi: Admin Provinsi (Seluruh Gorontalo), Admin Kab/Kota (Wilayah spesifik), dan Operator (Inseminator Lapangan).
            </p>
            @if(auth()->user() && auth()->user()->isAdminKabupaten())
                <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 text-[11px] font-bold rounded-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                    Scope Regional Terkunci: {{ auth()->user()->regency->name ?? 'Kabupaten/Kota Anda' }}
                </span>
            @endif
        </div>

        <button @click="openAddUserModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2 shrink-0 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah User / Admin Baru</span>
        </button>
    </div>

    <!-- Table Users -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Pengguna</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Email Login</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Scope Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Role Hak Akses</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 bg-white dark:bg-slate-900/30">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2 whitespace-nowrap">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center text-xs border border-emerald-300 dark:border-emerald-800 shrink-0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="block text-slate-900 dark:text-white font-extrabold text-sm">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $u->phone ?? 'Tidak ada no hp' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-200 font-mono font-semibold whitespace-nowrap">{{ $u->email }}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($u->isAdminProvinsi())
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-700">
                                    <i class="fa-solid fa-layer-group mr-1"></i> Provinsi (Seluruh Kab/Kota)
                                </span>
                            @elseif($u->regency)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-300 dark:border-teal-700">
                                    <i class="fa-solid fa-location-dot mr-1"></i> {{ $u->regency->name }}
                                </span>
                            @else
                                <span class="text-slate-500 italic text-[11px]">Semua Wilayah</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @php
                                $roleBadge = match($u->role) {
                                    'admin' => $u->isAdminProvinsi() ? 'bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 border-purple-300 dark:border-purple-600' : ($u->isAdminKota() ? 'bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border-teal-300 dark:border-teal-600' : 'bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border-blue-300 dark:border-blue-600'),
                                    'operator' => 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-600',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700',
                                };
                                $roleName = match($u->role) {
                                    'admin' => $u->isAdminProvinsi() ? 'ADMIN PROVINSI' : ($u->isAdminKota() ? 'ADMIN KOTA' : 'ADMIN KABUPATEN'),
                                    'operator' => 'OPERATOR / INSEMINATOR',
                                    default => 'PENGGUNA',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border {{ $roleBadge }}">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            @if($u->id !== auth()->id())
                            <div class="flex items-center justify-end space-x-1.5">
                                <button 
                                    type="button" 
                                    @click="editUserData = { id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}', role: '{{ $u->role }}', regency_id: '{{ $u->regency_id ?? '' }}', phone: '{{ $u->phone ?? '' }}' }; openEditUserModal = true"
                                    class="px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/80 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-300 rounded-lg text-[11px] font-semibold border border-amber-300 dark:border-amber-800 transition"
                                    title="Edit User"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/80 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 rounded-lg text-[11px] font-semibold border border-rose-300 dark:border-rose-800 transition" title="Hapus User">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-400 font-mono italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada user ditemukan pada scope ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div x-show="openAddUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openAddUserModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Tambah Pengguna Baru</h4>
                <button @click="openAddUserModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Contoh: Admin Kab. Gorontalo / Budi Inseminator" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Login *</label>
                    <input type="email" name="email" required placeholder="admin.gorontalo@siternak.go.id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Role Hak Akses *</label>
                    <select name="role" x-model="selectedRole" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        <option value="admin">Administrasi (Admin Provinsi / Admin Kabupaten / Admin Kota)</option>
                        <option value="operator">Operator (Inseminator Lapangan)</option>
                    </select>
                </div>

                <!-- Scope Regency Selection -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Wilayah Kabupaten/Kota</label>
                    <select name="regency_id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white" {{ auth()->user() && auth()->user()->isAdminKabupaten() ? 'disabled' : '' }}>
                        @if(!auth()->user() || !auth()->user()->isAdminKabupaten())
                        <option value="">-- Semua Kabupaten (Admin Provinsi) --</option>
                        @endif
                        @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ (auth()->user() && auth()->user()->regency_id == $reg->id) ? 'selected' : '' }}>{{ $reg->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                        @if(auth()->user() && auth()->user()->isAdminKabupaten())
                            Terkunci pada wilayah Kabupaten/Kota Anda.
                        @else
                            Kosongkan jika akun adalah Admin Provinsi yang memiliki akses seluruh Gorontalo.
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi (Password) *</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openAddUserModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div x-show="openEditUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openEditUserModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Edit Data Pengguna</h4>
                <button @click="openEditUserModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form :action="'/users/' + editUserData.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" x-model="editUserData.name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Login *</label>
                    <input type="email" name="email" x-model="editUserData.email" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Role Hak Akses *</label>
                    <select name="role" x-model="editUserData.role" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        <option value="admin">Administrasi (Admin Provinsi / Admin Kabupaten / Admin Kota)</option>
                        <option value="operator">Operator (Inseminator Lapangan)</option>
                    </select>
                </div>

                <!-- Scope Regency Selection -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Wilayah Kabupaten/Kota</label>
                    <select name="regency_id" x-model="editUserData.regency_id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white" {{ auth()->user() && auth()->user()->isAdminKabupaten() ? 'disabled' : '' }}>
                        @if(!auth()->user() || !auth()->user()->isAdminKabupaten())
                        <option value="">-- Semua Kabupaten (Admin Provinsi) --</option>
                        @endif
                        @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" x-model="editUserData.phone" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openEditUserModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
