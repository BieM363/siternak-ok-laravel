@extends('layouts.app')

@section('title', 'Rekapan Kelahiran Pedet')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-emerald-500"></i>
                Rekapan Kelahiran Bulanan Pedet (Anak Sapi)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cetak dan rekapitulasi data kelahiran anak sapi hasil Inseminasi Buatan (IB), metrik CR & CvR.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <!-- Link ke Leaderboard Inseminator -->
            <a href="{{ route('reports.inseminators') }}" class="flex-1 sm:flex-initial px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-trophy"></i>
                <span>Leaderboard Inseminator</span>
            </a>

            <!-- PDF Export Button -->
            <a href="{{ route('reports.pdf', request()->query()) }}" target="_blank" class="flex-1 sm:flex-initial px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Export PDF</span>
            </a>

            <!-- Excel Export Button -->
            <a href="{{ route('reports.excel', request()->query()) }}" class="flex-1 sm:flex-initial px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Kelahiran -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-purple-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Kelahiran</p>
                <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ count($bornCalves) }} <span class="text-xs font-normal text-slate-500">Ekor</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fa-solid fa-baby-carriage text-lg"></i>
            </div>
        </div>

        <!-- Anak Sapi Jantan -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-blue-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Pedet Jantan</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $maleCalvesCount }} <span class="text-xs font-normal text-slate-500">Ekor</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-mars text-lg"></i>
            </div>
        </div>

        <!-- Anak Sapi Betina -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-pink-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-pink-700 dark:text-pink-300 uppercase tracking-wider">Pedet Betina</p>
                <h3 class="text-2xl font-extrabold text-pink-600 dark:text-pink-400 mt-1">{{ $femaleCalvesCount }} <span class="text-xs font-normal text-slate-500">Ekor</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-950/80 border border-pink-200 dark:border-pink-800 flex items-center justify-center text-pink-600 dark:text-pink-400">
                <i class="fa-solid fa-venus text-lg"></i>
            </div>
        </div>

        <!-- Conception Rate (CR %) -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-teal-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-teal-700 dark:text-teal-300 uppercase tracking-wider">Conception Rate (CR)</p>
                <h3 class="text-2xl font-extrabold text-teal-600 dark:text-teal-400 mt-1">{{ $conceptionRate }}%</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-600 dark:text-teal-400">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
        </div>

        <!-- Calving Rate (CvR %) -->
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-l-4 border-l-amber-500 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Calving Rate (CvR)</p>
                <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $calvingRate }}%</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-cow text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="glass-card p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Dari Tanggal Lahir</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Sampai Tanggal Lahir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ras / Rumpun</label>
                <select name="breed" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ request('breed') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Petugas Inseminator</label>
                <select name="officer_name" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $ins)
                        <option value="{{ $ins }}" {{ request('officer_name') == $ins ? 'selected' : '' }}>{{ $ins }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekapan Kelahiran Pedet -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-100 dark:bg-slate-900/50">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-baby-carriage text-purple-500"></i>
                Rincian Data Kelahiran Pedet ({{ count($bornCalves) }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3 px-4 whitespace-nowrap">NIK Anak Sapi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Nama Pedet</th>
                        <th class="py-3 px-4 whitespace-nowrap">Ras & Gender</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal Lahir</th>
                        <th class="py-3 px-4 whitespace-nowrap">NIK & Induk Sapi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Peternak & Wilayah</th>
                        <th class="py-3 px-4 whitespace-nowrap">Inseminator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 text-xs bg-white dark:bg-slate-900/30">
                    @forelse($bornCalves as $calf)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700 dark:text-emerald-400">{{ $calf->calf_nik }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">{{ $calf->name }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-slate-800 dark:text-slate-300 mr-1">{{ $calf->breed ?? '-' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $calf->gender == 'Jantan' ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700' : 'bg-pink-100 dark:bg-pink-900/60 text-pink-800 dark:text-pink-300 border border-pink-200 dark:border-pink-700' }}">
                                {{ $calf->gender }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">
                            {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->name ?? 'Induk Sapi' }}</p>
                            <span class="text-[10px] text-slate-400 font-mono">NIK: {{ $calf->motherCow->cow_nik ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->farmer->name ?? '-' }}</p>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $calf->motherCow->district->name ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">
                            <i class="fa-solid fa-user-doctor text-emerald-500 mr-1"></i>{{ $calf->pregnancyRecord->officer_name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500 dark:text-slate-400">Belum ada data kelahiran pedet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
