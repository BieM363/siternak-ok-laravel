@extends('layouts.app')

@section('title', 'Manajemen Data Peternak')

@section('content')
<div class="space-y-6"
     x-data="{ openModal: false, openEditModal: false, editData: { id: '', nik: '', name: '', phone: '', address: '', village_id: '' } }"
     @keydown.escape.window="openModal = false; openEditModal = false">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400"></i>
                Data Peternak Pemilik Sapi (NIK &amp; Nama Terpisah)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola NIK KTP, nama lengkap peternak, nomor telepon, dan lokasi domisili wilayah.
            </p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <button
                type="button"
                @click="openModal = true"
                class="min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center space-x-2 flex-shrink-0 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Peternak Baru</span>
            </button>
        @endif
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK KTP Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">No. Telepon</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kab/Kota, Kec &amp; Desa/Kelurahan</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jumlah Sapi Betina</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($farmers as $farmer)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3.5 font-mono text-emerald-700 dark:text-emerald-400 font-bold whitespace-nowrap tabular-nums">
                            @if(auth()->check() && !auth()->user()->isGuest())
                                {{ $farmer->nik }}
                            @else
                                {{ substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4) }}
                            @endif
                        </td>

                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $farmer->name }}
                        </td>

                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 whitespace-nowrap tabular-nums">
                            {{ $farmer->phone ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                            <strong class="text-slate-800 dark:text-slate-300">{{ $farmer->regency->name ?? '-' }}</strong>,
                            Kec. {{ $farmer->district->name ?? '-' }}
                            ({{ ($farmer->regency && str_contains(strtolower($farmer->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $farmer->village->name ?? '-' }})
                        </td>

                        <td class="px-4 py-3.5 whitespace-nowrap tabular-nums">
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
                                        aria-label="Edit data peternak {{ $farmer->name }}"
                                        class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-300 rounded-lg text-[11px] font-semibold border border-amber-200 dark:border-amber-800 transition"
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
                                            aria-label="Hapus peternak {{ $farmer->name }}"
                                            class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 rounded-lg text-[11px] font-semibold border border-rose-200 dark:border-rose-800 transition"
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
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                {{ $farmers->links() }}
            </div>
        @endif
    </div>

    @if(auth()->check() && auth()->user()->isAdmin())
        <div
            x-show="openEditModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80"
            role="dialog"
            aria-modal="true"
        >
            <div
                @click.away="openEditModal = false"
                class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-lg shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">
                        Edit Data Peternak
                    </h2>
                    <button
                        @click="openEditModal = false"
                        type="button"
                        aria-label="Tutup modal edit peternak"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form :action="'/farmers/' + editData.id" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="edit-farmer-nik" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            NIK KTP Peternak (16 Digit) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="edit-farmer-nik"
                            type="text"
                            name="nik"
                            x-model="editData.nik"
                            required
                            maxlength="16"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white font-mono tabular-nums"
                        >
                    </div>

                    <div>
                        <label for="edit-farmer-name" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Nama Lengkap Peternak <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="edit-farmer-name"
                            type="text"
                            name="name"
                            x-model="editData.name"
                            required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"
                        >
                    </div>

                    <div>
                        <label for="edit-farmer-phone" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input
                            id="edit-farmer-phone"
                            type="text"
                            name="phone"
                            x-model="editData.phone"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white tabular-nums"
                        >
                    </div>

                    <div>
                        <label for="edit-farmer-village" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Wilayah Domisili (Desa/Kelurahan) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="edit-farmer-village"
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
                        <label for="edit-farmer-address" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Alamat Lengkap Domisili
                        </label>
                        <textarea
                            id="edit-farmer-address"
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
                            class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl font-semibold"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(auth()->check() && auth()->user()->isAdmin())
        <div
            x-show="openModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80"
            role="dialog"
            aria-modal="true"
        >
            <div
                @click.away="openModal = false"
                class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-lg shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">
                        Tambah Data Peternak Baru
                    </h2>
                    <button
                        @click="openModal = false"
                        type="button"
                        aria-label="Tutup modal tambah peternak"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1"
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
                        <label for="add-farmer-nik" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            NIK KTP Peternak (16 Digit) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="add-farmer-nik"
                            type="text"
                            name="nik"
                            required
                            maxlength="16"
                            placeholder="Contoh: 7501011001850001"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:border-emerald-600 font-mono tabular-nums"
                        >
                    </div>

                    <div>
                        <label for="add-farmer-name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Nama Lengkap Peternak <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="add-farmer-name"
                            type="text"
                            name="name"
                            required
                            placeholder="Contoh: Ahmad Sugiono"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:border-emerald-600"
                        >
                    </div>

                    <div>
                        <label for="add-farmer-phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            No. Telepon / WhatsApp
                        </label>
                        <input
                            id="add-farmer-phone"
                            type="text"
                            name="phone"
                            placeholder="081234567890"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white tabular-nums"
                        >
                    </div>

                    <div>
                        <label for="add-farmer-village" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Desa / Kelurahan Domisili Peternak <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="add-farmer-village"
                            name="village_id"
                            required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"
                        >
                            <option value="">-- Pilih Desa / Kelurahan --</option>
                            @foreach($districts as $d)
                                <optgroup label="Kecamatan {{ $d->name }}">
                                    @foreach($d->villages as $v)
                                        <option value="{{ $v->id }}">
                                            {{ ($d->regency && str_contains(strtolower($d->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $v->name }} ({{ $v->code }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="add-farmer-address" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Alamat Lengkap
                        </label>
                        <textarea
                            id="add-farmer-address"
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
                            class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl"
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
