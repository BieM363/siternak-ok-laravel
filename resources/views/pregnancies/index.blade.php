@extends('layouts.app')

@section('title', 'Data Kebuntingan Sapi Aktif')

@section('content')
<div class="space-y-6">

    {{-- Header & Tombol Ekspor Excel --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-heart-pulse text-emerald-600 dark:text-emerald-400"></i>
                <span>Data Kebuntingan Sapi Aktif</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Daftar khusus sapi betina yang sedang bunting, dilengkapi umur kebuntingan otomatis (bulan) dari Tanggal Inseminasi, identitas pejantan, dan filter pemilik tanpa NIK.
            </p>
        </div>

        <a href="{{ route('pregnancies.excel', request()->query()) }}"
           class="min-h-[40px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shrink-0 whitespace-nowrap">
            <i class="fa-solid fa-file-excel"></i>
            <span>Ekspor Excel (.xlsx)</span>
        </a>
    </div>

    {{-- Ringkasan Angka Kebuntingan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 tabular-nums">
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Total Sapi Bunting</p>
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ number_format($totalBunting) }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Sedang dalam siklus kebuntingan</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Menunggu PKB 90 Hr</p>
            <p class="text-2xl font-black text-sky-700 dark:text-sky-400 mt-1">{{ number_format($totalTahapPkb90) }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Tahap 1 &rarr; Tahap 2 (3 Bulan)</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bunting Terkonfirmasi</p>
            <p class="text-2xl font-black text-teal-700 dark:text-teal-400 mt-1">{{ number_format($totalTerkonfirmasi) }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Lulus PKB 90 Hari</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bulan ke-9 (Lahir)</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalBulan9) }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Tahap 3 Konfirmasi Kelahiran</p>
        </div>
    </div>

    {{-- Rincian Per Rumpun Pejantan / Ras --}}
    @if($breedSummary->count() > 0)
    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
            Sebaran Rumpun / Ras Sapi Bunting Aktif
        </p>
        <div class="flex flex-wrap gap-2 tabular-nums">
            @foreach($breedSummary as $breedName => $count)
                <span class="px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-900 dark:text-emerald-300">
                    {{ $breedName }}: <strong class="font-black">{{ $count }}</strong> ekor
                </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filter Data Kebuntingan --}}
    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('pregnancies.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama sapi, NIK, kode bull, pemilik..."
                class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">

            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <select name="regency_id" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            @endif

            @if(!auth()->check() || !(auth()->user()->isOperator() && !auth()->user()->isAdmin()))
                <select name="officer_name" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $off)
                        <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                    @endforeach
                </select>
            @endif

            <select name="breed" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                <option value="">-- Semua Rumpun / Ras --</option>
                @foreach($breeds as $b)
                    <option value="{{ $b }}" {{ $breedFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>

            <select name="nik_status" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                <option value="">-- Status NIK Pemilik (Semua) --</option>
                <option value="with_nik" {{ $nikStatus === 'with_nik' ? 'selected' : '' }}>Pemilik Memiliki NIK</option>
                <option value="without_nik" {{ $nikStatus === 'without_nik' ? 'selected' : '' }}>Pemilik Tanpa NIK</option>
            </select>

            <input type="date" name="start_date" value="{{ $startDate }}" title="Dari Tanggal Inseminasi"
                class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">

            <input type="date" name="end_date" value="{{ $endDate }}" title="Sampai Tanggal Inseminasi"
                class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('pregnancies.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Data Kebuntingan (Mobile Cards + Desktop Table) --}}
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($pregnancies as $rec)
                @php $cow = $rec->cow; $farmer = $cow?->farmer; @endphp
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 font-extrabold text-[10px] border border-emerald-200 dark:border-emerald-800">
                                Umur: {{ $rec->pregnancy_age_label }}
                            </span>
                            <span class="ml-1 font-mono text-[10px] text-slate-500">IB ke-{{ $rec->ib_ke ?? 1 }}</span>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mt-1 truncate">
                                {{ $cow->name }} <span class="font-normal text-xs text-slate-500">({{ $cow->breed }})</span>
                            </h3>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                Pemilik: <strong class="text-slate-800 dark:text-slate-200">{{ $farmer->name ?? '-' }}</strong>
                                @if(!$farmer?->nik)
                                    <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-[9px] font-bold">Tanpa NIK</span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[34px] px-3 py-1 bg-emerald-600 text-white font-bold text-[11px] rounded-lg shrink-0 inline-flex items-center">
                            Detail
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[11px]">
                        <div>
                            <span class="block text-[10px] text-slate-500">Pejantan (Straw)</span>
                            <strong class="text-slate-900 dark:text-white">{{ $rec->straw_identity_label }}</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Lokasi</span>
                            <strong class="text-slate-900 dark:text-white">{{ $cow->regency->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Tgl Inseminasi</span>
                            <strong class="text-slate-900 dark:text-white tabular-nums">{{ $rec->insemination_date?->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Inseminator</span>
                            <strong class="text-emerald-700 dark:text-emerald-400">{{ $rec->officer_name ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">Belum ada data sapi bunting aktif sesuai filter.</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Sapi Betina &amp; IB ke-</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Umur Kebuntingan</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Identitas Pejantan (Straw)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Pemilik &amp; Status NIK</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Lokasi</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Inseminator</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status Tahap</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($pregnancies as $rec)
                        @php $cow = $rec->cow; $farmer = $cow?->farmer; $badge = $rec->status_badge; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3.5">
                                <span class="font-extrabold text-slate-900 dark:text-white block">{{ $cow->name }}</span>
                                <span class="font-mono text-[11px] text-emerald-700 dark:text-emerald-400 tabular-nums">{{ $cow->cow_nik }}</span>
                                <span class="ml-1 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold">IB ke-{{ $rec->ib_ke ?? 1 }}</span>
                            </td>
                            <td class="px-4 py-3.5 tabular-nums whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-extrabold">
                                    {{ $rec->pregnancy_age_label }}
                                </span>
                                <span class="block text-[10px] text-slate-500 mt-1">
                                    IB: {{ $rec->insemination_date?->format('d M Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $rec->straw_rumpun ?: ($cow->breed ?? '-') }}</span>
                                <span class="font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                    Bull: {{ $rec->straw_kode_bull ?: ($rec->semen_code ?: '-') }}
                                    @if($rec->straw_kode_batch) · Batch: {{ $rec->straw_kode_batch }} @endif
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $farmer->name ?? '-' }}</span>
                                @if($farmer && $farmer->nik)
                                    <span class="font-mono text-[10px] text-slate-500 tabular-nums">
                                        {{ (auth()->check() && !auth()->user()->isGuest()) ? $farmer->nik : substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4) }}
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-bold">
                                        Tanpa NIK
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400">
                                <strong class="text-slate-800 dark:text-slate-200 block">{{ $cow->regency->name ?? '-' }}</strong>
                                <span class="text-[11px]">Kec. {{ $cow->district->name ?? '-' }}, {{ $cow->village->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $rec->officer_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-block px-2 py-1 rounded-lg text-[10px] font-bold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[32px] px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-[11px] inline-flex items-center transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada data sapi bunting aktif sesuai filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pregnancies->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                {{ $pregnancies->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
