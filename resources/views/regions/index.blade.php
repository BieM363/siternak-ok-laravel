@extends('layouts.app')

@section('title', 'Hirarki Wilayah Dinas Peternakan & Perkebunan Provinsi Gorontalo')

@section('content')
<div class="space-y-6" x-data="{ 
    openRegModal: false, 
    openDistModal: false, 
    openVillModal: false,
    openEditRegModal: false,
    openEditDistModal: false,
    openEditVillModal: false,
    editRegData: { id: '', code: '', name: '' },
    editDistData: { id: '', code: '', name: '', regency_name: '' },
    editVillData: { id: '', code: '', name: '', district_name: '' },
    openEditReg(r) {
        this.editRegData = { id: r.id, code: r.code, name: r.name };
        this.openEditRegModal = true;
    },
    openEditDist(d) {
        this.editDistData = { id: d.id, code: d.code, name: d.name, regency_name: d.regency ? d.regency.name : '' };
        this.openEditDistModal = true;
    },
    openEditVill(v, distName) {
        this.editVillData = { id: v.id, code: v.code, name: v.name, district_name: distName };
        this.openEditVillModal = true;
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-emerald-500"></i>
                Hirarki Wilayah Dinas Peternakan & Perkebunan Provinsi Gorontalo
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Struktur hirarki wilayah (Kabupaten/Kota &rarr; Kecamatan &rarr; Desa/Kelurahan) sebagai acuan NIK dan zonasi populasi ternak.</p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="flex flex-wrap items-center gap-2 shrink-0 w-full sm:w-auto">
            @if(auth()->user()->isAdminProvinsi())
            <button @click="openRegModal = true" class="flex-1 sm:flex-initial px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition text-center whitespace-nowrap">
                + Tambah Kab/Kota
            </button>
            @endif
            <button @click="openDistModal = true" class="flex-1 sm:flex-initial px-3.5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition text-center whitespace-nowrap">
                + Tambah Kecamatan
            </button>
            <button @click="openVillModal = true" class="flex-1 sm:flex-initial px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition text-center whitespace-nowrap">
                + Tambah {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }}
            </button>
        </div>
        @endif
    </div>

    <!-- Grid List Kabupaten & Kecamatan -->
    <div class="space-y-6">
        @foreach($regencies as $reg)
        @php
            $isCity = str_contains(strtolower($reg->name), 'kota');
            $regCodeLabel = $isCity ? 'KODE KOTA' : 'KODE KAB';
            $unitLabel = $isCity ? 'Kelurahan' : 'Desa';
        @endphp
        <div class="glass-card p-4 sm:p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 gap-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-purple-200 dark:border-purple-800">
                        {{ $regCodeLabel }}: {{ $reg->code }}
                    </span>
                    <h4 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $reg->name }}</span>
                        @if(auth()->check() && auth()->user()->isAdminProvinsi())
                        <button type="button" @click="openEditReg({{ json_encode($reg) }})" class="text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 transition text-xs p-1" title="Edit Nama / Kode {{ $reg->name }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        @endif
                    </h4>
                </div>
                <span class="text-xs text-slate-700 dark:text-slate-300 font-bold bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                    {{ $reg->districts->count() }} Kecamatan
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                @forelse($reg->districts as $d)
                <div class="bg-slate-50 dark:bg-slate-950/80 p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2.5 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 pb-2 gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block">KEC: {{ $d->code }}</span>
                            <div class="flex items-center gap-1.5 min-w-0">
                                <h5 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate" title="{{ $d->name }}">{{ $d->name }}</h5>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <button type="button" @click="openEditDist({{ json_encode($d) }})" class="text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition text-xs p-0.5 shrink-0" title="Edit Kecamatan {{ $d->name }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-800 font-semibold shrink-0">
                            {{ $d->villages->count() }} {{ $unitLabel }}
                        </span>
                    </div>

                    <div class="space-y-1 max-h-56 overflow-y-auto pr-1">
                        @forelse($d->villages as $v)
                        <div class="bg-white dark:bg-slate-900/70 p-2 rounded-xl border border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs gap-2">
                            <span class="font-semibold text-slate-800 dark:text-slate-300 truncate" title="{{ $unitLabel }} {{ $v->name }}">
                                {{ $unitLabel }} {{ $v->name }}
                            </span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="font-mono text-[10px] text-amber-600 dark:text-amber-400 font-bold">[{{ $v->code }}]</span>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <button type="button" @click="openEditVill({{ json_encode($v) }}, '{{ addslashes($d->name) }}')" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition text-[11px] p-0.5" title="Edit {{ $unitLabel }} {{ $v->name }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 italic">Belum ada {{ strtolower($unitLabel) }}.</p>
                        @endforelse
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 italic col-span-full">Belum ada kecamatan terdaftar di wilayah ini.</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Tambah Kabupaten/Kota -->
    @if(auth()->check() && auth()->user()->isAdminProvinsi())
    <div x-show="openRegModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openRegModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Kabupaten / Kota Baru</h4>
            <form action="{{ route('regions.store-regency') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Wilayah (misal: 75.02)</label>
                    <input type="text" name="code" required placeholder="75.02" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kabupaten / Kota</label>
                    <input type="text" name="name" required placeholder="Kabupaten Gorontalo" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openRegModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Edit Kabupaten/Kota -->
    @if(auth()->check() && auth()->user()->isAdminProvinsi())
    <div x-show="openEditRegModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openEditRegModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-purple-500"></i>
                <span>Edit Data Kabupaten / Kota</span>
            </h4>
            <form :action="'/regions/regency/' + editRegData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Wilayah (misal: 75.02)</label>
                    <input type="text" name="code" x-model="editRegData.code" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kabupaten / Kota</label>
                    <input type="text" name="name" x-model="editRegData.name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openEditRegModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow">Update Kab/Kota</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Tambah Kecamatan -->
    <div x-show="openDistModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openDistModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Kecamatan (misal: 75.02.01)</label>
                    <input type="text" name="code" required placeholder="75.02.01" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kecamatan</label>
                    <input type="text" name="name" required placeholder="Limboto" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openDistModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kecamatan -->
    <div x-show="openEditDistModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openEditDistModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-emerald-500"></i>
                <span>Edit Data Kecamatan</span>
            </h4>
            <form :action="'/regions/district/' + editDistData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div x-show="editDistData.regency_name">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Wilayah Kabupaten / Kota</label>
                    <input type="text" x-model="editDistData.regency_name" disabled class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-500 dark:text-slate-400 font-semibold cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Kecamatan (misal: 75.02.01)</label>
                    <input type="text" name="code" x-model="editDistData.code" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kecamatan</label>
                    <input type="text" name="name" x-model="editDistData.name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openEditDistModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow">Update Kecamatan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Desa / Kelurahan -->
    <div x-show="openVillModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openVillModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                Tambah {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa / Kelurahan' }} Baru
            </h4>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Kode {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }} (4 digit, misal: 1001 / 2001)
                    </label>
                    <input type="text" name="code" required placeholder="1001" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nama {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }}
                    </label>
                    <input type="text" name="name" required placeholder="Kayubulan / Biawu" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openVillModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Desa / Kelurahan -->
    <div x-show="openEditVillModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openEditVillModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                <span>Edit Data {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa / Kelurahan' }}</span>
            </h4>
            <form :action="'/regions/village/' + editVillData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div x-show="editVillData.district_name">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Kecamatan Induk</label>
                    <input type="text" x-model="editVillData.district_name" disabled class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-500 dark:text-slate-400 font-semibold cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Kode {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }} (4 digit, misal: 1001 / 2001)
                    </label>
                    <input type="text" name="code" x-model="editVillData.code" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nama {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }}
                    </label>
                    <input type="text" name="name" x-model="editVillData.name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openEditVillModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow">Update {{ (auth()->user() && auth()->user()->isAdminKota()) ? 'Kelurahan' : 'Desa' }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
