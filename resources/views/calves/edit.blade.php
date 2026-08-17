@extends('layouts.app')

@section('title', 'Edit Data Anak Sapi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('calves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold border border-slate-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Batal & Kembali</span>
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="glass-card rounded-2xl border border-slate-800 p-6 space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Edit Data Anak Sapi (Pedet)
            </h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui informasi NIK, ras, jenis kelamin, tanggal lahir, dan berat lahir anak sapi.</p>
        </div>

        <form action="{{ route('calves.update', $calf->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- NIK Anak Sapi -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">NIK / Kode Anak Sapi <span class="text-rose-400">*</span></label>
                <input type="text" name="calf_nik" value="{{ old('calf_nik', $calf->calf_nik) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500 font-mono">
            </div>

            <!-- Nama Anak Sapi -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Anak Sapi</label>
                <input type="text" name="name" value="{{ old('name', $calf->name) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>

            <!-- Ras Anak Sapi -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ras / Rumpun Ternak</label>
                <select name="breed" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Pilih Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ old('breed', $calf->breed) === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Kelamin & Tanggal Lahir Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jenis Kelamin <span class="text-rose-400">*</span></label>
                    <select name="gender" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="Jantan" {{ old('gender', $calf->gender) === 'Jantan' ? 'selected' : '' }}>Jantan</option>
                        <option value="Betina" {{ old('gender', $calf->gender) === 'Betina' ? 'selected' : '' }}>Betina</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Tanggal Kelahiran <span class="text-rose-400">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $calf->birth_date ? $calf->birth_date->format('Y-m-d') : '') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <!-- Berat Lahir & Kondisi Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Berat Lahir (kg)</label>
                    <input type="number" step="0.1" name="birth_weight" value="{{ old('birth_weight', $calf->birth_weight) }}" placeholder="Contoh: 28.5" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Kondisi Saat Lahir</label>
                    <select name="condition" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="Sehat" {{ old('condition', $calf->condition) === 'Sehat' ? 'selected' : '' }}>Sehat</option>
                        <option value="Cacat" {{ old('condition', $calf->condition) === 'Cacat' ? 'selected' : '' }}>Cacat</option>
                        <option value="Mati" {{ old('condition', $calf->condition) === 'Mati' ? 'selected' : '' }}>Mati</option>
                    </select>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-emerald-500">{{ old('notes', $calf->notes) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('calves.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-900/40 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
