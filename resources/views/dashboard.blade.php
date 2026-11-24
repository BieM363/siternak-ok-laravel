@extends('layouts.app')

@section('title', 'Dashboard Pemantauan Kebuntingan Sapi')

@section('content')
<div class="space-y-6">

    @if(auth()->check())
        @if(auth()->user()->isAdminProvinsi())
        <section class="glass-card p-5 sm:p-7 rounded-2xl border border-emerald-700/30 bg-slate-900 text-white shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white">
                            Dinas Peternakan dan Perkebunan Provinsi Gorontalo
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-950 text-emerald-300 text-[11px] font-bold uppercase border border-emerald-700/60">
                            <i class="fa-solid fa-building-Flag text-emerald-400"></i>
                            <span>Admin Provinsi</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Selamat bertugas, <strong class="text-white">{{ auth()->user()->name }}</strong>. Anda mengawasi seluruh <strong>6 Kabupaten &amp; Kota</strong> di Provinsi Gorontalo (Boalemo, Bone Bolango, Kab. Gorontalo, Gorontalo Utara, Pohuwato, dan Kota Gorontalo).
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('farmers.index') }}" class="min-h-[38px] px-3 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-users shrink-0"></i>
                        <span class="truncate">Data Peternak</span>
                    </a>
                    <a href="{{ route('regions.index') }}" class="min-h-[38px] px-3 py-2 bg-teal-700 hover:bg-teal-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot shrink-0"></i>
                        <span class="truncate">Wilayah</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="min-h-[38px] px-3 py-2 bg-sky-700 hover:bg-sky-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-user-gear shrink-0"></i>
                        <span class="truncate">Kelola User</span>
                    </a>
                    <a href="{{ route('straws.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-vial text-teal-400 shrink-0"></i>
                        <span class="truncate">Straw IB</span>
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->user()->isAdminKota())
        <section class="glass-card p-5 sm:p-7 rounded-2xl border border-teal-700/30 bg-slate-900 text-white shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white">
                            Pemerintah Kota Gorontalo &bull; Wilayah Perkotaan
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-teal-950 text-teal-300 text-[11px] font-bold uppercase border border-teal-700/60">
                            <i class="fa-solid fa-city text-teal-400"></i>
                            <span>Admin Kota Gorontalo</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Selamat bertugas, <strong class="text-white">{{ auth()->user()->name }}</strong>. Cakupan operasional Anda meliputi <strong>9 Kecamatan &amp; 50 Kelurahan</strong> di Kota Gorontalo.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('regions.index') }}" class="min-h-[38px] px-3.5 py-2 bg-teal-700 hover:bg-teal-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>50 Kelurahan Kota</span>
                    </a>
                    <a href="{{ route('farmers.index') }}" class="min-h-[38px] px-3.5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-users"></i>
                        <span>Peternak Kota</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="min-h-[38px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-teal-300 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>User Kota</span>
                    </a>
                    <a href="{{ route('straws.index') }}" class="min-h-[38px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-vial text-emerald-400"></i>
                        <span>Straw Pejantan</span>
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->user()->isAdminKabupaten())
        <section class="glass-card p-5 sm:p-7 rounded-2xl border border-sky-700/30 bg-slate-900 text-white shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white">
                            Dinas Peternakan &amp; Kesehatan Hewan {{ auth()->user()->regency->name ?? '' }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-sky-950 text-sky-300 text-[11px] font-bold uppercase border border-sky-700/60">
                            <i class="fa-solid fa-shield-halved text-sky-400"></i>
                            <span>Admin {{ auth()->user()->regency->name ?? 'Kabupaten' }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Selamat bertugas, <strong class="text-white">{{ auth()->user()->name }}</strong>. Anda mengelola data peternakan, inseminator, dan peternak lokal pada cakupan <strong>{{ auth()->user()->regency->name ?? 'Kabupaten Anda' }}</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('farmers.index') }}" class="min-h-[38px] px-3.5 py-2 bg-sky-700 hover:bg-sky-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-users"></i>
                        <span>Peternak Lokal</span>
                    </a>
                    <a href="{{ route('regions.index') }}" class="min-h-[38px] px-3.5 py-2 bg-teal-700 hover:bg-teal-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Desa &amp; Kecamatan</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="min-h-[38px] px-3.5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Operator Lokal</span>
                    </a>
                    <a href="{{ route('straws.index') }}" class="min-h-[38px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-vial text-teal-400"></i>
                        <span>Straw Pejantan</span>
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->user()->isOperator())
        <section class="glass-card p-5 sm:p-7 rounded-2xl border border-emerald-700/40 bg-slate-900 text-white shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white">
                            Pusat Operasional Pelayanan IB &amp; Monitoring Kebuntingan
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-950 text-emerald-300 text-[11px] font-bold uppercase border border-emerald-700/60">
                            <i class="fa-solid fa-syringe text-emerald-400"></i>
                            <span>Inseminator Lapangan</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-950 text-amber-300 text-[11px] font-bold border border-amber-700/60 tabular-nums">
                            <i class="fa-solid fa-stopwatch text-amber-400"></i>
                            <span>Sesi 8 Jam:</span>
                            <span data-operator-countdown class="font-mono text-white">{{ auth()->user()->getFormattedRemainingSession() }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Selamat bertugas di lapangan, <strong class="text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->regency->name ?? 'Provinsi Gorontalo' }}). Waktu login dibatasi maksimal <strong>8 jam</strong> melalui cronjob otomatis dengan dukungan notifikasi peramban ponsel.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('cows.create') }}" class="min-h-[40px] px-3 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus text-sm shrink-0"></i>
                        <span class="truncate">Daftar IB Baru</span>
                    </a>
                    <button type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.testMobileNotification()" class="min-h-[40px] px-3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen-button shrink-0"></i>
                        <span class="truncate">Tes Notifikasi HP</span>
                    </button>
                    <a href="{{ route('straws.index') }}" class="min-h-[40px] px-3 py-2 bg-slate-800 hover:bg-slate-700 text-teal-300 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-vial shrink-0"></i>
                        <span class="truncate">Cek Straw</span>
                    </a>
                    <a href="{{ route('reports.inseminators') }}" class="min-h-[40px] px-3 py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-trophy shrink-0"></i>
                        <span class="truncate">Leaderboard IB</span>
                    </a>
                </div>
            </div>
        </section>
        @endif
    @else
        <section class="glass-card p-5 sm:p-7 rounded-2xl border border-slate-700/60 bg-slate-900 text-white shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white">
                            Sistem Pemantauan Kebuntingan Sapi &bull; Disnakbun Provinsi Gorontalo
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 text-[11px] font-bold uppercase border border-slate-700">
                            <i class="fa-solid fa-eye text-emerald-400"></i>
                            <span>Akses Publik (Read-Only)</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Anda sedang melihat statistik populasi dan siklus kebuntingan sapi secara publik. NIK KTP peternak disembunyikan demi privasi. Untuk mencatat tindakan IB, silakan masuk menggunakan akun petugas.
                    </p>
                </div>

                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('login') }}" class="min-h-[40px] px-5 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold transition flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login Petugas / Admin</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if(isset($confirmationReminders) && $confirmationReminders->count() > 0)
    <section class="glass-card p-4 sm:p-5 rounded-2xl border-2 border-emerald-600/60 bg-slate-900 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-start sm:items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-bell text-base"></i>
            </div>
            <div class="min-w-0">
                <h2 class="font-extrabold text-xs sm:text-sm text-emerald-300 uppercase tracking-wide tabular-nums">
                    Notifikasi 3 Tahap Kebuntingan: {{ $confirmationReminders->count() }} Sapi Perlu Konfirmasi
                </h2>
                <p class="text-xs text-slate-300 mt-0.5">
                    Antrean konfirmasi (Hari ke-30, PKB 90 Hari, Bulan ke-9 · Auto-Lanjut 3 Hari) kini dipisahkan ke menu khusus <strong>Konfirmasi</strong> agar Dashboard tetap ringkas.
                </p>
            </div>
        </div>
        <a href="{{ route('confirmations.index') }}"
           class="min-h-[40px] px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl transition inline-flex items-center justify-center gap-2 shrink-0 w-full sm:w-auto whitespace-nowrap">
            <span>Buka Menu Konfirmasi ({{ $confirmationReminders->count() }})</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </section>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Sapi di-IB</span>
                <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1 tabular-nums">{{ $totalIbCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-400 flex items-center justify-center font-bold border border-sky-200 dark:border-sky-800">
                <i class="fa-solid fa-syringe text-lg"></i>
            </div>
        </div>

        <a href="{{ route('pregnancies.index') }}" class="glass-card p-4 sm:p-5 rounded-2xl border-2 border-emerald-600/40 dark:border-emerald-500/40 shadow-sm flex items-center justify-between hover:border-emerald-500 transition">
            <div>
                <span class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Total Bunting Aktif</span>
                <p class="text-xl sm:text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums">{{ $totalActivePregnancies }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-200 dark:border-emerald-800">
                <i class="fa-solid fa-heart-pulse text-lg"></i>
            </div>
        </a>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Bulan ke-9 (Lahir)</span>
                <p class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 tabular-nums">{{ $totalReadyToGiveBirth }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold border border-amber-200 dark:border-amber-800">
                <i class="fa-solid fa-clock text-lg"></i>
            </div>
        </div>

        <a href="{{ route('calves.index') }}" class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between hover:border-teal-500 transition">
            <div>
                <span class="text-[10px] font-extrabold text-teal-700 dark:text-teal-400 uppercase tracking-wider block">Pedet &amp; Kelahiran</span>
                <p class="text-xl sm:text-2xl font-black text-teal-700 dark:text-teal-400 mt-1 tabular-nums">{{ $totalCalvesBorn }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold border border-teal-200 dark:border-teal-800">
                <i class="fa-solid fa-baby-carriage text-lg"></i>
            </div>
        </a>
    </div>

    @if(isset($breedRecap) && $breedRecap->count() > 0)
    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-2.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                Rekap Per Jenis / Ras Sapi (Total IB · Bunting · Kelahiran/Pedet)
            </span>
            <a href="{{ route('reports.excel', request()->query()) }}" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline inline-flex items-center gap-1">
                <i class="fa-solid fa-file-excel"></i> Ekspor Rekap Excel (.xlsx)
            </a>
        </div>
        <div class="flex flex-wrap gap-2 tabular-nums">
            @foreach($breedRecap as $br)
                <span class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-800 dark:text-slate-200">
                    <strong>{{ $br->breed }}</strong>: IB {{ $br->total_ib }} · <span class="text-emerald-700 dark:text-emerald-400 font-bold">Bunting {{ $br->total_bunting }}</span> · <span class="text-teal-700 dark:text-teal-400 font-bold">Lahir {{ $br->total_lahir }}</span>
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="dashboard-search" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Cari Sapi / Pemilik</label>
                <input id="dashboard-search" type="text" name="search" value="{{ $search }}" placeholder="NIK Sapi, Nama, atau Pemilik..."
                    class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="dashboard-regency" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kabupaten / Kota</label>
                <select id="dashboard-regency" name="regency_id" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white" {{ auth()->check() && auth()->user()->isAdminKabupaten() ? 'disabled' : '' }}>
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $r)
                    <option value="{{ $r->id }}" {{ $regencyId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dashboard-officer" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Petugas Inseminator</label>
                <select id="dashboard-officer" name="officer_name" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $off)
                        <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dashboard-nik-status" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Status NIK Pemilik</label>
                <select id="dashboard-nik-status" name="nik_status" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                    <option value="">-- Semua Pemilik --</option>
                    <option value="with_nik" {{ ($nikStatus ?? '') === 'with_nik' ? 'selected' : '' }}>Pemilik Ada NIK</option>
                    <option value="without_nik" {{ ($nikStatus ?? '') === 'without_nik' ? 'selected' : '' }}>Pemilik Tanpa NIK</option>
                </select>
            </div>

            <div>
                <label for="dashboard-start" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Dari Tgl Inseminasi</label>
                <input id="dashboard-start" type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white tabular-nums">
            </div>

            <div>
                <label for="dashboard-end" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sampai Tgl Inseminasi</label>
                <input id="dashboard-end" type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white tabular-nums">
            </div>

            <div>
                <label for="dashboard-status" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahap Kebuntingan</label>
                <select id="dashboard-status" name="status" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                    <option value="">-- Semua Tahap --</option>
                    <option value="Pasca IB" {{ $statusFilter == 'Pasca IB' ? 'selected' : '' }}>Tahap 1 · Pasca IB (0-30 Hr)</option>
                    <option value="Bunting (PKB 90 Hari)" {{ $statusFilter == 'Bunting (PKB 90 Hari)' ? 'selected' : '' }}>Tahap 2 · Menunggu PKB 90 Hr</option>
                    <option value="Siap Melahirkan (245 Hari)" {{ $statusFilter == 'Siap Melahirkan (245 Hari)' ? 'selected' : '' }}>Bunting Terkonfirmasi (PKB 90)</option>
                    <option value="Fase Melahirkan" {{ $statusFilter == 'Fase Melahirkan' ? 'selected' : '' }}>Tahap 3 · Bulan ke-9 (Lahir)</option>
                    <option value="Melahirkan (Selesai)" {{ $statusFilter == 'Melahirkan (Selesai)' ? 'selected' : '' }}>Melahirkan (Selesai)</option>
                    <option value="Gagal/Keguguran" {{ $statusFilter == 'Gagal/Keguguran' ? 'selected' : '' }}>Tidak Bunting / Siap IB Ulang</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full min-h-[38px] py-2 px-4 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan</span>
                </button>
                @if($search || $regencyId || $statusFilter || $officerName || ($nikStatus ?? '') || ($startDate ?? '') || ($endDate ?? ''))
                <a href="{{ route('dashboard') }}" aria-label="Reset filter" class="min-h-[38px] py-2 px-3 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl shadow-sm overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 bg-slate-50 dark:bg-slate-900">
            <h2 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                <span>Daftar Pemantauan Kebuntingan, 3 Tahap Milestone &amp; Umur Kebuntingan</span>
            </h2>
            <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 tabular-nums shrink-0">Menampilkan {{ $records->count() }} dari {{ $records->total() }} Sapi</span>
        </div>

        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
            @forelse($records as $rec)
            @php
                $badge = $rec->status_badge;
                $gcalUrl = $rec->getGoogleCalendarUrl();
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-slate-800 tabular-nums break-all">
                        {{ $rec->cow->cow_nik ?? 'N/A' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shrink-0 {{ $badge['bg'] }}">
                        {{ $badge['label'] }}
                    </span>
                </div>

                <div>
                    <a href="{{ route('cows.show', $rec->cow->id) }}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors block">
                        {{ $rec->cow->name ?? 'Sapi ' . $rec->cow->cow_nik }}
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">({{ $rec->cow->breed }})</span>
                    </a>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Peternak: <strong class="text-slate-900 dark:text-white">{{ $rec->cow->farmer->name ?? '-' }}</strong>
                        @if(auth()->check() && !auth()->user()->isGuest())
                        <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400 tabular-nums">(NIK: {{ $rec->cow->farmer->nik ?? '-' }})</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $rec->cow->regency->name ?? '-' }} &bull; Kec. {{ $rec->cow->district->name ?? '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[11px] tabular-nums">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[10px]">Tanggal IB &amp; Petugas</span>
                        <strong class="text-slate-900 dark:text-white">{{ $rec->insemination_date ? $rec->insemination_date->format('d/m/Y') : '-' }}</strong>
                        <span class="block text-[10px] text-slate-500 truncate">{{ $rec->officer_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[10px]">Estimasi HPL (283 Hr)</span>
                        <strong class="text-amber-700 dark:text-amber-300">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d/m/Y') : '-' }}</strong>
                        @if($rec->actual_birth_date)
                        <span class="block text-[10px] text-teal-600 dark:text-teal-400 font-bold">Lahir: {{ $rec->actual_birth_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <div class="col-span-2 pt-1.5 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-[10px]">
                        <span>30 Hr: <strong class="text-sky-700 dark:text-sky-300">{{ $rec->day30_date ? $rec->day30_date->format('d/m/Y') : '-' }}</strong></span>
                        <span>90 Hr: <strong class="text-emerald-700 dark:text-emerald-300">{{ $rec->day90_date ? $rec->day90_date->format('d/m/Y') : '-' }}</strong></span>
                        <span>245 Hr: <strong class="text-amber-700 dark:text-amber-300">{{ $rec->day245_date ? $rec->day245_date->format('d/m/Y') : '-' }}</strong></span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-0.5">
                    <a href="{{ route('cows.show', $rec->cow->id) }}" class="flex-1 min-h-[38px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-colors">
                        <i class="fa-solid fa-eye"></i> Detail Sapi
                    </a>
                    <a href="{{ $gcalUrl }}" target="_blank" rel="noopener noreferrer" class="min-h-[38px] inline-flex items-center justify-center gap-1 px-3 py-2 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950 text-sky-700 dark:text-sky-300 rounded-xl text-xs font-bold border border-sky-200 dark:border-sky-800 transition-colors">
                        <i class="fa-solid fa-calendar-plus"></i> + Cal
                    </a>
                    @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) && $rec->status !== 'Gagal/Keguguran' && $rec->status !== 'Melahirkan (Selesai)')
                    <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" onsubmit="return confirm('Tandai rekam IB sapi {{ $rec->cow->cow_nik }} GAGAL / KEGUGURAN?')" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="confirm_no">
                        <button type="submit" class="min-h-[38px] px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl border border-rose-200 dark:border-rose-800 transition-colors">
                            <i class="fa-solid fa-xmark mr-0.5"></i> Gagal
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-cow text-3xl mb-2 block text-slate-300 dark:text-slate-700"></i>
                <p class="text-xs font-semibold">Tidak ada data kebuntingan sapi yang cocok.</p>
            </div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi &amp; Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Sapi &amp; Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Tanggal Inseminasi &amp; IB ke-</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">3 Tahap (30 / 90 / Bln 9)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Umur &amp; Estimasi Lahir</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Fase &amp; Status Milestone</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi &amp; Pengingat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($records as $rec)
                    @php
                        $badge = $rec->status_badge;
                        $gcalUrl = $rec->getGoogleCalendarUrl();
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-1 rounded border border-emerald-200 dark:border-slate-800 inline-block tabular-nums">
                                {{ $rec->cow->cow_nik ?? 'N/A' }}
                            </span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                <strong class="text-slate-700 dark:text-slate-300">{{ $rec->cow->regency->name ?? '-' }}</strong> &bull; Kec. {{ $rec->cow->district->name ?? '-' }}
                            </p>
                        </td>

                        <td class="px-4 py-3.5">
                            <a href="{{ route('cows.show', $rec->cow->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors text-xs flex items-center gap-1.5">
                                {{ $rec->cow->name ?? 'Sapi ' . $rec->cow->cow_nik }}
                                <span class="text-[10px] font-normal text-slate-500 dark:text-slate-400">({{ $rec->cow->breed }})</span>
                            </a>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Peternak: <strong class="text-slate-800 dark:text-slate-300">{{ $rec->cow->farmer->name ?? '-' }}</strong>
                                @if(auth()->check() && !auth()->user()->isGuest())
                                <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 tabular-nums">(NIK: {{ $rec->cow->farmer->nik ?? '-' }})</span>
                                @endif
                            </p>
                        </td>

                        <td class="px-4 py-3.5 tabular-nums">
                            <span class="font-bold text-slate-900 dark:text-slate-200">{{ $rec->insemination_date ? $rec->insemination_date->format('d/m/Y') : '-' }}</span>
                            <span class="ml-1 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold">IB ke-{{ $rec->ib_ke ?? 1 }}</span>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Inseminator: {{ $rec->officer_name ?? '-' }}</p>
                        </td>

                        <td class="px-4 py-3.5 text-[11px] text-slate-700 dark:text-slate-300 space-y-0.5 tabular-nums">
                            <p>Tahap 1 (30 Hr): <strong class="text-sky-700 dark:text-sky-300">{{ $rec->day30_date ? $rec->day30_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>Tahap 2 (PKB 90): <strong class="text-emerald-700 dark:text-emerald-300">{{ $rec->day90_date ? $rec->day90_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>Tahap 3 (Bln 9): <strong class="text-amber-700 dark:text-amber-300">{{ ($rec->target_month9_date ?? $rec->estimated_birth_date)?->format('d/m/Y') ?? '-' }}</strong></p>
                        </td>

                        <td class="px-4 py-3.5 tabular-nums">
                            <span class="font-bold text-emerald-700 dark:text-emerald-400 text-xs block">{{ $rec->pregnancy_age_label }}</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-300 font-semibold">Est: {{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d/m/Y') : '-' }}</span>
                            @if($rec->actual_birth_date)
                            <p class="text-[10px] text-teal-700 dark:text-teal-400 font-bold mt-0.5">Lahir: {{ $rec->actual_birth_date->format('d/m/Y') }}</p>
                            @endif
                        </td>

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

                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ $gcalUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-2.5 py-1.5 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950 dark:hover:bg-sky-900 text-sky-700 dark:text-sky-300 rounded-lg text-[10px] font-bold border border-sky-200 dark:border-sky-800 transition-colors">
                                    <i class="fa-solid fa-calendar-plus mr-1"></i> + Cal
                                </a>

                                @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()) && $rec->status !== 'Gagal/Keguguran' && $rec->status !== 'Melahirkan (Selesai)')
                                <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" onsubmit="return confirm('Tandai rekam IB sapi {{ $rec->cow->cow_nik }} GAGAL / KEGUGURAN?')" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="confirm_no">
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-bold rounded-lg border border-rose-200 dark:border-rose-800 transition-colors" title="Tandai Gagal / Keguguran">
                                        <i class="fa-solid fa-xmark mr-0.5"></i> Gagal
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('cows.show', $rec->cow->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-[11px] font-semibold border border-slate-300 dark:border-slate-700 transition-colors">
                                    <i class="fa-solid fa-eye mr-1 text-emerald-600 dark:text-emerald-400"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-cow text-3xl mb-3 block text-slate-300 dark:text-slate-700"></i>
                            <p class="text-sm font-semibold">Tidak ada data kebuntingan sapi yang cocok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
            {{ $records->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
