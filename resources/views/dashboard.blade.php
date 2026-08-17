@extends('layouts.app')

@section('title', 'Dashboard Pemantauan Kehamilan Sapi')

@section('content')
<div class="space-y-6">

    <!-- Top Reminder Banner (Pop-up Push Notification Bunting 245 Hari / Window Melahirkan 50 Hari) -->
    @if(isset($pendingConfirms) && count($pendingConfirms) > 0)
    <div class="glass-card p-5 sm:p-6 rounded-3xl border-2 border-emerald-500/60 bg-gradient-to-r from-emerald-950/90 via-slate-900 to-teal-950/90 shadow-2xl relative overflow-hidden text-white" x-data="{ showPushModal: false }">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                    <h3 class="font-extrabold text-sm text-emerald-300 uppercase tracking-wider">
                        REMINDER KONFIRMASI (BATAS MAX 3 HARI): {{ count($pendingConfirms) }} SAPI MEMBUTUHKAN AKSI
                    </h3>
                </div>
                <p class="text-xs text-slate-300">Sapi betina di bawah telah memasuki jadwal milestone atau window melahirkan. Mohon lakukan konfirmasi YA / TIDAK.</p>
            </div>

            <button @click="showPushModal = true" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5 flex-shrink-0">
                <i class="fa-solid fa-bell"></i>
                <span>Tampilkan Notifikasi HP/Peramban</span>
            </button>
        </div>

        <!-- Horizontal Scroll Cards Pending Confirmations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            @foreach($pendingConfirms as $item)
            @php 
                $rec = $item['record']; 
                $cow = $rec->cow;
            @endphp
            <div class="bg-slate-950/90 p-4 rounded-2xl border border-emerald-500/40 space-y-3 shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-emerald-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">
                        {{ $cow->cow_nik }}
                    </span>
                    <span class="px-2 py-0.5 bg-purple-950 text-purple-300 font-bold text-[10px] rounded border border-purple-800">
                        {{ $rec->status }}
                    </span>
                </div>

                <div>
                    <h4 class="font-bold text-sm text-white">{{ $cow->name ?? 'Sapi ' . $cow->cow_nik }} ({{ $cow->breed }})</h4>
                    <p class="text-xs text-slate-400">Peternak: <strong class="text-slate-200">{{ $cow->farmer->name ?? '-' }}</strong> <span class="text-[10px] font-mono text-slate-400">(NIK: {{ $cow->farmer->nik ?? '-' }})</span></p>
                    <p class="text-xs text-slate-400">Milestone: <strong class="text-amber-300">{{ $item['milestone_label'] }}</strong></p>
                    <p class="text-xs text-slate-400">Tanggal Konfirmasi: <strong class="text-emerald-300">{{ $item['target_date'] ? $item['target_date']->format('d M Y') : '-' }}</strong></p>
                    <p class="text-xs text-slate-400">HPL: <strong class="text-amber-400 font-bold">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d M Y') : '-' }}</strong></p>
                </div>

                <!-- Form Interactive Quick Confirm YA / TIDAK -->
                <div class="pt-2 border-t border-slate-800 space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <a href="{{ $rec->getGoogleCalendarUrl() }}" target="_blank" class="text-blue-400 hover:underline font-bold">
                            <i class="fa-solid fa-calendar-plus mr-1"></i> + Google Calendar
                        </a>
                        <span class="text-amber-400 font-bold">Max 3 Hari</span>
                    </div>

                    @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()))
                        @if($rec->status === 'Fase Melahirkan')
                        <!-- Form Khusus Input Data Anak (Pedet) jika Fase Melahirkan -->
                        <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="space-y-2" x-data="{ birthAction: 'birth_yes' }">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" :value="birthAction">

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <label class="block text-[10px] text-slate-400">Jenis Kelamin Anak</label>
                                    <select name="calf_gender" class="w-full px-2 py-1 bg-slate-900 border border-slate-800 rounded text-xs text-white">
                                        <option value="Jantan">Jantan</option>
                                        <option value="Betina">Betina</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400">Berat Lahir (kg)</label>
                                    <input type="number" step="0.1" name="calf_weight" value="30" class="w-full px-2 py-1 bg-slate-900 border border-slate-800 rounded text-xs text-white">
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" @click="birthAction = 'birth_yes'" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow">
                                    <i class="fa-solid fa-check mr-1"></i> YA (Melahirkan)
                                </button>
                                <button type="submit" @click="birthAction = 'birth_no'" onclick="return confirm('Sapi gagal melahirkan/gugur?')" class="py-1.5 px-3 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow">
                                    <i class="fa-solid fa-xmark mr-1"></i> TIDAK
                                </button>
                            </div>
                        </form>
                        @else
                        <!-- Form Standard Konfirmasi YA / TIDAK -->
                        <div class="flex gap-2">
                            <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="confirm_yes">
                                <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow">
                                    <i class="fa-solid fa-check mr-1"></i> YA (Bunting)
                                </button>
                            </form>

                            <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="confirm_no">
                                <button type="submit" onclick="return confirm('Konfirmasi TIDAK (Gagal Bunting)?')" class="w-full py-1.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow">
                                    <i class="fa-solid fa-xmark mr-1"></i> TIDAK
                                </button>
                            </form>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Push Notification Modal Dialog -->
        <div x-show="showPushModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="showPushModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 text-slate-900 dark:text-white">
                <div class="flex items-center space-x-3 text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-bell text-2xl"></i>
                    <h4 class="font-extrabold text-sm">Notifikasi Pengingat Peramban/HP</h4>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300">
                    Sistem SiTernak-OK secara otomatis mengirimkan notifikasi pengingat ke peramban dan HP Anda ketika terdapat sapi betina yang memasuki jadwal konfirmasi (Batas Maksimal 3 Hari).
                </p>
                <div class="bg-slate-100 dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-mono">
                    Status Push Notification: <strong class="text-emerald-600 dark:text-emerald-400">AKTIF (Browser Supported)</strong>
                </div>
                <div class="flex justify-end">
                    <button @click="showPushModal = false" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section Stat Cards (Quick Summary) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Sapi Betina</span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalCows }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-cow text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Sapi Bunting (Aktif)</span>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalActivePregnancies }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-syringe text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Siap Melahirkan (HPL)</span>
                <h3 class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $totalReadyToGiveBirth }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-clock text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-purple-600 dark:text-purple-400 uppercase tracking-wider block">Anak Sapi Lahir</span>
                <h3 class="text-xl sm:text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $totalCalvesBorn }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-baby-carriage text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar (Multilevel Regency, Inseminator & Status Filter) -->
    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Keyword Search -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Cari NIK Sapi / Nama Sapi / NIK Peternak</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik NIK Sapi, Nama, atau NIK/Nama Peternak..." 
                        class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Kabupaten / Kota Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kabupaten / Kota</label>
                <select name="regency_id" class="w-full px-3 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500" {{ auth()->check() && auth()->user()->isAdminKabupaten() ? 'disabled' : '' }}>
                    <option value="">-- Semua Kabupaten --</option>
                    @foreach($regencies as $r)
                    <option value="{{ $r->id }}" {{ $regencyId == $r->id ? 'selected' : '' }}>{{ $r->code }} - {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Inseminator / Petugas Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Inseminator / Petugas</label>
                <select name="officer_name" class="w-full px-3 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($officers as $off)
                    <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Kebuntingan Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Fase Kebuntingan</label>
                <select name="status" class="w-full px-3 py-2.5 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Fase --</option>
                    <option value="Pasca IB" {{ $statusFilter == 'Pasca IB' ? 'selected' : '' }}>Pasca IB (0-30 Hari)</option>
                    <option value="Estimasi 30 Hari" {{ $statusFilter == 'Estimasi 30 Hari' ? 'selected' : '' }}>Estimasi 30 Hari</option>
                    <option value="Bunting (PKB 90 Hari)" {{ $statusFilter == 'Bunting (PKB 90 Hari)' ? 'selected' : '' }}>Bunting PKB 90 Hari</option>
                    <option value="Siap Melahirkan (245 Hari)" {{ $statusFilter == 'Siap Melahirkan (245 Hari)' ? 'selected' : '' }}>Siap Melahirkan (245 Hari)</option>
                    <option value="Fase Melahirkan" {{ $statusFilter == 'Fase Melahirkan' ? 'selected' : '' }}>Fase Melahirkan (HPL)</option>
                    <option value="Melahirkan (Selesai)" {{ $statusFilter == 'Melahirkan (Selesai)' ? 'selected' : '' }}>Melahirkan (Selesai)</option>
                    <option value="Gagal/Keguguran" {{ $statusFilter == 'Gagal/Keguguran' ? 'selected' : '' }}>Gagal / Keguguran</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if($search || $regencyId || $statusFilter || $officerName)
                <a href="{{ route('dashboard') }}" class="py-2.5 px-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs font-semibold transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Table: Pemantauan Kehamilan Sapi & Milestone -->
    <div class="glass-card rounded-2xl shadow-sm overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-100 dark:bg-slate-900/50">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-500"></i>
                Daftar Pemantauan Kehamilan, Tanggal Milestone & Sisa Hari
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $records->count() }} dari {{ $records->total() }} Sapi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi & Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Sapi & Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Tgl Inseminasi (IB)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Milestone (30/90/245 Hr)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Estimasi HPL (283 Hari)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Fase & Status Milestone</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi & Reminders</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 bg-white dark:bg-slate-900/30">
                    @forelse($records as $rec)
                    @php 
                        $badge = $rec->status_badge;
                        $gcalUrl = $rec->getGoogleCalendarUrl();
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <!-- NIK Sapi & Wilayah -->
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-1 rounded border border-emerald-200 dark:border-slate-800 inline-block">
                                {{ $rec->cow->cow_nik ?? 'N/A' }}
                            </span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                <strong class="text-slate-700 dark:text-slate-300">{{ $rec->cow->regency->name ?? '-' }}</strong> &bull; Kec. {{ $rec->cow->district->name ?? '-' }}
                            </p>
                        </td>

                        <!-- Sapi & Peternak (NIK dan Nama Terpisah) -->
                        <td class="px-4 py-3.5">
                            <a href="{{ route('cows.show', $rec->cow->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors text-xs flex items-center gap-1.5">
                                {{ $rec->cow->name ?? 'Sapi ' . $rec->cow->cow_nik }}
                                <span class="text-[10px] font-normal text-slate-500 dark:text-slate-400">({{ $rec->cow->breed }})</span>
                            </a>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Peternak: <strong class="text-slate-800 dark:text-slate-300">{{ $rec->cow->farmer->name ?? '-' }}</strong> 
                                <span class="text-[10px] font-mono text-slate-400">(NIK: {{ $rec->cow->farmer->nik ?? '-' }})</span>
                            </p>
                        </td>

                        <!-- Tanggal IB -->
                        <td class="px-4 py-3.5">
                            <span class="font-bold text-slate-900 dark:text-slate-200">{{ $rec->insemination_date ? $rec->insemination_date->format('d/m/Y') : '-' }}</span>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Inseminator: {{ $rec->officer_name ?? '-' }}</p>
                        </td>

                        <!-- Milestone 30/90/245 Hari -->
                        <td class="px-4 py-3.5 text-[11px] text-slate-700 dark:text-slate-300 space-y-0.5">
                            <p>30 Hr: <strong class="text-blue-600 dark:text-blue-300">{{ $rec->day30_date ? $rec->day30_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>90 Hr (PKB): <strong class="text-emerald-600 dark:text-emerald-300">{{ $rec->day90_date ? $rec->day90_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>245 Hr: <strong class="text-amber-600 dark:text-orange-300">{{ $rec->day245_date ? $rec->day245_date->format('d/m/Y') : '-' }}</strong></p>
                        </td>

                        <!-- HPL 283 Hari -->
                        <td class="px-4 py-3.5">
                            <span class="font-bold text-amber-600 dark:text-amber-300 text-xs">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d/m/Y') : '-' }}</span>
                            @if($rec->actual_birth_date)
                            <p class="text-[10px] text-purple-600 dark:text-purple-400 font-bold mt-0.5">Lahir: {{ $rec->actual_birth_date->format('d/m/Y') }}</p>
                            @endif
                        </td>

                        <!-- Status Badge & Indicator -->
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badge['bg'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @if($rec->is_auto_advanced)
                            <span class="block text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-1">
                                <i class="fa-solid fa-clock-rotate-left"></i> Auto Lanjut (3 Hari Expired)
                            </span>
                            @endif
                        </td>

                        <!-- Actions & Google Calendar Button -->
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ $gcalUrl }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950 hover:dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg text-[10px] font-bold border border-blue-200 dark:border-blue-800 transition-all">
                                    <i class="fa-solid fa-calendar-plus mr-1"></i> + Cal
                                </a>

                                @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) && $rec->status !== 'Gagal/Keguguran' && $rec->status !== 'Melahirkan (Selesai)')
                                <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" onsubmit="return confirm('Tandai rekam IB sapi {{ $rec->cow->cow_nik }} GAGAL / KEGUGURAN?')" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="confirm_no">
                                    <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-bold rounded-lg border border-rose-200 dark:border-rose-800 transition-all" title="Tandai Gagal / Keguguran">
                                        <i class="fa-solid fa-xmark mr-0.5"></i> Gagal
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('cows.show', $rec->cow->id) }}" class="inline-flex items-center px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 hover:dark:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-lg text-[11px] font-semibold border border-slate-300 dark:border-slate-700 transition-all">
                                    <i class="fa-solid fa-eye mr-1 text-emerald-500"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-cow text-4xl mb-3 block text-slate-300 dark:text-slate-700"></i>
                            <p class="text-sm font-semibold">Tidak ada data kehamilan sapi yang cocok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
            {{ $records->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
