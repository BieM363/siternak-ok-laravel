@extends('layouts.app')

@section('title', 'Rekapitulasi Kinerja IB, Kebuntingan & Kelahiran')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                <span>Rekapitulasi Kinerja IB, Kebuntingan &amp; Kelahiran</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Rekap total sapi di-IB, total bunting, dan total kelahiran/pedet per ras sapi. Output ekspor Excel (.xlsx) &amp; PDF otomatis mengikuti filter aktif.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('reports.excel', request()->query()) }}"
               class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-file-excel"></i>
                <span>Ekspor Excel (.xlsx)</span>
            </a>
            <a href="{{ route('reports.pdf', request()->query()) }}" target="_blank" rel="noopener noreferrer"
               class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    {{-- Angka Rekap Utama --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 tabular-nums">
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 uppercase">Total Sapi di-IB</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalIb) }}</p>
            <p class="text-[10px] text-slate-500">Pelayanan IB tercatat</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border-2 border-emerald-600/40">
            <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Total Bunting</p>
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ number_format($totalActiveBunting) }}</p>
            <p class="text-[10px] text-slate-500">Terkonfirmasi: {{ number_format($totalConfirmedBunting) }} (CR {{ $conceptionRate }}%)</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-teal-700 dark:text-teal-400 uppercase">Total Kelahiran / Pedet</p>
            <p class="text-2xl font-black text-teal-700 dark:text-teal-400 mt-1">{{ number_format(count($bornCalves)) }}</p>
            <p class="text-[10px] text-slate-500">CvR {{ $calvingRate }}%</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-sky-700 dark:text-sky-400 uppercase">Pedet Jantan</p>
            <p class="text-2xl font-black text-sky-700 dark:text-sky-400 mt-1">{{ number_format($maleCalvesCount) }}</p>
            <p class="text-[10px] text-slate-500">Ekor lahir</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 col-span-2 lg:col-span-1">
            <p class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase">Pedet Betina</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($femaleCalvesCount) }}</p>
            <p class="text-[10px] text-slate-500">Calon akseptor baru</p>
        </div>
    </div>

    {{-- Filter Lengkap (Kab/Kota, Inseminator, Periode Tanggal, Status NIK Pemilik, Ras) --}}
    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <div>
                    <label for="rep-regency" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kabupaten/Kota</label>
                    <select id="rep-regency" name="regency_id" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">-- Semua Kabupaten/Kota --</option>
                        @foreach($regencies as $reg)
                            <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label for="rep-officer" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Petugas Inseminator</label>
                <select id="rep-officer" name="officer_name" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $ins)
                        <option value="{{ $ins }}" {{ $officerName == $ins ? 'selected' : '' }}>{{ $ins }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="rep-nik-status" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status NIK Pemilik</label>
                <select id="rep-nik-status" name="nik_status" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Semua Pemilik --</option>
                    <option value="with_nik" {{ $nikStatus === 'with_nik' ? 'selected' : '' }}>Ada NIK KTP</option>
                    <option value="without_nik" {{ $nikStatus === 'without_nik' ? 'selected' : '' }}>Tanpa NIK</option>
                </select>
            </div>

            <div>
                <label for="rep-breed" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenis / Ras Sapi</label>
                <select id="rep-breed" name="breed" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Semua Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ $breedFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="rep-start" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Periode Dari Tanggal</label>
                <input id="rep-start" type="date" name="start_date" value="{{ $startDate }}" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white tabular-nums">
            </div>

            <div>
                <label for="rep-end" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Sampai Tanggal</label>
                <input id="rep-end" type="date" name="end_date" value="{{ $endDate }}" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white tabular-nums">
            </div>

            <div class="sm:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 min-h-[38px] py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl transition">
                    Terapkan Filter Rekap
                </button>
                <a href="{{ route('reports.index') }}" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Rekapitulasi Rinci Per Ras/Jenis Sapi --}}
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-table-list text-emerald-600 dark:text-emerald-400"></i>
                <span>Rekapitulasi Per Jenis / Ras Sapi (Total IB, Bunting &amp; Kelahiran)</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs tabular-nums">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3 px-4">Jenis / Ras Sapi</th>
                        <th class="py-3 px-4 text-center">Total Sapi di-IB</th>
                        <th class="py-3 px-4 text-center">Bunting Aktif</th>
                        <th class="py-3 px-4 text-center">Kelahiran / Pedet</th>
                        <th class="py-3 px-4 text-center">Tidak Bunting / Gagal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($breedRecap as $row)
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $row->breed }}</td>
                        <td class="py-3 px-4 text-center font-extrabold text-slate-900 dark:text-white">{{ number_format($row->total_ib) }}</td>
                        <td class="py-3 px-4 text-center font-extrabold text-emerald-700 dark:text-emerald-400">{{ number_format($row->total_bunting) }}</td>
                        <td class="py-3 px-4 text-center font-extrabold text-teal-700 dark:text-teal-400">{{ number_format($row->total_lahir) }}</td>
                        <td class="py-3 px-4 text-center font-semibold text-rose-600 dark:text-rose-400">{{ number_format($row->total_gagal) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data rekap untuk filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rincian Kelahiran Pedet --}}
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 tabular-nums">
                <i class="fa-solid fa-baby-carriage text-emerald-600 dark:text-emerald-400"></i>
                <span>Rincian Data Kelahiran Pedet ({{ count($bornCalves) }})</span>
            </h2>
            <a href="{{ route('calves.index', request()->query()) }}" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                Buka Menu Pedet &amp; Kelahiran &rarr;
            </a>
        </div>

        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($bornCalves as $calf)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="font-mono font-bold text-emerald-700 dark:text-emerald-400 text-[11px] break-all">{{ $calf->calf_nik }}</span>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $calf->name }}</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase shrink-0 {{ $calf->gender == 'Jantan' ? 'bg-sky-50 text-sky-800 border border-sky-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            {{ $calf->gender }} ({{ $calf->breed ?? '-' }})
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Lahir: {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }} · Pemilik: {{ $calf->motherCow->farmer->name ?? '-' }} · Inseminator: {{ $calf->pregnancyRecord->officer_name ?? '-' }}
                    </p>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Belum ada data kelahiran pedet sesuai filter.</div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3 px-4">NIK Pedet</th>
                        <th class="py-3 px-4">Nama Pedet</th>
                        <th class="py-3 px-4">Ras &amp; Gender</th>
                        <th class="py-3 px-4">Tanggal Lahir</th>
                        <th class="py-3 px-4">Induk &amp; Pemilik</th>
                        <th class="py-3 px-4">Inseminator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($bornCalves as $calf)
                    <tr>
                        <td class="py-3 px-4 font-mono font-bold text-emerald-700 dark:text-emerald-400">{{ $calf->calf_nik }}</td>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $calf->name }}</td>
                        <td class="py-3 px-4">{{ $calf->breed ?? '-' }} · <strong>{{ $calf->gender }}</strong></td>
                        <td class="py-3 px-4 tabular-nums">{{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}</td>
                        <td class="py-3 px-4">
                            <strong>{{ $calf->motherCow->name ?? '-' }}</strong> · {{ $calf->motherCow->farmer->name ?? '-' }} ({{ $calf->motherCow->regency->name ?? '-' }})
                        </td>
                        <td class="py-3 px-4 font-semibold">{{ $calf->pregnancyRecord->officer_name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada data kelahiran pedet sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
