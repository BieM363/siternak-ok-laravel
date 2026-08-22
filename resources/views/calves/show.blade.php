@extends('layouts.app')

@section('title', 'Detail Anak Sapi - ' . $calf->calf_nik)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('calves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold border border-slate-700 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Data Anak Sapi</span>
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->check() && auth()->user()->isOperator())
            <a href="{{ route('calves.edit', $calf->id) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-amber-900/30">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Data</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Main Card Detail -->
    <div class="glass-card rounded-2xl border border-slate-800 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-5 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-emerald-900/40">
                    <i class="fa-solid fa-baby-carriage"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                        {{ $calf->name ?? 'Anak Sapi' }}
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $calf->gender === 'Jantan' ? 'bg-blue-900/80 text-blue-300 border border-blue-600' : 'bg-pink-900/80 text-pink-300 border border-pink-600' }}">
                            {{ $calf->gender }}
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-1 font-mono">NIK: <strong class="text-emerald-400">{{ $calf->calf_nik }}</strong></p>
                </div>
            </div>

            <!-- Promotion Status Banner -->
            @if($calf->is_promoted)
                <div class="px-4 py-2 bg-emerald-950/90 border border-emerald-500/60 rounded-xl text-emerald-300 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    <div>
                        <p class="text-white text-xs font-bold">Sudah Dipromosikan</p>
                        <a href="{{ route('cows.show', $calf->promoted_cow_id) }}" class="text-[11px] text-emerald-400 underline hover:text-emerald-300">
                            Lihat Profil Sapi Siap IB &rarr;
                        </a>
                    </div>
                </div>
            @elseif($calf->gender === 'Betina')
                <form action="{{ route('calves.promote', $calf->id) }}" method="POST" onsubmit="return confirm('Promosikan anak sapi betina ini menjadi Sapi Siap IB dewasa?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-emerald-900/40 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Tarik Data ke Sapi Siap IB</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Data Kelahiran -->
            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-3">
                <h3 class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-cake-candles"></i>
                    <span>Informasi Kelahiran</span>
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Tanggal Lahir:</span>
                        <span class="font-bold text-white">{{ $calf->birth_date ? $calf->birth_date->format('d F Y') : '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Berat Lahir:</span>
                        <span class="font-bold text-emerald-400">{{ $calf->birth_weight ? $calf->birth_weight . ' kg' : '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Ras / Rumpun:</span>
                        <span class="font-bold text-white">{{ $calf->breed ?? ($calf->motherCow->breed ?? 'Limosin') }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Kondisi Lahir:</span>
                        <span class="font-bold text-white">{{ $calf->condition }}</span>
                    </div>

                    <div class="flex justify-between py-1">
                        <span class="text-slate-400">Catatan Tambahan:</span>
                        <span class="font-medium text-slate-300 text-right">{{ $calf->notes ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Data Induk & Silsilah -->
            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-3">
                <h3 class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-dna"></i>
                    <span>Silsilah & Induk Utama</span>
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Induk Sapi:</span>
                        <span class="font-bold text-white">
                            <a href="{{ route('cows.show', $calf->motherCow->id ?? 0) }}" class="text-emerald-400 hover:underline">
                                {{ $calf->motherCow->name ?? '-' }} ({{ $calf->motherCow->cow_nik ?? '-' }})
                            </a>
                        </span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800 gap-4">
                        <span class="text-slate-400 shrink-0">Pemilik / Peternak:</span>
                        <span class="font-bold text-white">{{ $calf->motherCow->farmer->name ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800 gap-4">
                        <span class="text-slate-400 shrink-0">No. HP:</span>

                        @if(!@empty($calf->motherCow->farmer->phone))
                            <a href="tel:{{ $calf->motherCow->farmer->phone }}" class="font-bold text-emerald-400 hover:text-emerald-300 hover:underline text-right">
                                <i class="fa-solid fa-phone mr-1"></i>
                                {{ $calf->motherCow->farmer->phone }}
                            </a>
                        @else
                        <span class="font-medium text-slate-500 text-right">
                            -
                        </span>
                    @endif
                </div>
               
                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="flex justify-between py-1 border-b border-slate-800 gap-4">
                    <span class="text-slate-400 shrink-0">
                        NIK Peternak:
                    </span>
                
                    <span class="font-bold text-amber-300 font-mono text-right">
                        {{ $calf->motherCow->farmer->nik ?? '-' }}
                    </span>
                </div>
                @endif

                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Kode Straw Pejantan:</span>
                        <span class="font-bold text-amber-300 font-mono">{{ $calf->pregnancyRecord->semen_code ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800">
                        <span class="text-slate-400">Petugas Inseminator:</span>
                        <span class="font-bold text-white">{{ $calf->pregnancyRecord->officer_name ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between py-1">
                        <span class="text-slate-400">Lokasi Wilayah:</span>
                        <span class="font-medium text-slate-300 text-right">
                            Kab. {{ $calf->motherCow->regency->name ?? '-' }}, Kec. {{ $calf->motherCow->district->name ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-800 gap-4">
                        <span class="text-slate-400 shrink-0">Alamat Lengkap: </span>
                        <span class="font-medium text-slate-300 text-right leading-relaxed max-w-[65%]">
                            {{ $calf->motherCow->farmer->address ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
