@extends('layouts.app')

@section('title', 'Leaderboard Inseminator')

@section('content')
<div class="space-y-6">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                Leaderboard Inseminator (Data Pelayanan IB Terbanyak)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Rangking performansi inseminator berdasarkan jumlah pelayanan Inseminasi Buatan (IB), hasil kebuntingan, dan nilai Conception Rate (CR %).</p>
        </div>

        <div class="flex items-center space-x-2 flex-shrink-0">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition flex items-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-file-invoice text-emerald-500"></i>
                <span>Rekapan Kelahiran Pedet</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Inseminator Active -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-amber-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Inseminator</p>
                <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ count($inseminatorRecap) }} <span class="text-xs font-normal text-slate-500">Petugas</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-user-doctor text-lg"></i>
            </div>
        </div>

        <!-- Total Inseminasi Buatan (IB) -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-blue-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Total Pelayanan IB</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($totalIbAll) }} <span class="text-xs font-normal text-slate-500">Ternak</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-syringe text-lg"></i>
            </div>
        </div>

        <!-- Total Sapi Bunting Positif -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-emerald-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">Sapi Bunting Positif</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalBuntingAll) }} <span class="text-xs font-normal text-slate-500">Ekor</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class="fa-solid fa-notes-medical text-lg"></i>
            </div>
        </div>

        <!-- Rata-rata CR % -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-teal-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-teal-700 dark:text-teal-300 uppercase tracking-wider">Rata-rata CR %</p>
                <h3 class="text-2xl font-extrabold text-teal-600 dark:text-teal-400 mt-1">{{ $avgCr }}%</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-600 dark:text-teal-400">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar Inseminator -->
    <div class="glass-card p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <form method="GET" action="{{ route('reports.inseminators') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Cari Nama Inseminator</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama petugas inseminator..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 pl-9 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Wilayah Kabupaten</label>
                <select name="regency_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Kabupaten --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ request('regency_id') == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                    Cari Inseminator
                </button>
            </div>
        </form>
    </div>

    <!-- Leaderboard Inseminator Table Card -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-100 dark:bg-slate-900/50">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                Peringkat Performansi Inseminator (Terbanyak ke Tersedikit)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3 px-4 text-center w-16 whitespace-nowrap">Peringkat</th>
                        <th class="py-3 px-4 whitespace-nowrap">Nama Inseminator / Petugas</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Total Pelayanan IB</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Sapi Bunting Positif</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Pedet Lahir Selesai</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Conception Rate (CR %)</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status Performansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 text-xs bg-white dark:bg-slate-900/30">
                    @forelse($inseminatorRecap as $index => $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 text-center font-bold">
                            @if($index == 0)
                                <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 inline-flex items-center justify-center font-black text-sm shadow">1</span>
                            @elseif($index == 1)
                                <span class="w-7 h-7 rounded-full bg-slate-300 text-slate-900 inline-flex items-center justify-center font-black text-sm shadow">2</span>
                            @elseif($index == 2)
                                <span class="w-7 h-7 rounded-full bg-amber-700 text-white inline-flex items-center justify-center font-black text-sm shadow">3</span>
                            @else
                                <span class="text-slate-500 dark:text-slate-400 font-bold">#{{ $index + 1 }}</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center text-xs">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <div>
                                <span class="text-sm font-extrabold">{{ $row->officer_name }}</span>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Inseminator Terdaftar</p>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center font-extrabold text-blue-600 dark:text-blue-400 text-base">
                            {{ number_format($row->total_ib) }}
                        </td>

                        <td class="py-4 px-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400 text-base">
                            {{ number_format($row->total_bunting) }}
                        </td>

                        <td class="py-4 px-4 text-center font-extrabold text-purple-600 dark:text-purple-400 text-base">
                            {{ number_format($row->total_melahirkan) }}
                        </td>

                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $row->cr >= 60 ? 'bg-emerald-100 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300' : ($row->cr >= 40 ? 'bg-amber-100 dark:bg-amber-900/80 text-amber-800 dark:text-amber-300' : 'bg-rose-100 dark:bg-rose-900/80 text-rose-800 dark:text-rose-300') }}">
                                {{ $row->cr }}%
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            @if($row->cr >= 60)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 text-[10px] font-extrabold uppercase">
                                    Sangat Baik
                                </span>
                            @elseif($row->cr >= 40)
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 text-[10px] font-extrabold uppercase">
                                    Cukup
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-700 text-[10px] font-extrabold uppercase">
                                    Perlu Evaluasi
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-trophy text-3xl mb-2 text-slate-300 dark:text-slate-700 block"></i>
                            Belum ada data inseminator recorded yang sesuai dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
