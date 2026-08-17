@extends('layouts.app')

@section('title', 'Hirarki Wilayah Dinas Peternakan')

@section('content')
<div class="space-y-6" x-data="{ openRegModal: false, openDistModal: false, openVillModal: false }">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-emerald-500"></i>
                Hirarki Wilayah Dinas Peternakan (Kabupaten, Kecamatan, Desa)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Struktur hirarki wilayah yang menjadi acuan pengelompokan populasi sapi dan formulasi NIK Sapi.</p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            @if(auth()->user()->isAdminProvinsi())
            <button @click="openRegModal = true" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                + Tambah Kabupaten/Kota
            </button>
            @endif
            <button @click="openDistModal = true" class="px-3.5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition whitespace-nowrap">
                + Tambah Kecamatan
            </button>
            <button @click="openVillModal = true" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                + Tambah Desa
            </button>
        </div>
        @endif
    </div>

    <!-- Grid List Kabupaten & Kecamatan -->
    <div class="space-y-6">
        @foreach($regencies as $reg)
        <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center space-x-3">
                    <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-slate-950 px-2.5 py-1 rounded border border-purple-200 dark:border-purple-800">KODE KAB: {{ $reg->code }}</span>
                    <h4 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $reg->name }}</h4>
                </div>
                <span class="text-xs text-slate-700 dark:text-slate-300 font-bold bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">{{ $reg->districts->count() }} Kecamatan</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($reg->districts as $d)
                <div class="bg-slate-50 dark:bg-slate-950/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 pb-2">
                        <div>
                            <span class="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400">KEC: {{ $d->code }}</span>
                            <h5 class="text-sm font-bold text-slate-900 dark:text-white">{{ $d->name }}</h5>
                        </div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-800 font-semibold">{{ $d->villages->count() }} Desa</span>
                    </div>

                    <div class="space-y-1.5">
                        @forelse($d->villages as $v)
                        <div class="bg-white dark:bg-slate-900/70 p-2 rounded-xl border border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-800 dark:text-slate-300">Desa {{ $v->name }}</span>
                            <span class="font-mono text-[10px] text-amber-600 dark:text-amber-400 font-bold">[{{ $v->code }}]</span>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 italic">Belum ada desa.</p>
                        @endforelse
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 italic col-span-3">Belum ada kecamatan terdaftar di kabupaten ini.</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Tambah Kabupaten/Kota -->
    @if(auth()->check() && auth()->user()->isAdminProvinsi())
    <div x-show="openRegModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openRegModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Kabupaten / Kota Baru</h4>
            <form action="{{ route('regions.store-regency') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Kabupaten (misal: 35.07)</label>
                    <input type="text" name="code" required placeholder="35.07" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kabupaten / Kota</label>
                    <input type="text" name="name" required placeholder="Kabupaten Malang" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openRegModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Tambah Kecamatan -->
    <div x-show="openDistModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openDistModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Kecamatan Baru</h4>
            <form action="{{ route('regions.store-district') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Kabupaten / Kota</label>
                    <select name="regency_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($regencies as $r)
                        <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Kecamatan (misal: 35.07.12)</label>
                    <input type="text" name="code" required placeholder="35.07.12" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kecamatan</label>
                    <input type="text" name="name" required placeholder="Kepanjen" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openDistModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Desa -->
    <div x-show="openVillModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openVillModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Desa Baru</h4>
            <form action="{{ route('regions.store-village') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Kecamatan</label>
                    <select name="district_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($districts as $d)
                        <option value="{{ $d->id }}">{{ $d->code }} - {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Desa (4 digit, misal: 2001)</label>
                    <input type="text" name="code" required placeholder="2001" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Desa</label>
                    <input type="text" name="name" required placeholder="Panggungrejo" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openVillModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
