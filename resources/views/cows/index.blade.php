@extends('layouts.app')

@section('title', 'Data Sapi Betina (Akseptor IB)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400"></i>
                <span>Populasi Sapi Betina {{ auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin() ? 'IB Saya' : '(Akseptor IB)' }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                @if(auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin())
                    Menampilkan seluruh sapi betina yang pernah Anda inseminasi (lintas kabupaten).
                @else
                    Daftar sapi betina akseptor yang dipantau siklus kebuntingannya, mendukung IB berulang (IB ke-2, dst.) dan filter pemilik tanpa NIK.
                @endif
            </p>
        </div>

        @if(auth()->check() && auth()->user()->canOperate())
        <a href="{{ route('cows.create') }}" class="min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shrink-0 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i>
            <span>{{ auth()->user()->isAdmin() ? 'Input IB Susulan' : 'Input Laporan IB' }}</span>
        </a>
        @endif
    </div>

    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('cows.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK Sapi, Nama Sapi, Pemilik..."
                class="w-full min-h-[38px] px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">

            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <select name="regency_id" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="breed" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                <option value="">-- Semua Jenis Sapi --</option>
                @foreach($breeds as $b)
                    <option value="{{ $b }}" {{ $breedFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>

            <select name="nik_status" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                <option value="">-- Status NIK Pemilik --</option>
                <option value="with_nik" {{ $nikStatus === 'with_nik' ? 'selected' : '' }}>Pemilik Ada NIK</option>
                <option value="without_nik" {{ $nikStatus === 'without_nik' ? 'selected' : '' }}>Pemilik Tanpa NIK</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-bold transition">Filter</button>
                <a href="{{ route('cows.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl flex items-center justify-center" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($cows as $cow)
            @php
                $badgeClass = match($cow->current_status) {
                    'Bunting', 'Bunting (PKB 90 Hari)' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
                    'Pasca IB' => 'bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-700',
                    'Siap Melahirkan', 'Mendekati Melahirkan' => 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-700',
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
                    <a href="{{ route('cows.show', $cow->id) }}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-emerald-700 block">
                        {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">({{ $cow->breed }})</span>
                    </a>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Pemilik: <strong class="text-slate-900 dark:text-white">{{ $cow->farmer->name ?? '-' }}</strong>
                        @if($cow->farmer && !$cow->farmer->nik)
                            <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-[9px] font-bold">Tanpa NIK</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $cow->regency->name ?? '-' }} &bull; Kec. {{ $cow->district->name ?? '-' }} · Total IB: {{ $cow->pregnancyRecords->count() }}x
                    </p>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ route('cows.show', $cow->id) }}" class="flex-1 min-h-[38px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700">
                        <i class="fa-solid fa-eye text-emerald-600"></i> Detail
                    </a>
                    @if(auth()->check() && auth()->user()->canOperate() && $cow->canReceiveNextIb())
                        <a href="{{ route('cows.create', ['cow_id' => $cow->id]) }}" class="min-h-[38px] inline-flex items-center justify-center gap-1 px-3 py-2 bg-emerald-600 text-white rounded-xl text-xs font-extrabold">
                            + IB ke-{{ $cow->getNextIbNumber() }}
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-slate-500 text-xs">Belum ada data sapi betina sesuai filter.</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi &amp; Nama</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Jenis / Ras</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Pemilik &amp; Status NIK</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Riwayat IB &amp; Status</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($cows as $cow)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-3.5">
                            <a href="{{ route('cows.show', $cow->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-700 text-xs block">
                                {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                            </a>
                            <span class="font-mono text-[11px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-slate-800 mt-1 inline-block font-semibold tabular-nums">
                                {{ $cow->cow_nik }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 font-semibold">{{ $cow->breed }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $cow->farmer->name ?? '-' }}</p>
                            @if($cow->farmer && $cow->farmer->nik)
                                @if(auth()->check() && !auth()->user()->isGuest())
                                    <p class="text-[10px] text-slate-500 font-mono tabular-nums">NIK: {{ $cow->farmer->nik }}</p>
                                @endif
                            @else
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-bold">Tanpa NIK</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                            <strong class="text-slate-800 dark:text-slate-300">{{ $cow->regency->name ?? '-' }}</strong>
                            <span class="block text-[10px]">Kec. {{ $cow->district->name ?? '-' }}, {{ ($cow->regency && str_contains(strtolower($cow->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $cow->village->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold mr-1">{{ $cow->pregnancyRecords->count() }}x IB</span>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase border bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700">
                                {{ $cow->current_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                @if(auth()->check() && auth()->user()->canOperate() && $cow->canReceiveNextIb())
                                    <a href="{{ route('cows.create', ['cow_id' => $cow->id]) }}" class="min-h-[32px] px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-extrabold inline-flex items-center">
                                        + IB ke-{{ $cow->getNextIbNumber() }}
                                    </a>
                                @endif
                                <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[32px] px-3 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-lg text-[11px] font-semibold border border-slate-300 dark:border-slate-700 inline-flex items-center">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data sapi betina sesuai filter.</td>
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
