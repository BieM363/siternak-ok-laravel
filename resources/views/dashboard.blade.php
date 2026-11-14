@extends('layouts.app')

@section('title', 'Dashboard Pemantauan Kehamilan Sapi')

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

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('farmers.index') }}" class="min-h-[38px] px-3.5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-users"></i>
                        <span>Data Peternak</span>
                    </a>
                    <a href="{{ route('regions.index') }}" class="min-h-[38px] px-3.5 py-2 bg-teal-700 hover:bg-teal-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Wilayah Gorontalo</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="min-h-[38px] px-3.5 py-2 bg-sky-700 hover:bg-sky-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                    <a href="{{ route('straws.index') }}" class="min-h-[38px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-vial text-teal-400"></i>
                        <span>Straw Pejantan</span>
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
                            <span data-operator-countdown class="font-mono text-white">08:00:00</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Selamat bertugas di lapangan, <strong class="text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->regency->name ?? 'Provinsi Gorontalo' }}). Waktu login dibatasi maksimal <strong>8 jam</strong> melalui cronjob otomatis dengan dukungan notifikasi peramban ponsel.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('cows.create') }}" class="min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold transition flex items-center gap-2">
                        <i class="fa-solid fa-plus text-sm"></i>
                        <span>Pendaftaran IB Baru</span>
                    </a>
                    <button type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.testMobileNotification()" class="min-h-[40px] px-3.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <span>Tes Notifikasi HP</span>
                    </button>
                    <a href="{{ route('straws.index') }}" class="min-h-[40px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-teal-300 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-vial"></i>
                        <span>Cek Straw</span>
                    </a>
                    <a href="{{ route('reports.inseminators') }}" class="min-h-[40px] px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-xl text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy"></i>
                        <span>Leaderboard IB</span>
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
                            Sistem Pemantauan Kehamilan Sapi &bull; Disnakbun Provinsi Gorontalo
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
    <section
        id="reminder-section"
        class="glass-card p-4 sm:p-6 rounded-2xl border-2 border-emerald-600/60 bg-slate-900 text-white shadow-sm"
        x-data="{ showPushModal: false }"
    >
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <h2 class="font-extrabold text-xs sm:text-sm text-emerald-300 uppercase tracking-wide tabular-nums">
                        Reminder Konfirmasi (Batas 3 Hari): {{ $confirmationReminders->count() }} Sapi Membutuhkan Aksi
                    </h2>
                </div>
                <p class="text-xs text-slate-300">
                    Sapi betina di bawah telah memasuki jadwal milestone atau fase melahirkan. Mohon konfirmasi YA atau TIDAK.
                </p>
            </div>

            <button
                @click="showPushModal = true"
                type="button"
                class="min-h-[38px] px-3.5 py-2 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition flex items-center gap-1.5 shrink-0 w-full sm:w-auto justify-center"
            >
                <i class="fa-solid fa-bell"></i>
                <span>Notifikasi HP / Peramban</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 mt-4">
            @foreach($confirmationReminders as $rec)
                @php
                    $cow = $rec->cow;
                    $milestoneLabel = 'Konfirmasi';
                    $targetDate = null;

                    if (in_array($rec->status, ['Pasca IB', 'Estimasi 30 Hari'])) {
                        $milestoneLabel = 'PKB 30 Hari';
                        $targetDate = $rec->day30_date;
                    } elseif ($rec->status === 'Bunting (PKB 90 Hari)') {
                        $milestoneLabel = 'PKB 90 Hari';
                        $targetDate = $rec->day90_date;
                    } elseif ($rec->status === 'Siap Melahirkan (245 Hari)') {
                        $milestoneLabel = 'PKB 245 Hari';
                        $targetDate = $rec->day245_date;
                    } elseif ($rec->status === 'Fase Melahirkan') {
                        $milestoneLabel = 'Konfirmasi Kelahiran';
                        $targetDate = $rec->estimated_birth_date;
                    }
                @endphp

                @if($cow)
                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-bold text-emerald-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800 truncate tabular-nums">
                                {{ $cow->cow_nik }}
                            </span>
                            <span class="px-2 py-0.5 bg-teal-950 text-teal-300 font-bold text-[10px] rounded border border-teal-800 shrink-0">
                                {{ $rec->status }}
                            </span>
                        </div>

                        <h3 class="font-bold text-sm text-white mt-2">
                            {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                            @if($cow->breed)
                                <span class="text-slate-400 font-normal">({{ $cow->breed }})</span>
                            @endif
                        </h3>

                        <div class="space-y-1.5 text-xs text-slate-300 mt-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Peternak:</span>
                                <strong class="text-slate-100 font-bold truncate max-w-[60%] text-right">{{ $cow->farmer->name ?? '-' }}</strong>
                            </div>

                            @if(auth()->check() && !auth()->user()->isGuest())
                            <div class="flex items-center justify-between gap-2 text-[11px]">
                                <span class="text-slate-400">NIK Peternak:</span>
                                <span class="font-mono text-emerald-300 font-semibold text-right tabular-nums">{{ $cow->farmer->nik ?? '-' }}</span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Milestone:</span>
                                <strong class="text-amber-300 font-bold text-right">{{ $milestoneLabel }}</strong>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Tgl Konfirmasi:</span>
                                <strong class="text-emerald-300 text-right tabular-nums">{{ $targetDate ? \Carbon\Carbon::parse($targetDate)->format('d M Y') : '-' }}</strong>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Estimasi HPL:</span>
                                <strong class="text-amber-400 font-mono font-bold text-right tabular-nums">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d M Y') : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <a
                                href="{{ $rec->getGoogleCalendarUrl() }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sky-400 hover:underline font-bold"
                            >
                                <i class="fa-solid fa-calendar-plus mr-1"></i>
                                + Google Calendar
                            </a>
                            <span class="text-amber-400 font-bold">Max 3 Hari</span>
                        </div>

                        @if(auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin()))
                            @if($rec->status === 'Fase Melahirkan')
                                <div
                                    x-data="{
                                        showBirthModal: false,
                                        calfName: 'Anak {{ addslashes($cow->name ?? 'Sapi') }} 1',
                                        calfGender: 'Jantan',
                                        calfBreed: '{{ addslashes($cow->breed ?? '') }}',
                                        calfWeight: '30',
                                        calfCondition: 'Sehat'
                                    }"
                                    @keydown.escape.window="showBirthModal = false"
                                    class="space-y-2"
                                >
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            @click="showBirthModal = true"
                                            class="flex-1 min-h-[36px] py-1.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition"
                                        >
                                            <i class="fa-solid fa-check mr-1"></i>
                                            YA (Melahirkan)
                                        </button>

                                        <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="birth_no">
                                            <button
                                                type="submit"
                                                onclick="return confirm('Sapi gagal melahirkan/gugur?')"
                                                class="min-h-[36px] py-1.5 px-3 bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition"
                                            >
                                                <i class="fa-solid fa-xmark mr-1"></i>
                                                TIDAK
                                            </button>
                                        </form>
                                    </div>

                                    <div
                                        x-show="showBirthModal"
                                        x-cloak
                                        x-transition.opacity
                                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80"
                                        role="dialog"
                                        aria-modal="true"
                                    >
                                        <div
                                            @click.away="showBirthModal = false"
                                            x-transition
                                            class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                                        >
                                            <div class="px-6 py-4 bg-emerald-700 text-white flex items-center justify-between">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                                                        <i class="fa-solid fa-baby-carriage text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-extrabold text-sm">Konfirmasi Kelahiran</h3>
                                                        <p class="text-[11px] text-emerald-100">Pencatatan data pedet (anak sapi) baru</p>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="showBirthModal = false"
                                                    aria-label="Tutup modal konfirmasi kelahiran"
                                                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center"
                                                >
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>

                                            <div class="px-6 pt-5">
                                                <div class="bg-slate-100 dark:bg-slate-950 rounded-xl p-3.5 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                                                    <div>
                                                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">Induk Sapi</p>
                                                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}</p>
                                                        <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 mt-0.5 tabular-nums">NIK: {{ $cow->cow_nik }}</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">Ras Induk</p>
                                                        <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">{{ $cow->breed ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="p-6 space-y-4">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="birth_yes">

                                                <div>
                                                    <label for="calf_name_{{ $rec->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                        Nama Anak Sapi
                                                    </label>
                                                    <input
                                                        id="calf_name_{{ $rec->id }}"
                                                        type="text"
                                                        name="calf_name"
                                                        x-model="calfName"
                                                        required
                                                        class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600"
                                                    >
                                                    <p class="text-[10px] text-slate-500 mt-1">Nama otomatis mengikuti nama induk dan masih dapat diubah.</p>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="calf_gender_{{ $rec->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                            Jenis Kelamin
                                                        </label>
                                                        <select
                                                            id="calf_gender_{{ $rec->id }}"
                                                            name="calf_gender"
                                                            x-model="calfGender"
                                                            required
                                                            class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white"
                                                        >
                                                            <option value="Jantan">Jantan</option>
                                                            <option value="Betina">Betina</option>
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="calf_breed_{{ $rec->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                            Ras Anak
                                                        </label>
                                                        <select
                                                            id="calf_breed_{{ $rec->id }}"
                                                            name="calf_breed"
                                                            x-model="calfBreed"
                                                            required
                                                            class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white"
                                                        >
                                                            @foreach($breeds as $breed)
                                                                <option value="{{ $breed }}">{{ $breed }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="calf_weight_{{ $rec->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                            Berat Lahir (kg)
                                                        </label>
                                                        <input
                                                            id="calf_weight_{{ $rec->id }}"
                                                            type="number"
                                                            step="0.1"
                                                            min="0"
                                                            name="calf_weight"
                                                            x-model="calfWeight"
                                                            class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600 tabular-nums"
                                                        >
                                                    </div>

                                                    <div>
                                                        <label for="calf_condition_{{ $rec->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                            Kondisi
                                                        </label>
                                                        <select
                                                            id="calf_condition_{{ $rec->id }}"
                                                            name="calf_condition"
                                                            x-model="calfCondition"
                                                            class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600"
                                                        >
                                                            <option value="Sehat">Sehat</option>
                                                            <option value="Cacat">Cacat</option>
                                                            <option value="Mati">Mati</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-xl p-3 flex gap-2">
                                                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
                                                    <p class="text-[10px] leading-relaxed text-amber-800 dark:text-amber-300">
                                                        Nama dan ras anak sudah diisi otomatis berdasarkan induk. Petugas dapat mengubahnya sebelum menyimpan.
                                                    </p>
                                                </div>

                                                <div class="flex gap-2 pt-2">
                                                    <button
                                                        type="button"
                                                        @click="showBirthModal = false"
                                                        class="flex-1 min-h-[38px] py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl"
                                                    >
                                                        Batal
                                                    </button>
                                                    <button
                                                        type="submit"
                                                        class="flex-1 min-h-[38px] py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl"
                                                    >
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        Simpan Kelahiran
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="confirm_yes">
                                        <button
                                            type="submit"
                                            class="w-full min-h-[38px] py-2 px-3 bg-emerald-700 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-1.5"
                                        >
                                            <i class="fa-solid fa-check"></i>
                                            <span>YA (Bunting)</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="confirm_no">
                                        <button
                                            type="submit"
                                            onclick="return confirm('Konfirmasi TIDAK (Gagal Bunting)?')"
                                            class="w-full min-h-[38px] py-2 px-3 bg-rose-700 hover:bg-rose-600 text-white font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-1.5"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>TIDAK</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div
            x-show="showPushModal"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="showPushModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80"
            role="dialog"
            aria-modal="true"
        >
            <div
                @click.away="showPushModal = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4 text-slate-900 dark:text-white"
            >
                <div class="flex items-center justify-between text-emerald-700 dark:text-emerald-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">
                                Notifikasi Peramban Ponsel &amp; Sesi 8 Jam
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Dukungan Browser HP (Chrome, Safari, Firefox, Samsung)
                            </p>
                        </div>
                    </div>
                    <button @click="showPushModal = false" type="button" aria-label="Tutup modal notifikasi" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Dirancang bagi <strong>Operator Inseminator yang bertugas di lapangan menggunakan HP</strong>. Sistem mengirimkan notifikasi ke peramban ponsel Anda selama sesi login kerja <strong>8 jam</strong> aktif.
                </p>

                @if(auth()->check() && auth()->user()->isOperator())
                <div class="bg-emerald-50 dark:bg-emerald-950/60 p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-800/80 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-clock"></i> Sisa Waktu Login (Maks 8 Jam):
                        </span>
                        <span data-operator-countdown class="font-mono font-black text-emerald-700 dark:text-emerald-300 bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800 tabular-nums">
                            08:00:00
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                        Cronjob (<code class="text-emerald-700 dark:text-emerald-400 font-bold">ternak:operator-session-cron</code>) otomatis mengakhiri sesi setelah 8 jam dan menyinkronkan pengingat 3 hari.
                    </p>
                </div>
                @endif

                <div class="bg-slate-100 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-slate-600 dark:text-slate-400">Status Notifikasi Peramban:</span>
                        <span data-browser-perm-badge class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                            AKTIF
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Antrean Sapi Butuh Konfirmasi:</span>
                        <strong class="text-amber-600 dark:text-amber-400 font-bold tabular-nums">{{ $confirmationReminders->count() }} Sapi Betina</strong>
                    </div>
                </div>

                <div class="space-y-2 pt-1">
                    <button
                        data-btn-request-perm
                        type="button"
                        onclick="window.SiTernakNotifier && window.SiTernakNotifier.requestPermission()"
                        class="w-full min-h-[38px] py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-bell"></i>
                        <span>Izinkan Notifikasi Sistem pada Peramban HP Ini</span>
                    </button>

                    <button
                        type="button"
                        @click="showPushModal = false; window.SiTernakNotifier && window.SiTernakNotifier.syncCronAndNotify()"
                        class="w-full min-h-[38px] py-2.5 px-4 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Uji Notifikasi ke Peramban Ponsel Sekarang</span>
                    </button>
                </div>

                <div class="flex justify-end pt-1">
                    <button
                        @click="showPushModal = false"
                        type="button"
                        class="min-h-[36px] px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Sapi Betina</span>
                <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1 tabular-nums">{{ $totalCows }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-400 flex items-center justify-center font-bold border border-sky-200 dark:border-sky-800">
                <i class="fa-solid fa-cow text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border-2 border-emerald-600/40 dark:border-emerald-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Sapi Bunting (Aktif)</span>
                <p class="text-xl sm:text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums">{{ $totalActivePregnancies }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-200 dark:border-emerald-800">
                <i class="fa-solid fa-syringe text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Siap Melahirkan (HPL)</span>
                <p class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 tabular-nums">{{ $totalReadyToGiveBirth }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold border border-amber-200 dark:border-amber-800">
                <i class="fa-solid fa-clock text-lg"></i>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-teal-700 dark:text-teal-400 uppercase tracking-wider block">Anak Sapi Lahir</span>
                <p class="text-xl sm:text-2xl font-black text-teal-700 dark:text-teal-400 mt-1 tabular-nums">{{ $totalCalvesBorn }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold border border-teal-200 dark:border-teal-800">
                <i class="fa-solid fa-baby-carriage text-lg"></i>
            </div>
        </div>
    </div>

    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="dashboard-search" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Cari NIK Sapi / Nama / Peternak</label>
                <div class="relative">
                    <input id="dashboard-search" type="text" name="search" value="{{ $search }}" placeholder="Ketik NIK Sapi, Nama, atau Peternak..."
                        class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label for="dashboard-regency" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kabupaten / Kota</label>
                <select id="dashboard-regency" name="regency_id" class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600" {{ auth()->check() && auth()->user()->isAdminKabupaten() ? 'disabled' : '' }}>
                    <option value="">-- Semua Kabupaten --</option>
                    @foreach($regencies as $r)
                    <option value="{{ $r->id }}" {{ $regencyId == $r->id ? 'selected' : '' }}>{{ $r->code }} - {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dashboard-officer" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Inseminator / Petugas
                </label>

                @if(auth()->check() && auth()->user()->isOperator())
                    <input
                        id="dashboard-officer"
                        type="text"
                        value="{{ auth()->user()->name }}"
                        disabled
                        class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-600 dark:text-slate-400 cursor-not-allowed"
                    >
                @else
                    <select
                        id="dashboard-officer"
                        name="officer_name"
                        class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600"
                    >
                        <option value="">-- Semua Petugas --</option>
                        @foreach($officers as $off)
                            <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label for="dashboard-status" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Fase Kebuntingan</label>
                <select id="dashboard-status" name="status" class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
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

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full min-h-[38px] py-2.5 px-4 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if($search || $regencyId || $statusFilter || $officerName)
                <a href="{{ route('dashboard') }}" aria-label="Reset filter" class="min-h-[38px] py-2.5 px-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl shadow-sm overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900">
            <h2 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400"></i>
                Daftar Pemantauan Kehamilan, Tanggal Milestone &amp; Sisa Hari
            </h2>
            <span class="text-xs text-slate-500 dark:text-slate-400 tabular-nums">Menampilkan {{ $records->count() }} dari {{ $records->total() }} Sapi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">NIK Sapi &amp; Wilayah</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Sapi &amp; Peternak</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Tgl Inseminasi (IB)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Milestone (30/90/245 Hr)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Estimasi HPL (283 Hari)</th>
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
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Inseminator: {{ $rec->officer_name ?? '-' }}</p>
                        </td>

                        <td class="px-4 py-3.5 text-[11px] text-slate-700 dark:text-slate-300 space-y-0.5 tabular-nums">
                            <p>30 Hr: <strong class="text-sky-700 dark:text-sky-300">{{ $rec->day30_date ? $rec->day30_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>90 Hr (PKB): <strong class="text-emerald-700 dark:text-emerald-300">{{ $rec->day90_date ? $rec->day90_date->format('d/m/Y') : '-' }}</strong></p>
                            <p>245 Hr: <strong class="text-amber-700 dark:text-amber-300">{{ $rec->day245_date ? $rec->day245_date->format('d/m/Y') : '-' }}</strong></p>
                        </td>

                        <td class="px-4 py-3.5 tabular-nums">
                            <span class="font-bold text-amber-700 dark:text-amber-300 text-xs">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d/m/Y') : '-' }}</span>
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
                            <p class="text-sm font-semibold">Tidak ada data kehamilan sapi yang cocok.</p>
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
