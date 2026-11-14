@extends('layouts.app')

@section('title', 'Data Sapi Betina Induk')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400"></i>
                Populasi Sapi Betina {{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? 'IB Saya' : 'Dinas' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                @if(auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin())
                Menampilkan khusus sapi betina yang di-Inseminasi Buatan (IB) oleh <strong class="text-emerald-700 dark:text-emerald-400">{{ auth()->user()->name }}</strong>.
                @else
                Seluruh ternak utama dalam sistem ini adalah sapi betina induk yang dipantau siklus kebuntingannya.
                @endif
            </p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('cows.create') }}" class="min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center space-x-2 flex-shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Sapi Induk Baru</span>
        </a>
        @endif
    </div>

    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('cows.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label for="cow-search" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pencarian Data</label>
                <div class="relative">
                    <input id="cow-search" type="text" name="search" value="{{ $search }}" placeholder="Cari NIK Sapi, Nama Sapi, Peternak..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="min-h-[40px] px-5 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-filter"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('cows.index') }}" class="min-h-[40px] px-4 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition flex items-center whitespace-nowrap">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($cows as $cow)
            @php
                $badgeClass = match($cow->current_status) {
                    'Bunting', 'Bunting (PKB 90 Hari)' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
                    'Pasca IB' => 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-700',
                    'Siap Melahirkan' => 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-700',
                    'Mendekati Melahirkan' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-200 border-amber-300 dark:border-amber-600',
                    'Pasca Melahirkan' => 'bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border-teal-200 dark:border-teal-700',
                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700',
                };
            @endphp
            <div class="p-4 space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-mono text-xs text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-slate-800 font-bold tabular-nums break-all">
                        {{ $cow->cow_nik }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase border shrink-0 {{ $badgeClass }}">
                        {{ $cow->current_status }}
                    </span>
                </div>

                <div>
                    <a href="{{ route('cows.show', $cow->id) }}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors block">
                        {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">({{ $cow->breed }})</span>
                    </a>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Peternak: <strong class="text-slate-900 dark:text-white">{{ $cow->farmer->name ?? '-' }}</strong>
                        @if(auth()->check() && !auth()->user()->isGuest())
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono tabular-nums">(NIK: {{ $cow->farmer->nik ?? '-' }})</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $cow->regency->name ?? '-' }} &bull; Kec. {{ $cow->district->name ?? '-' }}, {{ ($cow->regency && str_contains(strtolower($cow->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $cow->village->name ?? '-' }}
                    </p>
                </div>

                <div class="pt-1">
                    <a href="{{ route('cows.show', $cow->id) }}" class="w-full min-h-[38px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700 transition">
                        <i class="fa-solid fa-eye text-emerald-600 dark:text-emerald-400"></i> Lihat Kartu Ternak &amp; Detail HPL
                    </a>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-cow text-3xl mb-2 block text-slate-300 dark:text-slate-700"></i>
                <p class="text-xs font-semibold">Belum ada data sapi betina{{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? ' yang di-IB oleh Anda' : '' }}.</p>
            </div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi &amp; Nama</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jenis Sapi</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Peternak Pemilik {{ auth()->check() && !auth()->user()->isGuest() ? '(NIK & Nama)' : '' }}</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Wilayah Dinas</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status Kebuntingan</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($cows as $cow)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-3.5">
                            <a href="{{ route('cows.show', $cow->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors text-xs flex items-center gap-1.5">
                                {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                            </a>
                            <span class="font-mono text-[11px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-slate-800 mt-1 inline-block font-semibold tabular-nums">
                                {{ $cow->cow_nik }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 font-semibold">{{ $cow->breed }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $cow->farmer->name ?? '-' }}</p>
                            @if(auth()->check() && !auth()->user()->isGuest())
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tabular-nums">NIK: {{ $cow->farmer->nik ?? '-' }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                            <strong class="text-slate-800 dark:text-slate-300">{{ $cow->regency->name ?? '-' }}</strong>
                            <span class="block text-[10px]">Kec. {{ $cow->district->name ?? '-' }}, {{ ($cow->regency && str_contains(strtolower($cow->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $cow->village->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $badgeClass = match($cow->current_status) {
                                    'Bunting', 'Bunting (PKB 90 Hari)' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
                                    'Pasca IB' => 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-700',
                                    'Siap Melahirkan' => 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-700',
                                    'Mendekati Melahirkan' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-200 border-amber-300 dark:border-amber-600',
                                    'Pasca Melahirkan' => 'bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border-teal-200 dark:border-teal-700',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                {{ $cow->current_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('cows.show', $cow->id) }}" class="inline-flex items-center min-h-[34px] px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-[11px] font-semibold border border-slate-300 dark:border-slate-700 transition">
                                <i class="fa-solid fa-eye mr-1 text-emerald-600 dark:text-emerald-400"></i> Detail HPL
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-cow text-3xl mb-3 block text-slate-300 dark:text-slate-700"></i>
                            <p class="text-sm font-semibold">Belum ada data sapi betina{{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? ' yang di-IB oleh Anda' : '' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cows->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
            {{ $cows->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
