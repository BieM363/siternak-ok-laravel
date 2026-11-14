@extends('layouts.app')

@section('title', 'Edit Data Anak Sapi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('calves.index') }}" class="inline-flex items-center gap-2 min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Batal &amp; Kembali</span>
        </a>
    </div>

    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 shadow-sm">
        <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-600 dark:text-amber-400"></i>
                Edit Data Anak Sapi (Pedet)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi NIK, ras, jenis kelamin, tanggal lahir, dan berat lahir anak sapi.</p>
        </div>

        <form action="{{ route('calves.update', $calf->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-calf-nik" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">NIK / Kode Anak Sapi <span class="text-rose-500">*</span></label>
                <input id="edit-calf-nik" type="text" name="calf_nik" value="{{ old('calf_nik', $calf->calf_nik) }}" required class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 font-mono tabular-nums">
            </div>

            <div>
                <label for="edit-calf-name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Anak Sapi</label>
                <input id="edit-calf-name" type="text" name="name" value="{{ old('name', $calf->name) }}" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="edit-calf-breed" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Ras / Rumpun Ternak</label>
                <select id="edit-calf-breed" name="breed" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <option value="">-- Pilih Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ old('breed', $calf->breed) === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-calf-gender" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select id="edit-calf-gender" name="gender" required class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        <option value="Jantan" {{ old('gender', $calf->gender) === 'Jantan' ? 'selected' : '' }}>Jantan</option>
                        <option value="Betina" {{ old('gender', $calf->gender) === 'Betina' ? 'selected' : '' }}>Betina</option>
                    </select>
                </div>

                <div>
                    <label for="edit-calf-birthdate" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Tanggal Kelahiran <span class="text-rose-500">*</span></label>
                    <input id="edit-calf-birthdate" type="date" name="birth_date" value="{{ old('birth_date', $calf->birth_date ? $calf->birth_date->format('Y-m-d') : '') }}" required class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 tabular-nums">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-calf-weight" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Berat Lahir (kg)</label>
                    <input id="edit-calf-weight" type="number" step="0.1" name="birth_weight" value="{{ old('birth_weight', $calf->birth_weight) }}" placeholder="Contoh: 28.5" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 tabular-nums">
                </div>

                <div>
                    <label for="edit-calf-condition" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Kondisi Saat Lahir</label>
                    <select id="edit-calf-condition" name="condition" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        <option value="Sehat" {{ old('condition', $calf->condition) === 'Sehat' ? 'selected' : '' }}>Sehat</option>
                        <option value="Cacat" {{ old('condition', $calf->condition) === 'Cacat' ? 'selected' : '' }}>Cacat</option>
                        <option value="Mati" {{ old('condition', $calf->condition) === 'Mati' ? 'selected' : '' }}>Mati</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="edit-calf-notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Catatan Tambahan</label>
                <textarea id="edit-calf-notes" name="notes" rows="3" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl p-3 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">{{ old('notes', $calf->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('calves.index') }}" class="min-h-[40px] px-4 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition flex items-center">Batal</a>
                <button type="submit" class="min-h-[40px] px-5 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
