@extends('layouts.app')

@section('title', 'Edit Data Sapi - ' . $cow->cow_nik)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-600 dark:text-amber-400"></i>
                Edit Data Sapi Betina Induk
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perbarui profil sapi betina, ras, dan lokasi peternak pemilik.</p>
        </div>
        <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[38px] px-3.5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Batal
        </a>
    </div>

    <div class="glass-card p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('cows.update', $cow->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-cow-nik" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">NIK Sapi (Tetap)</label>
                <input id="edit-cow-nik" type="text" value="{{ $cow->cow_nik }}" disabled
                    class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono font-bold text-emerald-700 dark:text-emerald-400 cursor-not-allowed tabular-nums">
            </div>

            <div>
                <label for="edit-cow-name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama / Alias Sapi</label>
                <input id="edit-cow-name" type="text" name="name" value="{{ old('name', $cow->name) }}"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-cow-breed" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Ras / Jenis Sapi <span class="text-rose-500">*</span></label>
                    <select id="edit-cow-breed" name="breed" required class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        @foreach($breeds ?? \App\Models\Cow::BREEDS as $b)
                        <option value="{{ $b }}" {{ $cow->breed == $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit-cow-village" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                    <select id="edit-cow-village" name="village_id" required class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        @foreach($districts as $d)
                            <optgroup label="Kec. {{ $d->name }}">
                                @foreach($d->villages as $v)
                                <option value="{{ $v->id }}" {{ $cow->village_id == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->code }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="edit-cow-farmer" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pilih Peternak Pemilik (NIK &amp; Nama Terpisah) <span class="text-rose-500">*</span></label>
                <select id="edit-cow-farmer" name="farmer_id" required class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    @foreach($farmers as $f)
                    <option value="{{ $f->id }}" {{ $cow->farmer_id == $f->id ? 'selected' : '' }}>
                        {{ $f->name }} ({{ $f->nik ?: 'Tanpa NIK' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="edit-cow-status" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Kebuntingan Saat Ini <span class="text-rose-500">*</span></label>
                <select id="edit-cow-status" name="current_status" required class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <option value="Tidak Bunting" {{ $cow->current_status == 'Tidak Bunting' ? 'selected' : '' }}>Tidak Bunting</option>
                    <option value="Pasca IB" {{ $cow->current_status == 'Pasca IB' ? 'selected' : '' }}>Pasca IB</option>
                    <option value="Bunting (PKB 90 Hari)" {{ $cow->current_status == 'Bunting (PKB 90 Hari)' ? 'selected' : '' }}>Bunting (PKB 90 Hari)</option>
                    <option value="Siap Melahirkan" {{ $cow->current_status == 'Siap Melahirkan' ? 'selected' : '' }}>Siap Melahirkan (245 Hari)</option>
                    <option value="Mendekati Melahirkan" {{ $cow->current_status == 'Mendekati Melahirkan' ? 'selected' : '' }}>Mendekati Melahirkan</option>
                    <option value="Pasca Melahirkan" {{ $cow->current_status == 'Pasca Melahirkan' ? 'selected' : '' }}>Pasca Melahirkan</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[40px] px-5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 flex items-center">
                    Batal
                </a>
                <button type="submit" class="min-h-[40px] px-6 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition">
                    Simpan Pembaruan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
