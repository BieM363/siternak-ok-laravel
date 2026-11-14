@extends('layouts.app')

@section('title', 'Detail Anak Sapi - ' . $calf->calf_nik)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('calves.index') }}" class="inline-flex items-center gap-2 min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Data Anak Sapi</span>
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()))
            <a href="{{ route('calves.edit', $calf->id) }}" class="min-h-[38px] px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Data</span>
            </a>
            @endif
        </div>
    </div>

    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 p-6 space-y-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-700 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                    <i class="fa-solid fa-baby-carriage"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex flex-wrap items-center gap-2">
                        <span>{{ $calf->name ?? 'Anak Sapi' }}</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-md {{ $calf->gender === 'Jantan' ? 'bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                            {{ $calf->gender }}
                        </span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono tabular-nums">NIK: <strong class="text-emerald-700 dark:text-emerald-400">{{ $calf->calf_nik }}</strong></p>
                </div>
            </div>

            @if($calf->is_promoted)
                <div class="px-4 py-2.5 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                    <div>
                        <p class="text-slate-900 dark:text-white text-xs font-bold">Sudah Dipromosikan</p>
                        <a href="{{ route('cows.show', $calf->promoted_cow_id) }}" class="text-[11px] text-emerald-700 dark:text-emerald-400 underline hover:text-emerald-600">
                            Lihat Profil Sapi Siap IB
                        </a>
                    </div>
                </div>
            @elseif($calf->gender === 'Betina')
                <form action="{{ route('calves.promote', $calf->id) }}" method="POST" onsubmit="return confirm('Promosikan anak sapi betina ini menjadi Sapi Siap IB dewasa?')">
                    @csrf
                    <button type="submit" class="min-h-[40px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Tarik Data ke Sapi Siap IB</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                <h2 class="text-xs font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-cake-candles"></i>
                    <span>Informasi Kelahiran</span>
                </h2>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800 tabular-nums">
                        <span class="text-slate-500 dark:text-slate-400">Tanggal Lahir:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $calf->birth_date ? $calf->birth_date->format('d F Y') : '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800 tabular-nums">
                        <span class="text-slate-500 dark:text-slate-400">Berat Lahir:</span>
                        <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $calf->birth_weight ? $calf->birth_weight . ' kg' : '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Ras / Rumpun:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $calf->breed ?? ($calf->motherCow->breed ?? 'Limosin') }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Kondisi Lahir:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $calf->condition }}</span>
                    </div>

                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500 dark:text-slate-400">Catatan Tambahan:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 text-right">{{ $calf->notes ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                <h2 class="text-xs font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-dna"></i>
                    <span>Silsilah &amp; Induk Utama</span>
                </h2>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Induk Sapi:</span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            <a href="{{ route('cows.show', $calf->motherCow->id ?? 0) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline tabular-nums">
                                {{ $calf->motherCow->name ?? '-' }} ({{ $calf->motherCow->cow_nik ?? '-' }})
                            </a>
                        </span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800 gap-4">
                        <span class="text-slate-500 dark:text-slate-400 shrink-0">Pemilik / Peternak:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->farmer->name ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800 gap-4">
                        <span class="text-slate-500 dark:text-slate-400 shrink-0">No. HP:</span>
                        @if(!empty($calf->motherCow->farmer->phone))
                            <a href="tel:{{ $calf->motherCow->farmer->phone }}" class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline text-right tabular-nums">
                                <i class="fa-solid fa-phone mr-1"></i>
                                {{ $calf->motherCow->farmer->phone }}
                            </a>
                        @else
                            <span class="font-medium text-slate-500 text-right">-</span>
                        @endif
                    </div>

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800 gap-4">
                        <span class="text-slate-500 dark:text-slate-400 shrink-0">NIK Peternak:</span>
                        <span class="font-bold text-amber-700 dark:text-amber-300 font-mono text-right tabular-nums">
                            {{ $calf->motherCow->farmer->nik ?? '-' }}
                        </span>
                    </div>
                    @endif

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Kode Straw Pejantan:</span>
                        <span class="font-bold text-teal-700 dark:text-teal-400 font-mono">{{ $calf->pregnancyRecord->semen_code ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Petugas Inseminator:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $calf->pregnancyRecord->officer_name ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Lokasi Wilayah:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 text-right">
                            {{ $calf->motherCow->regency->name ?? '-' }}, Kec. {{ $calf->motherCow->district->name ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between py-1.5 gap-4">
                        <span class="text-slate-500 dark:text-slate-400 shrink-0">Alamat Lengkap:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 text-right leading-relaxed max-w-[65%]">
                            {{ $calf->motherCow->farmer->address ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
