@extends('layouts.app')

@section('title', 'Kartu Detail Sapi & HPL - ' . $cow->cow_nik)

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('cows.index') }}" class="px-3.5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Sapi
        </a>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
        <div class="flex items-center space-x-2">
            <a href="{{ route('cows.edit', $cow->id) }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-pen"></i> Edit Data Sapi
            </a>
            <form action="{{ route('cows.destroy', $cow->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sapi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-trash"></i> Hapus Data Sapi
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Section 1: Kartu Identitas Digital Sapi Betina & Status Kehamilan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- NIK ID Card -->
        <div class="lg:col-span-1 glass-card p-6 rounded-3xl border border-emerald-500/40 relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950/40 flex flex-col justify-between space-y-6 shadow-2xl text-white">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-white uppercase tracking-wider">KARTU TERNAK DIGITAL</h4>
                        <p class="text-[9px] text-emerald-400 font-bold uppercase">Dinas Peternakan Wilayah</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 bg-slate-950 text-[10px] font-mono text-emerald-400 font-bold border border-emerald-500/30 rounded">BETINA</span>
            </div>

            <!-- Cattle NIK & QR Code -->
            <div class="text-center space-y-3 py-2">
                <div class="inline-block p-2 bg-white rounded-2xl shadow-xl border border-slate-700">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($cow->cow_nik) }}" 
                        alt="QR Code NIK {{ $cow->cow_nik }}" class="w-32 h-32 rounded-xl">
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-widest block">NIK TERNAK SAPI</span>
                    <h3 class="font-mono text-lg font-extrabold text-emerald-400 tracking-wider mt-0.5 bg-slate-950/80 px-3 py-1 rounded-xl border border-slate-800 inline-block">
                        {{ $cow->cow_nik }}
                    </h3>
                </div>
            </div>

            <div class="border-t border-slate-800/80 pt-4 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Sapi:</span>
                    <strong class="text-white">{{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Ras / Jenis:</span>
                    <strong class="text-emerald-300">{{ $cow->breed }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Peternak:</span>
                    <strong class="text-white">{{ $cow->farmer->name ?? '-' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">NIK Peternak:</span>
                    <strong class="text-slate-300 font-mono">{{ $cow->farmer->nik ?? '-' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Wilayah Dinas:</span>
                    <strong class="text-slate-200 text-right">{{ $cow->regency->name ?? '-' }}, Kec. {{ $cow->district->name ?? '-' }}, Desa {{ $cow->village->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Milestone Kebuntingan Status Card -->
        <div class="lg:col-span-2 glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/80 border border-amber-300 dark:border-amber-600/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-lg">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Pemantauan Milestone Kebuntingan Sapi</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Inseminasi Buatan (IB) &bull; Reminder 30, 90, 245 Hari & Melahirkan (Window 50 Hari)</p>
                    </div>
                </div>

                @php
                    $latestRecord = $cow->pregnancyRecords->first();
                @endphp

                @if($latestRecord)
                @php $badge = $latestRecord->status_badge; @endphp
                <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold border {{ $badge['bg'] }}">
                    {{ $badge['label'] }}
                </span>
                @else
                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700">
                    Tidak Bunting
                </span>
                @endif
            </div>

            @if($latestRecord)
            <!-- Tanggal Target Milestone Kebuntingan -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-100 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">1. Suntik IB (Hari 0)</p>
                    <p class="font-bold text-slate-900 dark:text-white">{{ $latestRecord->insemination_date->format('d M Y') }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('30day') }}" target="_blank" class="text-[10px] text-blue-600 dark:text-blue-400 hover:underline block font-semibold">+ Cal 30 Hr</a>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase">2. Estimasi 30 Hari</p>
                    <p class="font-bold text-blue-600 dark:text-blue-300">{{ $latestRecord->day30_date ? $latestRecord->day30_date->format('d M Y') : '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('90day') }}" target="_blank" class="text-[10px] text-emerald-600 dark:text-emerald-400 hover:underline block font-semibold">+ Cal 90 Hr</a>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">3. PKB 90 Hari</p>
                    <p class="font-bold text-emerald-600 dark:text-emerald-300">{{ $latestRecord->day90_date ? $latestRecord->day90_date->format('d M Y') : '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('245day') }}" target="_blank" class="text-[10px] text-orange-600 dark:text-orange-400 hover:underline block font-semibold">+ Cal 245 Hr</a>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 uppercase">4. Siap Melahirkan</p>
                    <p class="font-bold text-orange-600 dark:text-orange-300">{{ $latestRecord->day245_date ? $latestRecord->day245_date->format('d M Y') : '-' }}</p>
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('birth') }}" target="_blank" class="text-[10px] text-amber-600 dark:text-amber-400 hover:underline block font-bold">+ Cal HPL</a>
                </div>
            </div>

            <!-- HPL 283 Hari & Quick Failure Button -->
            <div class="bg-amber-50 dark:bg-slate-950 p-4 rounded-2xl border border-amber-300 dark:border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div>
                    <p class="text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase">Perkiraan HPL (283 Hari Pasca IB):</p>
                    <p class="text-lg font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">{{ $latestRecord->estimated_birth_date->format('d M Y') }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Petugas Inseminator: <strong class="text-slate-800 dark:text-slate-200">{{ $latestRecord->officer_name ?? '-' }}</strong> | Straw: <strong class="text-purple-600 dark:text-purple-400 font-mono">{{ $latestRecord->semen_code ?? '-' }}</strong></p>
                </div>
                
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <a href="{{ $latestRecord->getGoogleCalendarUrl('birth') }}" target="_blank" class="px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-1.5">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>+ Google Cal HPL</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) && $latestRecord->status !== 'Gagal/Keguguran' && $latestRecord->status !== 'Melahirkan (Selesai)')
                    <form action="{{ route('pregnancy.update-status', $latestRecord->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai rekam IB ini GAGAL / KEGUGURAN?')">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="confirm_no">
                        <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span>Tandai Gagal / Gugur</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @else
            <div class="text-center py-8 text-slate-500 space-y-2">
                <i class="fa-solid fa-cow text-4xl text-slate-400"></i>
                <p class="text-sm font-semibold">Sapi betina ini belum memiliki rekam kehamilan aktif.</p>
            </div>
            @endif

        </div>

    </div>

    <!-- Section 2: Data Anak Sapi Lahir & Riwayat Kebuntingan -->
    @if($cow->calves->count() > 0)
    <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <i class="fa-solid fa-baby-carriage text-purple-500"></i>
            Data Anak Sapi yang Dilahirkan (Rekapan Kelahiran Induk)
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($cow->calves as $calf)
            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-slate-900 px-2 py-0.5 rounded border border-purple-200 dark:border-slate-800">
                        {{ $calf->calf_nik }}
                    </span>
                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $calf->name ?? 'Anak Sapi' }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Tanggal Lahir: <strong class="text-slate-800 dark:text-slate-200">{{ $calf->birth_date ? $calf->birth_date->format('d M Y') : '-' }}</strong></p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Berat Lahir: {{ $calf->birth_weight ?? '-' }} kg | Kondisi: <strong class="text-emerald-600 dark:text-emerald-400">{{ $calf->condition }}</strong></p>
                </div>
                <div>
                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $calf->gender === 'Jantan' ? 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-600' : 'bg-pink-100 dark:bg-pink-950 text-pink-700 dark:text-pink-300 border border-pink-300 dark:border-pink-600' }}">
                        <i class="fa-solid {{ $calf->gender === 'Jantan' ? 'fa-mars' : 'fa-venus' }} mr-1"></i> {{ $calf->gender }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Section 3: Form Input Rekam IB Baru & Riwayat Kehamilan Sapi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Add Inseminasi Record (Khusus Operator / Admin dengan Dropdown Master Straw Pejantan) -->
        @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()))
        <div class="lg:col-span-1 glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-syringe text-emerald-500"></i>
                Pendaftaran Inseminasi Buatan (IB) Baru
            </h4>

            <form action="{{ route('pregnancy.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="cow_id" value="{{ $cow->id }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Suntik IB <span class="text-rose-500">*</span></label>
                    <input type="date" name="insemination_date" value="{{ date('Y-m-d') }}" required 
                        class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Dropdown Master Kode Straw Pejantan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Pilih Kode Straw Pejantan</span>
                        <a href="{{ route('straws.index') }}" target="_blank" class="text-[10px] text-purple-600 dark:text-purple-400 font-bold underline">+ Master</a>
                    </label>
                    <select name="semen_code" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Kode Straw Pejantan --</option>
                        @foreach($straws ?? [] as $st)
                        <option value="{{ $st->code }}">
                            {{ $st->code }} - {{ $st->bull_name }} ({{ $st->breed }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Petugas Inseminator</label>
                    <input type="text" name="officer_name" value="{{ auth()->user()->name }}" 
                        class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan kondisi sapi..." 
                        class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Simpan Rekam IB Baru</span>
                </button>
            </form>
        </div>
        @endif

        <!-- Timeline Riwayat Inseminasi & Kehamilan -->
        <div class="{{ auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) ? 'lg:col-span-2' : 'lg:col-span-3' }} glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                Riwayat Siklus Inseminasi & Kebuntingan Sapi Betina
            </h4>

            <div class="space-y-4">
                @forelse($cow->pregnancyRecords as $rec)
                <div class="bg-slate-50 dark:bg-slate-950/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-slate-900 dark:text-white text-xs">IB: {{ $rec->insemination_date->format('d M Y') }}</span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs text-amber-600 dark:text-amber-300 font-semibold">HPL: {{ $rec->estimated_birth_date->format('d M Y') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Inseminator: <strong class="text-slate-700 dark:text-slate-300">{{ $rec->officer_name ?? '-' }}</strong> | Straw: <strong class="text-purple-600 dark:text-purple-400 font-mono">{{ $rec->semen_code ?? '-' }}</strong>
                        </p>
                        @if($rec->notes)
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">"{{ $rec->notes }}"</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) && $rec->status !== 'Gagal/Keguguran' && $rec->status !== 'Melahirkan (Selesai)')
                        <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" onsubmit="return confirm('Tandai rekam IB ini GAGAL / KEGUGURAN?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="confirm_no">
                            <button type="submit" class="px-2 py-1 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-bold rounded border border-rose-300 dark:border-rose-800 transition">
                                <i class="fa-solid fa-xmark mr-1"></i> Tandai Gagal
                            </button>
                        </form>
                        @endif

                        <a href="{{ $rec->getGoogleCalendarUrl() }}" target="_blank" class="px-2 py-1 bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-[10px] font-bold rounded border border-blue-300 dark:border-blue-800">
                            <i class="fa-solid fa-calendar-plus mr-1"></i> + Cal
                        </a>
                        @php $badge = $rec->status_badge; @endphp
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border {{ $badge['bg'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center py-6 text-xs text-slate-500">Belum ada riwayat kehamilan untuk sapi betina ini.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
