@extends('layouts.app')

@section('title', 'Data Sapi Betina Induk')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-cow text-emerald-500"></i>
                Populasi Sapi Betina {{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? 'IB Saya' : 'Dinas' }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                @if(auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin())
                Menampilkan khusus sapi betina yang di-Inseminasi Buatan (IB) oleh <strong class="text-emerald-600 dark:text-emerald-400">{{ auth()->user()->name }}</strong>.
                @else
                Seluruh ternak utama dalam sistem ini adalah sapi betina induk yang dipantau siklus kebuntingannya.
                @endif
            </p>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('cows.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2 flex-shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Sapi Induk Baru</span>
        </a>
        @endif
    </div>

    <!-- Search Form -->
    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('cows.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK Sapi, Nama Sapi, NIK Peternak, Nama Peternak..." 
                    class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-search"></i>
                <span>Cari</span>
            </button>
            @if($search)
            <a href="{{ route('cows.index') }}" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Data Table Sapi -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi & Nama</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jenis Sapi</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Peternak Pemilik (NIK & Nama)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Wilayah Dinas</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status Kebuntingan</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 bg-white dark:bg-slate-900/30">
                    @forelse($cows as $cow)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-3.5">
                            <a href="{{ route('cows.show', $cow->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors text-xs flex items-center gap-1.5">
                                {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                            </a>
                            <span class="font-mono text-[11px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-slate-800 mt-1 inline-block font-semibold">
                                {{ $cow->cow_nik }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 font-semibold">{{ $cow->breed }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $cow->farmer->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">NIK: {{ $cow->farmer->nik ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                            <strong class="text-slate-800 dark:text-slate-300">{{ $cow->regency->name ?? '-' }}</strong>
                            <span class="block text-[10px]">Kec. {{ $cow->district->name ?? '-' }}, Desa {{ $cow->village->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $badgeClass = match($cow->current_status) {
                                    'Bunting' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-600',
                                    'Pasca IB' => 'bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border-blue-300 dark:border-blue-600',
                                    'Siap Melahirkan' => 'bg-amber-100 dark:bg-orange-950 text-amber-800 dark:text-orange-300 border-amber-300 dark:border-orange-600',
                                    'Mendekati Melahirkan' => 'bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 border-purple-300 dark:border-purple-600',
                                    'Pasca Melahirkan' => 'bg-purple-50 dark:bg-purple-900/50 text-purple-700 dark:text-purple-200 border-purple-200 dark:border-purple-500',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                {{ $cow->current_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('cows.show', $cow->id) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-[11px] font-semibold border border-slate-300 dark:border-slate-700 transition">
                                <i class="fa-solid fa-eye mr-1 text-emerald-500"></i> Detail HPL
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-cow text-4xl mb-3 block text-slate-300 dark:text-slate-700"></i>
                            <p class="text-sm font-semibold">Belum ada data sapi betina{{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? ' yang di-IB oleh Anda' : '' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cows->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
            {{ $cows->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
