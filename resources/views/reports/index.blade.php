@extends('layouts.app')

@section('title', 'Rekapan Kelahiran Pedet')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-emerald-600 dark:text-emerald-400"></i>
                Rekapan Kelahiran Bulanan Pedet (Anak Sapi)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cetak dan rekapitulasi data kelahiran anak sapi hasil Inseminasi Buatan (IB), metrik CR &amp; CvR.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('reports.inseminators') }}" class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-trophy"></i>
                <span>Leaderboard Inseminator</span>
            </a>

            <a href="{{ route('reports.pdf', array_merge(['start_date' => $startDate, 'end_date' => $endDate], request()->query())) }}" target="_blank" class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Export PDF</span>
            </a>

            <a href="{{ route('reports.excel', array_merge(['start_date' => $startDate, 'end_date' => $endDate], request()->query())) }}" class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass-card p-4 sm:p-5 rounded-2xl border-2 border-emerald-600/40 dark:border-emerald-500/40 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Total Kelahiran</p>
                <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums">{{ count($bornCalves) }} <span class="text-xs font-normal text-slate-500">Ekor</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400">
                <i class="fa-solid fa-baby-carriage text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-sky-700 dark:text-sky-300 uppercase tracking-wider">Pedet Jantan</p>
                <p class="text-2xl font-extrabold text-sky-700 dark:text-sky-400 mt-1 tabular-nums">{{ $maleCalvesCount }} <span class="text-xs font-normal text-slate-500">Ekor</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400">
                <i class="fa-solid fa-mars text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Pedet Betina</p>
                <p class="text-2xl font-extrabold text-amber-700 dark:text-amber-400 mt-1 tabular-nums">{{ $femaleCalvesCount }} <span class="text-xs font-normal text-slate-500">Ekor</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400">
                <i class="fa-solid fa-venus text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-teal-700 dark:text-teal-300 uppercase tracking-wider">Conception Rate (CR)</p>
                <p class="text-2xl font-extrabold text-teal-700 dark:text-teal-400 mt-1 tabular-nums">{{ $conceptionRate }}%</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Calving Rate (CvR)</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1 tabular-nums">{{ $calvingRate }}%</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-300">
                <i class="fa-solid fa-cow text-lg"></i>
            </div>
        </div>
    </div>

    <div class="glass-card p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="report-start-date" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Dari Tanggal Lahir</label>
                <input id="report-start-date" type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 tabular-nums">
            </div>

            <div>
                <label for="report-end-date" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Sampai Tanggal Lahir</label>
                <input id="report-end-date" type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 tabular-nums">
            </div>

            <div>
                <label for="report-breed" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ras / Rumpun</label>
                <select id="report-breed" name="breed" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <option value="">-- Semua Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ request('breed') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="report-officer" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Petugas Inseminator</label>
                <select id="report-officer" name="officer_name" class="w-full bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $ins)
                        <option value="{{ $ins }}" {{ request('officer_name') == $ins ? 'selected' : '' }}>{{ $ins }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 min-h-[38px] py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition whitespace-nowrap">
                    Terapkan Filter
                </button>
                @if(request()->has('start_date') || request()->has('end_date') || request()->has('breed') || request()->has('officer_name'))
                    <a href="{{ route('reports.index') }}" aria-label="Reset filter ke default" title="Reset Filter ke Default" class="min-h-[38px] px-3 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 tabular-nums">
                <i class="fa-solid fa-baby-carriage text-emerald-600 dark:text-emerald-400"></i>
                Rincian Data Kelahiran Pedet ({{ count($bornCalves) }})
            </h2>
        </div>

        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($bornCalves as $calf)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="inline-block px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-950 font-mono font-bold text-emerald-700 dark:text-emerald-400 border border-slate-200 dark:border-slate-800 text-[11px] tabular-nums break-all">
                                {{ $calf->calf_nik }}
                            </span>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mt-1">
                                {{ $calf->name }}
                            </h3>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase shrink-0 {{ $calf->gender == 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                            {{ $calf->gender }} ({{ $calf->breed ?? '-' }})
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-500">Tgl Lahir</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 tabular-nums">
                                {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-500">Induk Sapi</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $calf->motherCow->name ?? 'Induk Sapi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-500">Peternak</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $calf->motherCow->farmer->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-500">Inseminator</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $calf->pregnancyRecord->officer_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 dark:text-slate-400 text-xs">
                    Belum ada data kelahiran pedet.
                </div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3.5 px-4 whitespace-nowrap">NIK Anak Sapi</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Nama Pedet</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Ras &amp; Gender</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Tanggal Lahir</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">NIK &amp; Induk Sapi</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Peternak &amp; Wilayah</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Inseminator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs bg-white dark:bg-slate-900">
                    @forelse($bornCalves as $calf)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">{{ $calf->calf_nik }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">{{ $calf->name }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-slate-800 dark:text-slate-300 mr-1">{{ $calf->breed ?? '-' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $calf->gender == 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                {{ $calf->gender }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold tabular-nums">
                            {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->name ?? 'Induk Sapi' }}</p>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tabular-nums">NIK: {{ $calf->motherCow->cow_nik ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->farmer->name ?? '-' }}</p>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $calf->motherCow->district->name ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">
                            <i class="fa-solid fa-user-doctor text-emerald-600 dark:text-emerald-400 mr-1"></i>{{ $calf->pregnancyRecord->officer_name ?? '-' }}
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
