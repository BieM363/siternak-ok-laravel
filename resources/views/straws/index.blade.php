@extends('layouts.app')

@section('title', 'Master Identitas Pejantan (Straw) — Distribusi Provinsi')

@section('content')
@php
    $canManageStraws = auth()->check() && auth()->user()->isAdminProvinsi();
@endphp
<div class="space-y-6"
     x-data="{ openAddModal: false, openEditModal: false, editStraw: { id: '', breed: '', kode_bull: '', kode_batch: '', bull_name: '', producer: '', is_active: true, notes: '' } }"
     @keydown.escape.window="openAddModal = false; openEditModal = false">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-vial text-teal-600 dark:text-teal-400"></i>
                <span>Master Identitas Pejantan (Straw Semen Beku)</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Distribusi resmi Admin Provinsi Gorontalo dengan 3 kolom identitas utama: <strong>Rumpun</strong>, <strong>Kode Bull</strong>, dan <strong>Kode Batch</strong> (+ Produsen / Balai IB).
            </p>
        </div>

        @if($canManageStraws)
            <button type="button" @click="openAddModal = true"
                class="min-h-[40px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shrink-0 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Master Straw</span>
            </button>
        @else
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-700 shrink-0">
                Dikelola Admin Provinsi (Mode Baca)
            </span>
        @endif
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($straws as $st)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800 text-[10px] font-extrabold uppercase">
                                {{ $st->breed }}
                            </span>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mt-1">{{ $st->bull_name }}</h3>
                            <p class="text-[11px] text-slate-500">{{ $st->producer ?: 'BBIB Singosari' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $st->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $st->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[11px]">
                        <div>
                            <span class="block text-[10px] text-slate-500">Kode Bull</span>
                            <strong class="font-mono text-emerald-700 dark:text-emerald-400">{{ $st->effective_kode_bull }}</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Kode Batch</span>
                            <strong class="font-mono text-slate-900 dark:text-white">{{ $st->kode_batch ?: '-' }}</strong>
                        </div>
                    </div>

                    @if($canManageStraws)
                    <div class="flex justify-end gap-1.5 pt-1">
                        <button type="button"
                            @click="editStraw = { id: {{ $st->id }}, breed: '{{ addslashes($st->breed) }}', kode_bull: '{{ addslashes($st->effective_kode_bull) }}', kode_batch: '{{ addslashes($st->kode_batch ?? '') }}', bull_name: '{{ addslashes($st->bull_name) }}', producer: '{{ addslashes($st->producer ?? '') }}', is_active: {{ $st->is_active ? 'true' : 'false' }}, notes: '{{ addslashes($st->notes ?? '') }}' }; openEditModal = true"
                            class="min-h-[34px] px-3 py-1 bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-lg text-[11px] font-bold">
                            Edit
                        </button>
                        <form action="{{ route('straws.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Hapus straw ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="min-h-[34px] px-3 py-1 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-lg text-[11px] font-bold">
                                Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">Belum ada master straw pejantan terdaftar.</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Rumpun</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kode Bull</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kode Batch</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Pejantan</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Produsen / Sumber</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        @if($canManageStraws)
                            <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($straws as $st)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3.5 font-extrabold text-teal-800 dark:text-teal-300">{{ $st->breed }}</td>
                        <td class="px-4 py-3.5 font-mono font-bold text-emerald-700 dark:text-emerald-400">{{ $st->effective_kode_bull }}</td>
                        <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300">{{ $st->kode_batch ?: '-' }}</td>
                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">{{ $st->bull_name }}</td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">{{ $st->producer ?: 'BBIB Singosari' }}</td>
                        @php
                            $strawStatusClass = $st->is_active
                                ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700';
                        @endphp
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $strawStatusClass }}">
                                {{ $st->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        @if($canManageStraws)
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                    @click="editStraw = { id: {{ $st->id }}, breed: '{{ addslashes($st->breed) }}', kode_bull: '{{ addslashes($st->effective_kode_bull) }}', kode_batch: '{{ addslashes($st->kode_batch ?? '') }}', bull_name: '{{ addslashes($st->bull_name) }}', producer: '{{ addslashes($st->producer ?? '') }}', is_active: {{ $st->is_active ? 'true' : 'false' }}, notes: '{{ addslashes($st->notes ?? '') }}' }; openEditModal = true"
                                    class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-lg border border-amber-200 dark:border-amber-800" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('straws.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Hapus straw ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 rounded-lg border border-rose-200 dark:border-rose-800" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canManageStraws ? 7 : 6 }}" class="px-6 py-8 text-center text-slate-500">Belum ada master straw pejantan terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($canManageStraws)
    {{-- Modal Tambah Master Straw --}}
    <div x-show="openAddModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openAddModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2.5">
                <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">Tambah Master Identitas Pejantan</h2>
                <button type="button" @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('straws.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">1. Rumpun Pejantan <span class="text-rose-500">*</span></label>
                    <select name="breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        @foreach($breeds as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">2. Kode Bull <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_bull" required placeholder="LIM-8890" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">3. Kode Batch</label>
                        <input type="text" name="kode_batch" placeholder="B26-014" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pejantan (Opsional)</label>
                    <input type="text" name="bull_name" placeholder="Mis: Bima Limosin Super" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai IB (Opsional)</label>
                    <input type="text" name="producer" value="BBIB Singosari" placeholder="BBIB Singosari / BIB Lembang" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="openAddModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-extrabold">Simpan Straw</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Master Straw --}}
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openEditModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2.5">
                <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">Edit Master Identitas Pejantan</h2>
                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'/straws/' + editStraw.id" method="POST" class="space-y-3.5 text-xs">
                @csrf @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">1. Rumpun Pejantan <span class="text-rose-500">*</span></label>
                    <select name="breed" x-model="editStraw.breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        @foreach($breeds as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">2. Kode Bull <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_bull" x-model="editStraw.kode_bull" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">3. Kode Batch</label>
                        <input type="text" name="kode_batch" x-model="editStraw.kode_batch" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pejantan</label>
                    <input type="text" name="bull_name" x-model="editStraw.bull_name" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai IB</label>
                    <input type="text" name="producer" x-model="editStraw.producer" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="openEditModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-extrabold">Perbarui Straw</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
