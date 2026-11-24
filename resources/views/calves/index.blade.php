@extends('layouts.app')

@section('title', 'Data Pedet & Kelahiran Terpadu')

@section('content')
<div class="space-y-6">

    {{-- Header Terpadu Pedet & Kelahiran --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-baby-carriage text-emerald-600 dark:text-emerald-400"></i>
                <span>Data Pedet &amp; Kelahiran</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Menu terpadu rekapitulasi kelahiran pedet hasil Inseminasi Buatan (IB), promosi pedet betina menjadi akseptor siap IB, serta ekspor Excel (.xlsx) dan PDF.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('calves.excel', request()->query()) }}"
               class="min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-file-excel"></i>
                <span>Ekspor Excel (.xlsx)</span>
            </a>
            <a href="{{ route('calves.pdf', request()->query()) }}" target="_blank" rel="noopener noreferrer"
               class="min-h-[38px] px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    {{-- Statistik Terpadu Pedet & Kelahiran --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 tabular-nums">
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-bold uppercase text-slate-500">Total Pedet Lahir</p>
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-0.5">{{ number_format($totalCalvesCount) }}</p>
            <p class="text-[10px] text-slate-500">{{ number_format($maleCalvesCount) }} Jantan · {{ number_format($femaleCalvesCount) }} Betina</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-bold uppercase text-slate-500">Total Sapi di-IB</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalIb) }}</p>
            <p class="text-[10px] text-slate-500">Sesuai filter aktif</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-bold uppercase text-slate-500">Total Bunting</p>
            <p class="text-2xl font-black text-teal-700 dark:text-teal-400 mt-0.5">{{ number_format($totalBunting) }}</p>
            <p class="text-[10px] text-slate-500">Terkonfirmasi / Melahirkan</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-bold uppercase text-slate-500">Conception Rate (CR)</p>
            <p class="text-2xl font-black text-sky-700 dark:text-sky-400 mt-0.5">{{ $conceptionRate }}%</p>
            <p class="text-[10px] text-slate-500">Bunting / Total IB</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 col-span-2 lg:col-span-1">
            <p class="text-[10px] font-bold uppercase text-slate-500">Calving Rate (CVR)</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ $calvingRate }}%</p>
            <p class="text-[10px] text-slate-500">Lahir / Bunting</p>
        </div>
    </div>

    {{-- Filter Lengkap (Kab/Kota, Inseminator, Periode, Ras, Gender, Status NIK) --}}
    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('calves.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK pedet, nama, induk, pemilik..."
                class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">

            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <select name="regency_id" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="officer_name" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Inseminator --</option>
                @foreach($officers as $off)
                    <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                @endforeach
            </select>

            <select name="nik_status" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Status NIK Pemilik (Semua) --</option>
                <option value="with_nik" {{ $nikStatus === 'with_nik' ? 'selected' : '' }}>Ada NIK</option>
                <option value="without_nik" {{ $nikStatus === 'without_nik' ? 'selected' : '' }}>Tanpa NIK</option>
            </select>

            <input type="date" name="start_date" value="{{ $startDate }}" title="Dari Tanggal Lahir"
                class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white tabular-nums">

            <input type="date" name="end_date" value="{{ $endDate }}" title="Sampai Tanggal Lahir"
                class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-slate-900 dark:text-white tabular-nums">

            <div class="grid grid-cols-2 gap-2">
                <select name="breed" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
                    <option value="">Semua Ras</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ $breedFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
                <select name="gender" class="w-full min-h-[38px] bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
                    <option value="">Gender</option>
                    <option value="Jantan" {{ $genderFilter == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                    <option value="Betina" {{ $genderFilter == 'Betina' ? 'selected' : '' }}>Betina</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl">Filter</button>
                <a href="{{ route('calves.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl flex items-center justify-center" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Pedet & Kelahiran (Mobile Cards + Desktop Table) --}}
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 tabular-nums">
                <i class="fa-solid fa-list text-emerald-600 dark:text-emerald-400"></i>
                <span>Daftar Pedet &amp; Kelahiran ({{ $calves->total() }})</span>
            </h2>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($calves as $calf)
            <div class="p-4 space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-slate-800 tabular-nums break-all">
                        {{ $calf->calf_nik }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase shrink-0 {{ $calf->gender == 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                        {{ $calf->gender }}
                    </span>
                </div>

                <div>
                    <p class="font-extrabold text-sm text-slate-900 dark:text-white">
                        {{ $calf->name }} <span class="text-xs font-semibold text-slate-500">({{ $calf->breed ?? '-' }})</span>
                    </p>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 tabular-nums">
                        Lahir: <strong class="text-slate-900 dark:text-white">{{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}</strong>
                        &bull; Induk: <strong>{{ $calf->motherCow->name ?? 'Induk Sapi' }}</strong>
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Pemilik: {{ $calf->motherCow->farmer->name ?? '-' }} ({{ $calf->motherCow->regency->name ?? '-' }}) &bull; Inseminator: {{ $calf->pregnancyRecord->officer_name ?? '-' }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a href="{{ route('calves.show', $calf->id) }}" class="flex-1 min-h-[36px] inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 rounded-xl text-xs font-bold border border-sky-200 dark:border-sky-800">
                        <i class="fa-solid fa-eye"></i> Detail
                    </a>
                    @if(auth()->check() && auth()->user()->canOperate())
                        <a href="{{ route('calves.edit', $calf->id) }}" class="min-h-[36px] inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-xl text-xs font-bold border border-amber-200 dark:border-amber-800">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        @if($calf->gender == 'Betina' && !$calf->is_promoted)
                        <form action="{{ route('calves.promote', $calf->id) }}" method="POST" onsubmit="return confirm('Promosikan pedet betina ini menjadi Sapi Akseptor Siap IB?');" class="w-full">
                            @csrf
                            <button type="submit" class="w-full min-h-[36px] px-3 py-1.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-angles-up"></i> Tarik ke Sapi Siap IB
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="py-8 px-4 text-center text-xs text-slate-500">Belum ada data pedet &amp; kelahiran sesuai filter.</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3.5 px-4 whitespace-nowrap">NIK Pedet</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Nama Pedet</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Ras &amp; Gender</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Tgl Lahir</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Induk &amp; Pemilik</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Inseminator</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Status Promosi</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs bg-white dark:bg-slate-900">
                    @forelse($calves as $calf)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">{{ $calf->calf_nik }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">{{ $calf->name }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block font-semibold text-slate-800 dark:text-slate-300 mr-1">{{ $calf->breed ?? '-' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $calf->gender == 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                {{ $calf->gender }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold tabular-nums">
                            {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->name ?? 'Induk Sapi' }} <span class="text-[10px] text-slate-500 font-mono">({{ $calf->motherCow->cow_nik ?? '-' }})</span></p>
                            <p class="text-[11px] text-slate-500">
                                {{ $calf->motherCow->farmer->name ?? '-' }}
                                @if($calf->motherCow?->farmer && !$calf->motherCow->farmer->nik)
                                    <span class="px-1 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold">Tanpa NIK</span>
                                @endif
                                · {{ $calf->motherCow->regency->name ?? '-' }}
                            </p>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">{{ $calf->pregnancyRecord->officer_name ?? '-' }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($calf->is_promoted)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700 text-[10px] font-extrabold uppercase">
                                    Promosi Sapi Siap IB
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-semibold">
                                    Belum Dipromosikan
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('calves.show', $calf->id) }}" class="min-w-[32px] min-h-[32px] p-1.5 bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 rounded-lg border border-sky-200 dark:border-sky-800 flex items-center justify-center" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->check() && auth()->user()->canOperate())
                                    <a href="{{ route('calves.edit', $calf->id) }}" class="min-w-[32px] min-h-[32px] p-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-lg border border-amber-200 dark:border-amber-800 flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($calf->gender == 'Betina' && !$calf->is_promoted)
                                        <form action="{{ route('calves.promote', $calf->id) }}" method="POST" onsubmit="return confirm('Promosikan pedet betina ini menjadi Sapi Akseptor Siap IB?');">
                                            @csrf
                                            <button type="submit" class="min-h-[32px] px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white rounded-lg text-[10px] font-extrabold flex items-center gap-1 whitespace-nowrap">
                                                <i class="fa-solid fa-angles-up"></i> Tarik ke Siap IB
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <form action="{{ route('calves.destroy', $calf->id) }}" method="POST" onsubmit="return confirm('Hapus data pedet ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="min-w-[32px] min-h-[32px] p-1.5 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 rounded-lg border border-rose-200 dark:border-rose-800 flex items-center justify-center" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500">Belum ada data pedet &amp; kelahiran sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($calves->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
            {{ $calves->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
