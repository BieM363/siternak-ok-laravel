@extends('layouts.app')

@section('title', 'Detail Sapi & Siklus Kebuntingan - ' . $cow->cow_nik)

@section('content')
@php
    $latestRecord = $cow->pregnancyRecords->first();
    $nextIbNum = $cow->getNextIbNumber();
    $canOperate = auth()->check() && auth()->user()->canOperate();
@endphp

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <a href="{{ route('cows.index') }}" class="min-h-[40px] px-4 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700 transition flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Sapi</span>
        </a>

        @if($canOperate)
        <div class="flex flex-wrap items-center gap-2">
            @if($cow->canReceiveNextIb())
                <a href="{{ route('cows.create', ['cow_id' => $cow->id]) }}"
                   class="min-h-[40px] px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-1.5 whitespace-nowrap shadow-sm">
                    <i class="fa-solid fa-repeat"></i>
                    <span>Input IB Lanjutan (IB ke-{{ $nextIbNum }})</span>
                </a>
            @endif
            <a href="{{ route('cows.edit', $cow->id) }}" class="min-h-[40px] px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                <i class="fa-solid fa-pen"></i>
                <span>Edit Sapi</span>
            </a>
            <form action="{{ route('cows.destroy', $cow->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sapi ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="min-h-[40px] px-4 py-2.5 bg-rose-700 hover:bg-rose-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-trash"></i>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kartu Identitas Sapi --}}
        <div class="lg:col-span-1 glass-card p-6 rounded-2xl border border-emerald-600/40 bg-slate-900 flex flex-col justify-between space-y-5 shadow-sm text-white">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-700 flex items-center justify-center text-white text-sm">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xs text-white uppercase tracking-wider">Kartu Ternak Akseptor</h1>
                        <p class="text-[9px] text-emerald-400 font-bold uppercase">Disnakbun Prov. Gorontalo</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 bg-slate-950 text-[10px] font-mono text-emerald-400 font-bold border border-emerald-500/30 rounded">BETINA</span>
            </div>

            <div class="text-center space-y-2 py-1">
                <div class="inline-block p-2 bg-white rounded-xl border border-slate-700">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($cow->cow_nik) }}"
                        alt="QR Code NIK {{ $cow->cow_nik }}" width="120" height="120" class="w-28 h-28 rounded-lg">
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-widest block">NIK Ternak Sapi</span>
                    <p class="font-mono text-sm sm:text-base font-extrabold text-emerald-400 tracking-wider mt-0.5 bg-slate-950 px-3 py-1 rounded-xl border border-slate-800 inline-block tabular-nums break-all">
                        {{ $cow->cow_nik }}
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-2 text-xs">
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">Nama Sapi:</span>
                    <strong class="text-white text-right">{{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}</strong>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">Jenis / Ras:</span>
                    <strong class="text-emerald-300 text-right">{{ $cow->breed }}</strong>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">Pemilik Sapi:</span>
                    <strong class="text-white text-right">{{ $cow->farmer->name ?? '-' }}</strong>
                </div>
                @if(auth()->check() && !auth()->user()->isGuest())
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">NIK Pemilik:</span>
                    <strong class="text-slate-300 font-mono text-right tabular-nums">{{ $cow->farmer->nik ?: 'Tanpa NIK' }}</strong>
                </div>
                @endif
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">No. HP Pemilik:</span>
                    <strong class="text-slate-300 text-right tabular-nums">{{ $cow->farmer->phone ?? '-' }}</strong>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">Wilayah:</span>
                    <strong class="text-slate-200 text-right">{{ $cow->regency->name ?? '-' }}, Kec. {{ $cow->district->name ?? '-' }}, {{ ($cow->regency && str_contains(strtolower($cow->regency->name), 'kota')) ? 'Kel.' : 'Desa' }} {{ $cow->village->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- Milestone 3 Tahap Kebuntingan --}}
        <div class="lg:col-span-2 glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 dark:border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-lg shrink-0">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">Pemantauan Kebuntingan 3 Tahap</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Dihitung dari Tanggal Inseminasi &bull; Hari ke-30, PKB Hari ke-90, Bulan ke-9 (Auto-lanjut 3 hari)</p>
                    </div>
                </div>

                @if($latestRecord)
                    @php $badge = $latestRecord->status_badge; @endphp
                    <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold border {{ $badge['bg'] }}">
                        IB ke-{{ $latestRecord->ib_ke ?? 1 }} · {{ $badge['label'] }}
                    </span>
                @else
                    <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700">
                        Belum Ada Riwayat IB
                    </span>
                @endif
            </div>

            @if($latestRecord)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-100 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 text-xs tabular-nums">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Tanggal Inseminasi</p>
                    <p class="font-bold text-slate-900 dark:text-white">{{ $latestRecord->insemination_date->format('d M Y') }}</p>
                    <p class="text-[10px] text-slate-500">Dilaporkan: {{ $latestRecord->created_at?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-sky-700 dark:text-sky-400 uppercase">Tahap 1 · Hari ke-30</p>
                    <p class="font-bold text-sky-700 dark:text-sky-300">{{ $latestRecord->day30_date?->format('d M Y') ?? '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('30day') }}" target="_blank" rel="noopener noreferrer" class="text-[10px] text-sky-700 dark:text-sky-400 hover:underline block font-semibold">+ Cal 30 Hr</a>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Tahap 2 · PKB 90 Hr</p>
                    <p class="font-bold text-emerald-700 dark:text-emerald-300">{{ $latestRecord->day90_date?->format('d M Y') ?? '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('90day') }}" target="_blank" rel="noopener noreferrer" class="text-[10px] text-emerald-700 dark:text-emerald-400 hover:underline block font-semibold">+ Cal PKB 90 Hr</a>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase">Tahap 3 · Bulan ke-9</p>
                    <p class="font-bold text-amber-700 dark:text-amber-300">{{ ($latestRecord->target_month9_date ?? $latestRecord->estimated_birth_date)?->format('d M Y') ?? '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('birth') }}" target="_blank" rel="noopener noreferrer" class="text-[10px] text-amber-700 dark:text-amber-400 hover:underline block font-bold">+ Cal Kelahiran</a>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-slate-950 p-4 rounded-xl border border-amber-300 dark:border-amber-700/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold text-amber-800 dark:text-amber-300 uppercase">
                        Umur Kebuntingan: <span class="text-emerald-700 dark:text-emerald-400">{{ $latestRecord->pregnancy_age_label }}</span> · Estimasi Lahir (Bulan ke-9):
                    </p>
                    <p class="text-lg font-extrabold text-amber-700 dark:text-amber-400 tabular-nums">{{ $latestRecord->estimated_birth_date->format('d M Y') }}</p>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                        Inseminator: <strong class="text-slate-900 dark:text-white">{{ $latestRecord->officer_name ?? '-' }}</strong>
                        &bull; Pejantan: <strong class="text-teal-700 dark:text-teal-400 font-mono">{{ $latestRecord->straw_identity_label }}</strong>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    @if($canOperate && !in_array($latestRecord->status, ['Gagal/Keguguran', 'Melahirkan (Selesai)']))
                        @if(in_array($latestRecord->status, ['Pasca IB', 'Estimasi 30 Hari', 'Bunting (PKB 90 Hari)']))
                        <form action="{{ route('pregnancy.update-status', $latestRecord->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="confirm_yes">
                            <button type="submit" class="min-h-[38px] px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>Konfirmasi Bunting / Lanjut</span>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('pregnancy.update-status', $latestRecord->id) }}" method="POST" onsubmit="return confirm('Tandai rekam IB ini Tidak Bunting / Gagal? Sapi akan siap masuk alur IB ke-{{ $nextIbNum }}.')">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="confirm_no">
                            <button type="submit" class="min-h-[38px] px-3.5 py-2 bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>Tidak Bunting / Gagal</span>
                            </button>
                        </form>
                    @elseif($canOperate && $cow->canReceiveNextIb())
                        <a href="{{ route('cows.create', ['cow_id' => $cow->id]) }}" class="min-h-[38px] px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-repeat"></i>
                            <span>Input IB ke-{{ $nextIbNum }} Sekarang</span>
                        </a>
                    @endif
                </div>
            </div>
            @else
            <div class="text-center py-8 text-slate-500 space-y-2">
                <i class="fa-solid fa-cow text-3xl text-slate-400"></i>
                <p class="text-sm font-semibold">Sapi betina ini belum memiliki rekam kebuntingan aktif.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Data Pedet Lahir --}}
    @if($cow->calves->count() > 0)
    <div class="glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h2 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <i class="fa-solid fa-baby-carriage text-teal-600 dark:text-teal-400"></i>
            <span>Data Pedet yang Dilahirkan Induk Ini</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($cow->calves as $calf)
            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="font-mono text-xs font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-slate-900 px-2 py-0.5 rounded border border-teal-200 dark:border-slate-800 tabular-nums break-all">
                        {{ $calf->calf_nik }}
                    </span>
                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $calf->name ?? 'Pedet' }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 tabular-nums">Lahir: <strong class="text-slate-800 dark:text-slate-200">{{ $calf->birth_date?->format('d M Y') ?? '-' }}</strong> · Ras: {{ $calf->breed ?? '-' }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 {{ $calf->gender === 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                    {{ $calf->gender }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Cepat Input IB Lanjutan (IB ke-2, dst.) — Tersedia untuk Inseminator & Admin --}}
        @if($canOperate)
        <div class="lg:col-span-1 glass-card p-5 sm:p-6 rounded-2xl border border-emerald-600/30 shadow-sm space-y-4"
             x-data="{ strawMode: 'master' }">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h2 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-repeat text-emerald-600 dark:text-emerald-400"></i>
                    <span>Input IB Lanjutan (IB ke-{{ $nextIbNum }})</span>
                </h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Data pemilik &amp; sapi otomatis terisi. Cukup masukkan Tanggal Inseminasi dan Identitas Pejantan terbaru.
                </p>
            </div>

            <form action="{{ route('pregnancy.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="cow_id" value="{{ $cow->id }}">
                <input type="hidden" name="straw_mode" :value="strawMode">

                <div>
                    <label for="show-ib-date" class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Tanggal Inseminasi <span class="text-rose-500">*</span>
                    </label>
                    <input id="show-ib-date" type="date" name="insemination_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required
                        class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white tabular-nums">
                </div>

                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[11px]">Identitas Pejantan</span>
                    <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[10px] font-bold">
                        <button type="button" @click="strawMode = 'master'" :class="strawMode === 'master' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2 py-1 rounded">Master</button>
                        <button type="button" @click="strawMode = 'custom'" :class="strawMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2 py-1 rounded">Custom</button>
                    </div>
                </div>

                <div x-show="strawMode === 'master'">
                    <select name="bull_straw_id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        <option value="">-- Pilih Master Straw Provinsi --</option>
                        @foreach($straws ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->display_label }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="strawMode === 'custom'" x-cloak class="space-y-2">
                    <select name="straw_rumpun" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                        <option value="">-- Pilih Rumpun Pejantan --</option>
                        @foreach($breeds ?? [] as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                    </select>
                    <input type="text" name="straw_kode_bull" placeholder="Kode Bull (mis: LIM-8890)" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                    <input type="text" name="straw_kode_batch" placeholder="Kode Batch (mis: B26-014)" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl font-mono text-slate-900 dark:text-white">
                </div>

                <div>
                    <label for="show-officer-name" class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Petugas Inseminator
                    </label>
                    @if(auth()->user()->isAdmin())
                        <select name="operator_id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            <option value="">-- Pilih Inseminator --</option>
                            @foreach($inseminators ?? [] as $ins)
                                <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="show-officer-name" type="text" value="{{ auth()->user()->name }}" readonly
                            class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-bold">
                        <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="officer_name" value="{{ auth()->user()->name }}">
                    @endif
                </div>

                <div>
                    <label for="show-ib-notes" class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Catatan</label>
                    <textarea id="show-ib-notes" name="notes" rows="2" placeholder="Catatan IB lanjutan..."
                        class="w-full px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"></textarea>
                </div>

                <button type="submit" class="w-full min-h-[42px] py-2.5 px-4 bg-emerald-700 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Simpan IB ke-{{ $nextIbNum }}</span>
                </button>
            </form>
        </div>
        @endif

        {{-- Riwayat Seluruh IB (IB ke-1, IB ke-2, dst.) --}}
        <div class="{{ $canOperate ? 'lg:col-span-2' : 'lg:col-span-3' }} glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600 dark:text-emerald-400"></i>
                <span>Riwayat Inseminasi &amp; Kebuntingan ({{ $cow->pregnancyRecords->count() }} Kali IB)</span>
            </h2>

            <div class="space-y-3.5">
                @forelse($cow->pregnancyRecords as $rec)
                <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="space-y-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 tabular-nums">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[10px] font-extrabold border border-emerald-200 dark:border-emerald-800">
                                IB ke-{{ $rec->ib_ke ?? 1 }}
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white text-xs">Tgl Inseminasi: {{ $rec->insemination_date->format('d M Y') }}</span>
                            <span class="text-[11px] text-slate-500">Dilaporkan: {{ $rec->created_at?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">
                            Inseminator: <strong class="text-slate-800 dark:text-slate-200">{{ $rec->officer_name ?? '-' }}</strong>
                            &bull; Pejantan: <strong class="text-teal-700 dark:text-teal-400 font-mono">{{ $rec->straw_identity_label }}</strong>
                        </p>
                        @if($rec->notes)
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">"{{ $rec->notes }}"</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        @php $badge = $rec->status_badge; @endphp
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $badge['bg'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center text-xs text-slate-500 py-6">Belum ada riwayat Inseminasi Buatan pada sapi ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
