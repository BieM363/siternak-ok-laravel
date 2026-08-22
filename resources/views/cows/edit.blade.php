@extends('layouts.app')

@section('title', 'Edit Data Sapi - ' . $cow->cow_nik)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Edit Data Sapi Betina Induk
            </h3>
            <p class="text-xs text-slate-400 mt-1">Perbarui profil sapi betina, ras, dan lokasi peternak pemilik.</p>
        </div>
        <a href="{{ route('cows.show', $cow->id) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold border border-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Batal
        </a>
    </div>

    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-2xl">
        <form action="{{ route('cows.update', $cow->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">NIK Sapi (Tetap)</label>
                <input type="text" value="{{ $cow->cow_nik }}" disabled 
                    class="w-full px-4 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-sm font-mono font-bold text-emerald-400 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Nama / Alias Sapi</label>
                <input type="text" name="name" value="{{ old('name', $cow->name) }}" 
                    class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Ras / Jenis Sapi <span class="text-rose-500">*</span></label>
                    <select name="breed" required class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                        @foreach(['Limosin', 'Simental', 'PO (Peranakan Ongole)', 'Brahman', 'Frisian Holstein', 'Bali', 'Cross / Persilangan'] as $b)
                        <option value="{{ $b }}" {{ $cow->breed == $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                    <select name="village_id" required class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
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
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Pilih Peternak Pemilik (Pencarian NIK & Nama Terpisah) <span class="text-rose-500">*</span></label>
                <select name="farmer_id" required class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                    @foreach($farmers as $f)
                    <option value="{{ $f->id }}" {{ $cow->farmer_id == $f->id ? 'selected' : '' }}>
                        NIK: {{ $f->nik }} | Nama: {{ $f->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Status Kebuntingan Saat Ini <span class="text-rose-500">*</span></label>
                <select name="current_status" required class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="Tidak Bunting" {{ $cow->current_status == 'Tidak Bunting' ? 'selected' : '' }}>Tidak Bunting</option>
                    <option value="Pasca IB" {{ $cow->current_status == 'Pasca IB' ? 'selected' : '' }}>Pasca IB</option>
                    <option value="Bunting (PKB 90 Hari)" {{ $cow->current_status == 'Bunting (PKB 90 Hari)' ? 'selected' : '' }}>Bunting (PKB 90 Hari)</option>
                    <option value="Siap Melahirkan" {{ $cow->current_status == 'Siap Melahirkan' ? 'selected' : '' }}>Siap Melahirkan (245 Hari)</option>
                    <option value="Mendekati Melahirkan" {{ $cow->current_status == 'Mendekati Melahirkan' ? 'selected' : '' }}>Mendekati Melahirkan</option>
                    <option value="Pasca Melahirkan" {{ $cow->current_status == 'Pasca Melahirkan' ? 'selected' : '' }}>Pasca Melahirkan</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('cows.show', $cow->id) }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                    Simpan Pembaruan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
