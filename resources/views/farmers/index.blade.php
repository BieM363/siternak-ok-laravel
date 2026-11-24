@extends('layouts.app')

@section('title', 'Data Pemilik Sapi (Peternak)')

@section('content')
<div class="space-y-6"
     x-data="{ openModal: false, openEditModal: false, editData: { id: '', nik: '', name: '', phone: '', address: '', village_id: '' } }"
     @keydown.escape.window="openModal = false; openEditModal = false">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400"></i>
                <span>Data Pemilik Sapi (Peternak)</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                1 peternak dapat memiliki banyak sapi betina. NIK KTP bersifat <strong>opsional</strong> dan tersedia filter khusus untuk pemilik tanpa NIK.
            </p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <button type="button" @click="openModal = true"
                class="min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shrink-0 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pemilik Sapi</span>
            </button>
        @endif
    </div>

    {{-- Ringkasan & Filter Cepat NIK --}}
    <div class="grid grid-cols-3 gap-3 tabular-nums">
        <a href="{{ route('farmers.index', array_merge(request()->except('nik_status', 'page'))) }}"
           class="glass-card p-3.5 rounded-2xl border {{ !$nikStatus ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-bold uppercase text-slate-500">Total Pemilik</span>
            <strong class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($totalFarmersCount) }}</strong>
        </a>
        <a href="{{ route('farmers.index', array_merge(request()->except('page'), ['nik_status' => 'with_nik'])) }}"
           class="glass-card p-3.5 rounded-2xl border {{ $nikStatus === 'with_nik' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-400">Memiliki NIK</span>
            <strong class="text-xl font-black text-emerald-700 dark:text-emerald-400">{{ number_format($withNikCount) }}</strong>
        </a>
        <a href="{{ route('farmers.index', array_merge(request()->except('page'), ['nik_status' => 'without_nik'])) }}"
           class="glass-card p-3.5 rounded-2xl border {{ $nikStatus === 'without_nik' ? 'border-amber-500 ring-1 ring-amber-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-bold uppercase text-amber-700 dark:text-amber-400">Tanpa NIK</span>
            <strong class="text-xl font-black text-amber-600 dark:text-amber-400">{{ number_format($withoutNikCount) }}</strong>
        </a>
    </div>

    {{-- Bar Pencarian & Filter --}}
    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('farmers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemilik, NIK, atau HP..."
                class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">

            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <select name="regency_id" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="nik_status" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                <option value="">-- Semua Status NIK --</option>
                <option value="with_nik" {{ $nikStatus === 'with_nik' ? 'selected' : '' }}>Ada NIK KTP</option>
                <option value="without_nik" {{ $nikStatus === 'without_nik' ? 'selected' : '' }}>Tanpa NIK</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl">Filter</button>
                <a href="{{ route('farmers.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl flex items-center justify-center" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($farmers as $farmer)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            @if($farmer->nik)
                                <span class="inline-block px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-950 font-mono text-emerald-700 dark:text-emerald-400 font-bold border border-slate-200 dark:border-slate-800 text-[11px] tabular-nums">
                                    {{ (auth()->check() && !auth()->user()->isGuest()) ? $farmer->nik : substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4) }}
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-extrabold">
                                    Tanpa NIK
                                </span>
                            @endif
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mt-1">{{ $farmer->name }}</h3>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">
                                <strong class="text-slate-800 dark:text-slate-300">{{ $farmer->regency->name ?? '-' }}</strong> · Kec. {{ $farmer->district->name ?? '-' }}
                            </p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-slate-950 text-emerald-700 dark:text-emerald-400 font-extrabold text-[11px] rounded-lg border border-emerald-200 dark:border-slate-800 shrink-0 tabular-nums">
                            {{ $farmer->cows->count() }} Ekor
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <span class="text-slate-600 dark:text-slate-400 font-mono tabular-nums">
                            <i class="fa-solid fa-phone mr-1 text-emerald-600"></i>{{ $farmer->phone ?? '-' }}
                        </span>
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                    @click="editData = { id: {{ $farmer->id }}, nik: '{{ $farmer->nik ?? '' }}', name: '{{ addslashes($farmer->name) }}', phone: '{{ $farmer->phone ?? '' }}', address: '{{ addslashes($farmer->address ?? '') }}', village_id: '{{ $farmer->village_id }}' }; openEditModal = true"
                                    class="min-h-[34px] px-3 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-lg text-[11px] font-bold border border-amber-200 dark:border-amber-800">
                                    Edit
                                </button>
                                <form action="{{ route('farmers.destroy', $farmer->id) }}" method="POST" onsubmit="return confirm('Hapus peternak ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="min-w-[34px] min-h-[34px] px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 rounded-lg text-[11px] font-bold border border-rose-200 dark:border-rose-800">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Belum ada data pemilik sapi sesuai filter.</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK KTP (Opsional)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Pemilik Sapi</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">No. Telepon</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jumlah Sapi</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($farmers as $farmer)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3.5 font-mono whitespace-nowrap tabular-nums">
                            @if($farmer->nik)
                                <span class="text-emerald-700 dark:text-emerald-400 font-bold">
                                    {{ (auth()->check() && !auth()->user()->isGuest()) ? $farmer->nik : substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4) }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 font-sans text-[10px] font-extrabold">
                                    Tanpa NIK
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ $farmer->name }}</td>
                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 whitespace-nowrap tabular-nums">{{ $farmer->phone ?? '-' }}</td>
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                        @click="editData = { id: {{ $farmer->id }}, nik: '{{ $farmer->nik ?? '' }}', name: '{{ addslashes($farmer->name) }}', phone: '{{ $farmer->phone ?? '' }}', address: '{{ addslashes($farmer->address ?? '') }}', village_id: '{{ $farmer->village_id }}' }; openEditModal = true"
                                        class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-lg border border-amber-200 dark:border-amber-800" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('farmers.destroy', $farmer->id) }}" method="POST" onsubmit="return confirm('Hapus peternak ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 rounded-lg border border-rose-200 dark:border-rose-800" title="Hapus">
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
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data pemilik sapi sesuai filter.</td>
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
    {{-- Modal Tambah Pemilik Sapi (NIK Opsional) --}}
    <div x-show="openModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-lg shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">Tambah Pemilik Sapi Baru</h2>
                <button @click="openModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('farmers.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="add-farmer-name" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap Pemilik <span class="text-rose-500">*</span></label>
                    <input id="add-farmer-name" type="text" name="name" required placeholder="Contoh: Ahmad Mohamad" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="add-farmer-nik" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIK KTP (Opsional, 16 Digit)</label>
                    <input id="add-farmer-nik" type="text" name="nik" maxlength="16" placeholder="Kosongkan jika pemilik belum memiliki NIK" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white font-mono tabular-nums">
                </div>
                <div>
                    <label for="add-farmer-phone" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">No. Telepon / WhatsApp (Opsional)</label>
                    <input id="add-farmer-phone" type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white tabular-nums">
                </div>
                <div>
                    <label for="add-farmer-village" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Desa / Kelurahan Domisili <span class="text-rose-500">*</span></label>
                    <select id="add-farmer-village" name="village_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        <option value="">-- Pilih Desa/Kelurahan --</option>
                        @foreach($districts as $dist)
                            <optgroup label="{{ $dist->regency->name ?? '' }} · Kec. {{ $dist->name }}">
                                @foreach($dist->villages as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="add-farmer-address" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat Lengkap (Opsional)</label>
                    <textarea id="add-farmer-address" name="address" rows="2" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="openModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-extrabold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Pemilik Sapi (NIK Opsional) --}}
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openEditModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-lg shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">Edit Data Pemilik Sapi</h2>
                <button @click="openEditModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'/farmers/' + editData.id" method="POST" class="space-y-4 text-xs">
                @csrf @method('PUT')
                <div>
                    <label for="edit-farmer-name" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap Pemilik <span class="text-rose-500">*</span></label>
                    <input id="edit-farmer-name" type="text" name="name" x-model="editData.name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="edit-farmer-nik" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIK KTP (Opsional, 16 Digit)</label>
                    <input id="edit-farmer-nik" type="text" name="nik" x-model="editData.nik" maxlength="16" placeholder="Boleh dikosongkan" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white font-mono tabular-nums">
                </div>
                <div>
                    <label for="edit-farmer-phone" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">No. Telepon / WhatsApp</label>
                    <input id="edit-farmer-phone" type="text" name="phone" x-model="editData.phone" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white tabular-nums">
                </div>
                <div>
                    <label for="edit-farmer-village" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                    <select id="edit-farmer-village" name="village_id" x-model="editData.village_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        @foreach($districts as $dist)
                            <optgroup label="{{ $dist->regency->name ?? '' }} · Kec. {{ $dist->name }}">
                                @foreach($dist->villages as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit-farmer-address" class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat</label>
                    <textarea id="edit-farmer-address" name="address" x-model="editData.address" rows="2" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="openEditModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-extrabold">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
