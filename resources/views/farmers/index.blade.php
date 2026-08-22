@extends('layouts.app')

@section('title', 'Manajemen Data Peternak')

@section('content')
<div class="space-y-6" x-data="{ openModal: false, openEditModal: false, editData: { id: '', nik: '', name: '', phone: '', address: '', village_id: '' } }">

    <!-- Header & Add Farmer Modal Trigger -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-emerald-500"></i>
                Data Peternak Pemilik Sapi (NIK & Nama Terpisah)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola NIK KTP, nama lengkap peternak, nomor telepon, dan lokasi domisili wilayah.
            </p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <button
                @click="openModal = true"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2 flex-shrink-0 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Peternak Baru</span>
            </button>
        @endif
    </div>

    <!-- Table Peternak -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK KTP Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">No. Telepon</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kab/Kota, Kec & Desa/Kelurahan</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jumlah Sapi Betina</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 bg-white dark:bg-slate-900/30">
                    @forelse($farmers as $farmer)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3.5 font-mono text-emerald-700 dark:text-emerald-400 font-bold whitespace-nowrap">
                            @if(auth()->check() && !auth()->user()->isGuest())
                                {{ $farmer->nik }}
                            @else
                                {{ substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4) }}
                            @endif
                        </td>

                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $farmer->name }}
                        </td>

                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            {{ $farmer->phone ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                            <strong class="text-slate-800 dark:text-slate-300">{{ $farmer->regency->name ?? '-' }}</strong>,
                            Kec. {{ $farmer->district->name ?? '-' }}
                            ({{ ($farmer->regency && str_contains(strtolower($farmer->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $farmer->village->name ?? '-' }})
                        </td>

                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-slate-950 text-emerald-700 dark:text-emerald-400 font-extrabold rounded-lg border border-emerald-200 dark:border-slate-800">
                                {{ $farmer->cows->count() }} Ekor
                            </span>
                        </td>

                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button
                                        type="button"
                                        @click="editData = { id: {{ $farmer->id }}, nik: '{{ $farmer->nik }}', name: '{{ addslashes($farmer->name) }}', phone: '{{ $farmer->phone ?? '' }}', address: '{{ addslashes($farmer->address ?? '') }}', village_id: '{{ $farmer->village_id }}' }; openEditModal = true"
                                        class="px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/80 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-300 rounded-lg text-[11px] font-semibold border border-amber-300 dark:border-amber-800 transition"
                                        title="Edit Data Peternak"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form
                                        action="{{ route('farmers.destroy', $farmer->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus peternak ini?')"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/80 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 rounded-lg text-[11px] font-semibold border border-rose-300 dark:border-rose-800 transition"
                                            title="Hapus Peternak"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-400 font-mono text-[10px]">Read-only</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Belum ada data peternak terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($farmers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                {{ $farmers->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL EDIT PETERNAK -->
    @if(auth()->check() && auth()->user()->isAdmin())
        <div
            x-show="openEditModal"
            x-cloak
            x-transition
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        >
            <div
                @click.away="openEditModal = false"
                class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-lg shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">
                        Edit Data Peternak
                    </h4>
                    <button
                        @click="openEditModal = false"
                        type="button"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form :action="'/farmers/' + editData.id" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            NIK KTP Peternak (16 Digit) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nik"
                            x-model="editData.nik"
                            required
                            maxlength="16"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white font-mono"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Nama Lengkap Peternak <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            x-model="editData.name"
                            required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input
                            type="text"
                            name="phone"
                            x-model="editData.phone"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Wilayah Domisili (Desa/Kelurahan) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="village_id"
                            x-model="editData.village_id"
                            required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"
                        >
                            @foreach($districts as $d)
                                <optgroup label="Kec. {{ $d->name }} ({{ $d->regency->name ?? '' }})">
                                    @foreach($d->villages as $v)
                                        <option value="{{ $v->id }}">
                                            {{ ($d->regency && str_contains(strtolower($d->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $v->name }} [{{ $v->code }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Alamat Lengkap Domisili
                        </label>
                        <textarea
                            name="address"
                            x-model="editData.address"
                            rows="2"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="openEditModal = false"
                            class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl font-semibold"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL TAMBAH PETERNAK -->
    @if(auth()->check() && auth()->user()->isAdmin())
        <div
            x-show="openModal"
            x-cloak
            x-transition
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        >
            <div
                @click.away="openModal = false"
                class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-lg shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">
                        Tambah Data Peternak Baru
                    </h4>
                    <button
                        @click="openModal = false"
                        type="button"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form
                    action="{{ route('farmers.store') }}"
                    method="POST"
                    class="space-y-4"
                >
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            NIK KTP Peternak (16 Digit) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nik"
                            required
                            placeholder="Contoh: 3507121001850001"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:border-emerald-500"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Nama Lengkap Peternak <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Contoh: Ahmad Sugiono"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:border-emerald-500"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            No. Telepon / WhatsApp
                        </label>
                        <input
                            type="text"
                            name="phone"
                            placeholder="081234567890"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Desa Domisili Peternak <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="village_id"
                            required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"
                        >
                            <option value="">-- Pilih Desa --</option>
                            @foreach($districts as $d)
                                <optgroup label="Kecamatan {{ $d->name }}">
                                    @foreach($d->villages as $v)
                                        <option value="{{ $v->id }}">
                                            Desa {{ $v->name }} ({{ $v->code }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Alamat Lengkap
                        </label>
                        <textarea
                            name="address"
                            rows="2"
                            placeholder="Dusun, RT/RW..."
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="openModal = false"
                            class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow"
                        >
                            Simpan Data Peternak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
