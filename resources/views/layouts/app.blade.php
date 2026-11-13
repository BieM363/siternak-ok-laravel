<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SiTernak-OK">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'Dashboard') - SiTernak-OK (Dinas Peternakan & Perkebunan Prov. Gorontalo)</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Flatpickr Interactive Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Tailwind CSS CDN & Alpine JS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Light Mode Glassmorphism & Card Styling */
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.05);
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Dark Mode Glassmorphism & Card Styling */
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }
        .dark .glass-header {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: #1e293b; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col font-sans antialiased overflow-x-hidden" x-data="{ mobileMenuOpen: false, adminDropdownOpen: false, userDropdownOpen: false, notifDropdownOpen: false }">

    <!-- Floating Mobile & Cross-Browser Push Notification Banner (Muncul di Semua Peramban HP & Desktop) -->
    <div id="mobile-push-toast" class="fixed top-3 inset-x-3 sm:inset-x-auto sm:right-5 sm:w-96 z-[10050] hidden transform transition-all duration-300">
        <div class="bg-slate-900/95 dark:bg-slate-900 text-white border-2 border-emerald-500/70 rounded-2xl shadow-2xl p-3.5 backdrop-blur-xl">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shrink-0 shadow-lg">
                    <i class="fa-solid fa-bell text-base animate-bounce"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1">
                            <i class="fa-solid fa-mobile-screen-button"></i> Notifikasi Peramban
                        </span>
                        <button type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.hideToast()" class="text-slate-400 hover:text-white text-xs p-0.5">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <h4 id="mobile-push-toast-title" class="font-extrabold text-xs text-white mt-0.5 truncate">SiTernak-OK Gorontalo</h4>
                    <p id="mobile-push-toast-body" class="text-[11px] text-slate-300 mt-0.5 leading-snug line-clamp-3"></p>
                    <div class="flex items-center gap-2 mt-2.5">
                        <a id="mobile-push-toast-link" href="{{ route('dashboard') }}#reminder-section" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] rounded-lg transition shadow flex items-center gap-1">
                            <i class="fa-solid fa-check-double"></i>
                            <span>Buka & Konfirmasi</span>
                        </a>
                        <button type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.hideToast()" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-[11px] rounded-lg transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Sticky Header Navigation Bar -->
    <header class="glass-header sticky top-0 z-50 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-2">

                <!-- Left Side: Brand Logo & Title -->
                <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-2.5 group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-500 to-emerald-400 flex items-center justify-center text-white shadow-md shadow-emerald-600/30 group-hover:scale-105 transition-transform shrink-0">
                            <i class="fa-solid fa-cow text-base sm:text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white tracking-tight leading-none">
                                SiTernak<span class="text-emerald-500">-OK</span>
                            </span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-semibold tracking-wider uppercase mt-0.5 whitespace-nowrap">Disnakbun Gorontalo</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Desktop Horizontal Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-1 whitespace-nowrap">
                    <a href="{{ route('home') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('home') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-house {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-500' }}"></i>
                        <span>Utama</span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-chart-line {{ request()->routeIs('dashboard') ? 'text-white' : 'text-emerald-500' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('cows.index') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('cows.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-cow {{ request()->routeIs('cows.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                        <span>Sapi IB</span>
                    </a>

                    <a href="{{ route('calves.index') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('calves.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-baby-carriage {{ request()->routeIs('calves.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                        <span>Pedet</span>
                    </a>

                    <a href="{{ route('reports.index') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('reports.index') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-file-invoice {{ request()->routeIs('reports.index') ? 'text-white' : 'text-emerald-500' }}"></i>
                        <span>Kelahiran</span>
                    </a>

                    <a href="{{ route('reports.inseminators') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('reports.inseminators') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-trophy {{ request()->routeIs('reports.inseminators') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Inseminator</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <!-- Standalone Straw Pejantan Menu -->
                    <a href="{{ route('straws.index') }}" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('straws.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-vial {{ request()->routeIs('straws.*') ? 'text-white' : 'text-purple-500' }}"></i>
                        <span>Straw Pejantan</span>
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <!-- Dropdown Menu Administrasi KHUSUS ADMIN (Provinsi, Kota, Kabupaten) -->
                    <div class="relative" @click.away="adminDropdownOpen = false">
                        <button @click="adminDropdownOpen = !adminDropdownOpen" type="button" class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('farmers.*') || request()->routeIs('regions.*') || request()->routeIs('users.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                            <i class="fa-solid fa-user-shield {{ request()->routeIs('farmers.*') || request()->routeIs('regions.*') || request()->routeIs('users.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                            <span>Administrasi</span>
                            <i class="fa-solid fa-chevron-down text-[10px] ml-0.5"></i>
                        </button>

                        <div x-show="adminDropdownOpen" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl py-2 z-50">
                            <a href="{{ route('farmers.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-users text-emerald-500 w-4 text-center"></i> Data Peternak
                            </a>
                            <a href="{{ route('regions.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-map-location-dot text-emerald-500 w-4 text-center"></i> Wilayah Administratif
                            </a>
                            <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-user-gear text-emerald-500 w-4 text-center"></i> Kelola Role User
                            </a>
                        </div>
                    </div>
                    @endif
                </nav>

                <!-- Right Side: Theme Switcher, Notification Bell & Profile User Dropdown -->
                <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
                    
                    <!-- Mode Terang / Gelap Switcher Toggle -->
                    <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" 
                        class="p-2 sm:p-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl text-xs transition border border-slate-200 dark:border-slate-700 flex items-center justify-center" 
                        title="Ganti Mode Terang / Gelap">
                        <i class="fa-solid text-sm" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-600'"></i>
                    </button>

                    @if(auth()->check() && !auth()->user()->isGuest())
                    @if(auth()->user()->isOperator())
                    <!-- Indikator Sisa Waktu Login 8 Jam Operator (Tampil di Desktop & Tablet) -->
                    <div class="hidden md:flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-[11px] font-bold text-emerald-700 dark:text-emerald-300" title="Batas waktu login operator lapangan maksimal 8 jam">
                        <i class="fa-solid fa-stopwatch text-emerald-500 animate-pulse"></i>
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400">Sesi 8 Jam:</span>
                        <span data-operator-countdown class="font-mono font-extrabold text-emerald-700 dark:text-emerald-300">08:00:00</span>
                    </div>
                    @endif

                    <!-- Tombol Lonceng Notifikasi Peramban Ponsel / Semua Browser -->
                    <div class="relative" @click.away="notifDropdownOpen = false">
                        <button @click="notifDropdownOpen = !notifDropdownOpen" type="button"
                            class="relative p-2 sm:p-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-emerald-400 rounded-xl text-xs transition border border-slate-200 dark:border-slate-700 flex items-center justify-center"
                            title="Notifikasi Peramban Ponsel & Status Sesi 8 Jam">
                            <i class="fa-solid fa-bell text-sm text-amber-500"></i>
                            <span id="header-notif-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shadow">
                                0
                            </span>
                        </button>

                        <!-- Panel Dropdown Notifikasi Peramban Ponsel & Sesi 8 Jam -->
                        <div x-show="notifDropdownOpen" x-cloak x-transition
                             class="fixed sm:absolute inset-x-3 sm:inset-x-auto top-16 sm:top-auto sm:right-0 sm:mt-2 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-4 z-50 space-y-3 max-h-[82vh] overflow-y-auto">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-500 flex items-center justify-center">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-xs text-slate-900 dark:text-white">Notifikasi Peramban Ponsel</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Kompatibel Semua Browser HP & Desktop</p>
                                    </div>
                                </div>
                                <button @click="notifDropdownOpen = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            @if(auth()->user()->isOperator())
                            <!-- Kartu Sesi Login 8 Jam Operator -->
                            <div class="p-3 rounded-xl bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock"></i> Sesi Login Operator (Maks 8 Jam)
                                    </span>
                                    <span data-operator-countdown class="font-mono font-black text-xs text-emerald-700 dark:text-emerald-300 bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                        08:00:00
                                    </span>
                                </div>
                                <div class="w-full bg-emerald-200/70 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                    <div id="operator-session-progress" class="bg-emerald-600 h-full transition-all duration-500" style="width: 100%"></div>
                                </div>
                                <p class="text-[10px] text-slate-600 dark:text-slate-400 flex items-center justify-between">
                                    <span>Login: <strong id="operator-login-time">-</strong></span>
                                    <span>Berakhir: <strong id="operator-expire-time">-</strong></span>
                                </p>
                            </div>
                            @endif

                            <!-- Status Izin Notifikasi Peramban -->
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400 font-semibold">Status Peramban:</span>
                                    <span id="browser-perm-badge" class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                                        Mendeteksi...
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 gap-1.5 pt-1">
                                    <button id="btn-request-perm" type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.requestPermission()"
                                        class="w-full py-2 px-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[11px] rounded-xl shadow transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-bell"></i>
                                        <span>Aktifkan Izin Notifikasi Peramban HP</span>
                                    </button>
                                    <button type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.testMobileNotification()"
                                        class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] rounded-xl shadow transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Kirim Notifikasi ke Peramban HP Sekarang</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Daftar Sapi Butuh Konfirmasi -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Pengingat Konfirmasi Sapi (Batas 3 Hari)
                                    </span>
                                    <span id="dropdown-reminder-count" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">0 Sapi</span>
                                </div>
                                <div id="dropdown-reminder-list" class="space-y-2 max-h-52 overflow-y-auto pr-1">
                                    <p class="text-[11px] text-slate-400 text-center py-3">Memuat data pengingat...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Button -->
                    <div class="relative" @click.away="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" type="button" class="flex items-center space-x-1.5 sm:space-x-2 p-1 sm:p-1.5 sm:pr-3 bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 transition">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-bold text-xs flex items-center justify-center shadow shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight truncate max-w-[110px]">{{ auth()->user()->name }}</span>
                                <span class="text-[9px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                    {{ auth()->user()->getRoleBadgeText() }}
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5"></i>
                        </button>

                        <div x-show="userDropdownOpen" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 space-y-1">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 rounded text-[9px] font-bold uppercase border border-emerald-200 dark:border-emerald-800">
                                    {{ auth()->user()->getRoleBadgeText() }}
                                </span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Keluar / Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <!-- Login Button for Guests -->
                    <a href="{{ route('login') }}" class="px-3 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                    @endif

                    <!-- Mobile Hamburger Toggle Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 sm:p-2.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-sm border border-slate-200 dark:border-slate-700 shrink-0">
                        <i class="fa-solid" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>

                </div>

            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu (Root Level Fixed Overlay - Terpisah dari Sticky Header) -->
    <div x-cloak x-show="mobileMenuOpen" class="lg:hidden fixed inset-0 z-[9999]" style="display: none;">
        <!-- Backdrop Overlay -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" 
             @click="mobileMenuOpen = false"></div>

        <!-- Sidebar Drawer Container -->
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-[10000]">
            <aside x-show="mobileMenuOpen" 
                   x-transition:enter="transform transition ease-in-out duration-300" 
                   x-transition:enter-start="translate-x-full" 
                   x-transition:enter-end="translate-x-0" 
                   x-transition:leave="transform transition ease-in-out duration-200" 
                   x-transition:leave-start="translate-x-0" 
                   x-transition:leave-end="translate-x-full" 
                   class="w-screen max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col p-5 space-y-4 overflow-y-auto h-full">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-bold shadow">
                            <i class="fa-solid fa-cow"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm">SiTernak-OK</span>
                            <p class="text-[9px] text-slate-400 uppercase font-semibold">Gorontalo</p>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" type="button" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                @if(auth()->check() && !auth()->user()->isGuest())
                <!-- Mobile User Info Card -->
                <div class="p-3 bg-slate-50 dark:bg-slate-950/80 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2.5 overflow-hidden">
                    <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1 overflow-hidden">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[9px] font-extrabold uppercase border border-emerald-300 dark:border-emerald-800 shrink-0">
                        {{ auth()->user()->getRoleBadgeText() }}
                    </span>
                </div>

                @if(auth()->user()->isOperator())
                <!-- Mobile Drawer Sesi Login 8 Jam & Notifikasi HP -->
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-2xl border border-emerald-200 dark:border-emerald-800 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-stopwatch text-emerald-500"></i> Sesi Kerja 8 Jam:
                        </span>
                        <span data-operator-countdown class="font-mono font-black text-emerald-700 dark:text-emerald-300">08:00:00</span>
                    </div>
                    <button type="button" @click="mobileMenuOpen = false; window.SiTernakNotifier && window.SiTernakNotifier.testMobileNotification()"
                        class="w-full py-2 px-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[11px] rounded-xl shadow flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <span>Tes Notifikasi Peramban Ponsel</span>
                    </button>
                </div>
                @endif
                @endif

                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('home') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-house text-emerald-500"></i> Utama
                    </a>
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('dashboard') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-chart-line text-emerald-500"></i> Dashboard
                    </a>
                    <a href="{{ route('cows.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('cows.*') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-cow text-emerald-500"></i> Sapi IB
                    </a>
                    <a href="{{ route('calves.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('calves.*') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-baby-carriage text-emerald-500"></i> Pedet
                    </a>
                    <a href="{{ route('reports.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('reports.index') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-file-invoice text-emerald-500"></i> Kelahiran
                    </a>
                    <a href="{{ route('reports.inseminators') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('reports.inseminators') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-trophy text-emerald-500"></i> Ranking
                    </a>
                    <a href="{{ route('farmers.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('farmers.*') ? 'ring-2 ring-emerald-500' : '' }}">
                        <i class="fa-solid fa-users text-emerald-500"></i> Peternak
                    </a>
                    <a href="{{ route('straws.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 {{ request()->routeIs('straws.*') ? 'ring-2 ring-purple-500' : '' }}">
                        <i class="fa-solid fa-vial text-purple-500"></i> Straw IB
                    </a>
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Administrasi Wilayah</p>
                    <div class="space-y-1 text-xs font-bold">
                        <a href="{{ route('regions.index') }}" @click="mobileMenuOpen = false" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-map-location-dot text-emerald-500"></i> Wilayah Administratif
                        </a>
                        <a href="{{ route('users.index') }}" @click="mobileMenuOpen = false" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-user-gear text-emerald-500"></i> Kelola Role User
                        </a>
                    </div>
                </div>
                @endif

                <div class="border-t border-slate-100 dark:border-slate-800 pt-3 mt-auto">
                    @if(auth()->check() && !auth()->user()->isGuest())
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Keluar / Sign Out</span>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="block text-center py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Login
                    </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>

    <!-- Sub-Header Status Bar & Actions (Top Bar di bawah Navigation Header) -->
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800/80 py-3 px-3 sm:px-6 lg:px-8 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <h2 class="text-sm sm:text-base lg:text-lg font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 truncate">
                    @yield('title', 'Pemantauan Kehamilan Sapi')
                </h2>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto justify-start sm:justify-end">
                @if(auth()->check() && auth()->user()->isOperator())
                <!-- Indikator Sesi 8 Jam khusus Layar Ponsel -->
                <div class="md:hidden w-full flex items-center justify-between px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 rounded-xl text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-stopwatch text-emerald-500 animate-pulse"></i>
                        <span>Batas Login Operator (8 Jam):</span>
                    </span>
                    <span data-operator-countdown class="font-mono font-black text-emerald-700 dark:text-emerald-300">08:00:00</span>
                </div>

                <form action="{{ route('pregnancy.trigger-cron') }}" method="POST" class="flex-1 sm:flex-initial">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-emerald-700 dark:text-emerald-400 rounded-xl text-xs font-extrabold border border-slate-200 dark:border-slate-700 transition whitespace-nowrap" title="Jalankan otomatisasi simulasi 3 hari secara manual">
                        <i class="fa-solid fa-rotate text-emerald-500"></i>
                        <span class="hidden sm:inline">Check Auto-Advance (3 Hari)</span>
                        <span class="sm:hidden">Check 3 Hari</span>
                    </button>
                </form>

                <!-- KHUSUS OPERATOR: Pendaftaran Inseminasi Buatan (IB) Baru -->
                <a href="{{ route('cows.create') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center space-x-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow transition whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i>
                    <span>+ Pendaftaran IB Baru</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Body Container -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 space-y-6">

        <!-- Banner Public Read-Only View -->
        @if(!auth()->check() || auth()->user()->isGuest())
        <div class="bg-blue-50 dark:bg-slate-900/90 border border-blue-200 dark:border-slate-800 text-blue-900 dark:text-slate-300 px-4 py-3 rounded-2xl flex items-center justify-between text-xs shadow-sm">
            <div class="flex items-center space-x-2.5">
                <i class="fa-solid fa-eye text-blue-600 dark:text-emerald-400 text-base"></i>
                <span>Anda mengakses dalam <strong class="text-blue-700 dark:text-white">Mode Lihat Publik (Read-Only)</strong>. Untuk melakukan penambahan atau konfirmasi data, silakan login ke akun Pengelola.</span>
            </div>
            <a href="{{ route('login') }}" class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold text-[11px] flex-shrink-0 transition">
                Login
            </a>
        </div>
        @endif

        <!-- Flash Alert Messages -->
        @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-500/50 text-emerald-900 dark:text-emerald-200 px-5 py-4 rounded-2xl shadow-md flex items-start space-x-3">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold text-sm text-emerald-900 dark:text-emerald-300">Berhasil!</h4>
                <p class="text-xs text-emerald-800 dark:text-emerald-200/90 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-500/50 text-rose-900 dark:text-rose-200 px-5 py-4 rounded-2xl shadow-md flex items-start space-x-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold text-sm text-rose-900 dark:text-rose-300">Perhatian!</h4>
                <p class="text-xs text-rose-800 dark:text-rose-200/90 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-950 text-slate-500 text-xs py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} Dinas Peternakan dan Perkebunan Provinsi Gorontalo. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    System Active
                </span>
            </div>
        </div>
    </footer>

    <!-- Flatpickr Date Picker & Universal Mobile/Browser Notification Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr.localize(flatpickr.l10ns.id);
                flatpickr("input[type=date]", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                });
            }
        });
    </script>

    @if(auth()->check() && !auth()->user()->isGuest())
    <script>
        (function() {
            const IS_OPERATOR = @json(auth()->user()->isOperator());
            const POLL_URL = @json(route('notifications.poll'));
            const SYNC_CRON_URL = @json(route('notifications.sync-cron'));
            const LOGIN_URL = @json(route('login'));
            const DASHBOARD_REMINDER_URL = @json(route('dashboard')) + '#reminder-section';
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const INITIAL_REMAINING_SECONDS = @json(auth()->user()->getRemainingSessionSeconds());
            let expiresAtTimestamp = INITIAL_REMAINING_SECONDS !== null
                ? Math.floor(Date.now() / 1000) + Number(INITIAL_REMAINING_SECONDS)
                : null;

            let latestReminders = [];
            let swRegistration = null;
            let toastTimeout = null;
            let warned15Min = false;

            // 1. Registrasi Service Worker untuk mendukung notifikasi pada peramban ponsel (Android Chrome, Samsung Internet, Firefox Mobile, Safari iOS PWA, dll.)
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    swRegistration = reg;
                }).catch(function() {});
            }

            // 2. Nada dering lembut Web Audio API + Getar HP (Bekerja di peramban ponsel saat turun lapangan)
            function playMobileAlertFeedback() {
                try {
                    if ('vibrate' in navigator) {
                        navigator.vibrate([200, 100, 200]);
                    }
                } catch (e) {}

                try {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (AudioCtx) {
                        const ctx = new AudioCtx();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(880, ctx.currentTime);
                        osc.frequency.setValueAtTime(1174.66, ctx.currentTime + 0.12);
                        gain.gain.setValueAtTime(0.08, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    }
                } catch (e) {}
            }

            // 3. Tampilkan Banner Notifikasi Melayang di Peramban (Kompatibel 100% di Semua Peramban Ponsel & Desktop)
            function showInBrowserToast(title, body, targetUrl) {
                const toastEl = document.getElementById('mobile-push-toast');
                const titleEl = document.getElementById('mobile-push-toast-title');
                const bodyEl = document.getElementById('mobile-push-toast-body');
                const linkEl = document.getElementById('mobile-push-toast-link');

                if (!toastEl || !titleEl || !bodyEl) return;

                titleEl.textContent = title;
                bodyEl.textContent = body;
                if (linkEl && targetUrl) {
                    linkEl.setAttribute('href', targetUrl);
                }

                toastEl.classList.remove('hidden');
                if (toastTimeout) clearTimeout(toastTimeout);
                toastTimeout = setTimeout(function() {
                    toastEl.classList.add('hidden');
                }, 9000);
            }

            function hideToast() {
                const toastEl = document.getElementById('mobile-push-toast');
                if (toastEl) toastEl.classList.add('hidden');
            }

            // 4. Kirim Notifikasi Universal (Native OS via Service Worker + W3C Notification + Banner Peramban Ponsel)
            function dispatchUniversalNotification(title, body, targetUrl, tag) {
                const url = targetUrl || DASHBOARD_REMINDER_URL;
                playMobileAlertFeedback();
                showInBrowserToast(title, body, url);

                if (!('Notification' in window) || Notification.permission !== 'granted') {
                    return;
                }

                const payload = {
                    title: title,
                    body: body,
                    url: url,
                    tag: tag || ('siternak-' + Date.now()),
                    icon: 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png',
                    badge: 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png'
                };

                // Prioritaskan ServiceWorkerRegistration.showNotification (Wajib untuk Peramban Ponsel Android/iOS)
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(function(reg) {
                        if (reg && typeof reg.showNotification === 'function') {
                            reg.showNotification(title, {
                                body: body,
                                icon: payload.icon,
                                badge: payload.badge,
                                vibrate: [200, 100, 200, 100, 300],
                                tag: payload.tag,
                                renotify: true,
                                data: { url: url }
                            });
                        } else if (navigator.serviceWorker.controller) {
                            navigator.serviceWorker.controller.postMessage({
                                type: 'SHOW_NOTIFICATION',
                                payload: payload
                            });
                        }
                    }).catch(function() {
                        try {
                            const n = new Notification(title, { body: body, icon: payload.icon, tag: payload.tag });
                            n.onclick = function() { window.location.href = url; };
                        } catch (err) {}
                    });
                } else {
                    try {
                        const n = new Notification(title, { body: body, icon: payload.icon, tag: payload.tag });
                        n.onclick = function() { window.location.href = url; };
                    } catch (err) {}
                }
            }

            // 5. Perbarui Tampilan Status Izin Peramban
            function updatePermissionUI() {
                const badges = document.querySelectorAll('#browser-perm-badge, [data-browser-perm-badge]');
                const requestBtns = document.querySelectorAll('#btn-request-perm, [data-btn-request-perm]');
                const hasNotifApi = ('Notification' in window);
                const perm = hasNotifApi ? Notification.permission : 'unsupported';

                badges.forEach(function(badge) {
                    if (perm === 'granted') {
                        badge.textContent = 'AKTIF (Notifikasi Sistem HP & Peramban)';
                        badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300';
                    } else if (perm === 'denied') {
                        badge.textContent = 'AKTIF (Mode Banner Peramban Ponsel)';
                        badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300';
                    } else {
                        badge.textContent = 'SIAP (Ketuk Aktifkan Izin HP)';
                        badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300';
                    }
                });

                requestBtns.forEach(function(btn) {
                    if (perm === 'granted') {
                        btn.classList.add('hidden');
                    } else {
                        btn.classList.remove('hidden');
                    }
                });
            }

            // 6. Minta Izin Notifikasi pada Peramban Ponsel / Desktop
            function requestPermission() {
                if (!('Notification' in window)) {
                    dispatchUniversalNotification(
                        'Mode Notifikasi Peramban Ponsel Aktif',
                        'Peramban Anda menggunakan notifikasi layar langsung (In-Browser Push Banner). Sesi kerja operator berlaku 8 jam.',
                        DASHBOARD_REMINDER_URL
                    );
                    updatePermissionUI();
                    return;
                }

                Notification.requestPermission().then(function(permission) {
                    updatePermissionUI();
                    if (permission === 'granted') {
                        testMobileNotification();
                    } else {
                        dispatchUniversalNotification(
                            'Notifikasi Banner Peramban Aktif',
                            'Pengingat konfirmasi sapi tetap tampil langsung di layar peramban ponsel Anda selama sesi kerja 8 jam.',
                            DASHBOARD_REMINDER_URL
                        );
                    }
                }).catch(function() {
                    testMobileNotification();
                });
            }

            // 7. Kirim Uji / Trigger Notifikasi ke HP Sekarang
            function testMobileNotification() {
                if (latestReminders.length > 0) {
                    const first = latestReminders[0];
                    const extra = latestReminders.length > 1 ? ' (+' + (latestReminders.length - 1) + ' sapi lainnya)' : '';
                    dispatchUniversalNotification(
                        '🐄 Pengingat Lapangan: ' + latestReminders.length + ' Sapi Butuh Konfirmasi',
                        first.cow_name + ' (' + first.cow_nik + ') masuk tahap ' + first.milestone_label + ' - Peternak: ' + first.farmer_name + extra,
                        first.action_url || DASHBOARD_REMINDER_URL,
                        'siternak-test-' + Date.now()
                    );
                } else {
                    dispatchUniversalNotification(
                        '✅ Notifikasi Peramban Ponsel SiTernak-OK Aktif',
                        'Peramban HP Anda siap menerima pengingat konfirmasi kebuntingan sapi selama sesi kerja 8 jam di lapangan.',
                        DASHBOARD_REMINDER_URL,
                        'siternak-test-' + Date.now()
                    );
                }
            }

            // 8. Render Daftar Pengingat di Dropdown Lonceng Header
            function renderDropdownReminders(reminders) {
                const badgeEl = document.getElementById('header-notif-badge');
                const countEl = document.getElementById('dropdown-reminder-count');
                const listEl = document.getElementById('dropdown-reminder-list');

                if (badgeEl) {
                    if (reminders.length > 0) {
                        badgeEl.textContent = reminders.length;
                        badgeEl.classList.remove('hidden');
                    } else {
                        badgeEl.classList.add('hidden');
                    }
                }

                if (countEl) {
                    countEl.textContent = reminders.length + ' Sapi';
                }

                if (!listEl) return;

                if (reminders.length === 0) {
                    listEl.innerHTML = '<div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 text-center text-[11px] text-slate-500">Belum ada sapi yang memasuki batas konfirmasi 3 hari saat ini.</div>';
                    return;
                }

                listEl.innerHTML = reminders.map(function(item) {
                    return '<a href="' + (item.action_url || DASHBOARD_REMINDER_URL) + '" class="block p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 dark:bg-slate-950 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 transition">' +
                        '<div class="flex items-center justify-between gap-2">' +
                            '<span class="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400">' + item.cow_nik + '</span>' +
                            '<span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold">' + item.milestone_label + '</span>' +
                        '</div>' +
                        '<p class="text-xs font-bold text-slate-900 dark:text-white mt-1 truncate">' + item.cow_name + ' (' + item.farmer_name + ')</p>' +
                        '<p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center justify-between">' +
                            '<span>' + item.location + '</span>' +
                            '<span class="text-emerald-600 dark:text-emerald-400 font-bold">Konfirmasi &rarr;</span>' +
                        '</p>' +
                    '</a>';
                }).join('');
            }

            // 9. Format Detik ke HH:MM:SS untuk Countdown 8 Jam Operator
            function formatDuration(totalSeconds) {
                const sec = Math.max(0, Math.floor(totalSeconds));
                const h = String(Math.floor(sec / 3600)).padStart(2, '0');
                const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                const s = String(sec % 60).padStart(2, '0');
                return h + ':' + m + ':' + s;
            }

            function tickOperatorCountdown() {
                if (!IS_OPERATOR || !expiresAtTimestamp) return;

                const nowSec = Math.floor(Date.now() / 1000);
                const remaining = Math.max(0, expiresAtTimestamp - nowSec);
                const formatted = formatDuration(remaining);

                document.querySelectorAll('[data-operator-countdown]').forEach(function(el) {
                    el.textContent = formatted;
                    if (remaining <= 900) {
                        el.classList.add('text-rose-600', 'dark:text-rose-400');
                    }
                });

                const progressEl = document.getElementById('operator-session-progress');
                if (progressEl) {
                    const pct = Math.min(100, Math.max(0, (remaining / (8 * 3600)) * 100));
                    progressEl.style.width = pct.toFixed(1) + '%';
                }

                // Peringatan 15 menit sebelum waktu login 8 jam berakhir
                if (remaining > 0 && remaining <= 900 && !warned15Min) {
                    warned15Min = true;
                    dispatchUniversalNotification(
                        '⏳ Sesi Login 8 Jam Tersisa 15 Menit',
                        'Waktu kerja lapangan Anda akan segera berakhir. Pastikan seluruh konfirmasi IB telah disimpan.',
                        DASHBOARD_REMINDER_URL,
                        'siternak-session-warn'
                    );
                }

                // Jika waktu login 8 jam habis, kirim notifikasi & arahkan ke login
                if (remaining <= 0) {
                    dispatchUniversalNotification(
                        '🔒 Batas Waktu Login 8 Jam Berakhir',
                        'Sesi kerja Operator 8 jam telah selesai. Silakan login kembali melalui peramban ponsel Anda.',
                        LOGIN_URL,
                        'siternak-session-expired'
                    );
                    setTimeout(function() {
                        window.location.href = LOGIN_URL;
                    }, 1500);
                }
            }

            // 10. Polling ke Server untuk Sinkronisasi Sesi 8 Jam & Notifikasi Pengingat Sapi
            function pollServerNotifications(forceNotify) {
                fetch(POLL_URL, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function(res) {
                    if (res.status === 401) {
                        window.location.href = LOGIN_URL;
                        return null;
                    }
                    return res.json();
                })
                .then(function(data) {
                    if (!data) return;
                    if (!data.authenticated || data.expired) {
                        window.location.href = LOGIN_URL;
                        return;
                    }

                    if (data.session && data.session.is_operator) {
                        expiresAtTimestamp = data.session.expires_at_timestamp;
                        const loginTimeEl = document.getElementById('operator-login-time');
                        const expireTimeEl = document.getElementById('operator-expire-time');
                        if (loginTimeEl) loginTimeEl.textContent = data.session.login_at_formatted;
                        if (expireTimeEl) expireTimeEl.textContent = data.session.expires_at_formatted;
                        tickOperatorCountdown();
                    }

                    latestReminders = Array.isArray(data.reminders) ? data.reminders : [];
                    renderDropdownReminders(latestReminders);

                    const lastNotifiedAt = Number(sessionStorage.getItem('siternak_last_notified_at') || 0);
                    const nowMs = Date.now();
                    const shouldAutoNotify = forceNotify || data.just_logged_in || (latestReminders.length > 0 && (nowMs - lastNotifiedAt > 30 * 60 * 1000));

                    if (shouldAutoNotify && latestReminders.length > 0) {
                        sessionStorage.setItem('siternak_last_notified_at', String(nowMs));
                        const first = latestReminders[0];
                        const extra = latestReminders.length > 1 ? ' (+' + (latestReminders.length - 1) + ' sapi lainnya)' : '';
                        dispatchUniversalNotification(
                            '🐄 SiTernak-OK: ' + latestReminders.length + ' Sapi Butuh Konfirmasi!',
                            first.cow_name + ' (' + first.cow_nik + ') memasuki jadwal ' + first.milestone_label + ' (Batas 3 Hari)' + extra,
                            first.action_url || DASHBOARD_REMINDER_URL,
                            'siternak-auto-reminder'
                        );
                    } else if (data.just_logged_in && IS_OPERATOR) {
                        dispatchUniversalNotification(
                            '💉 Sesi Lapangan 8 Jam Aktif',
                            'Notifikasi peramban ponsel siap mengirimkan pengingat konfirmasi kebuntingan sapi selama Anda bertugas di lapangan.',
                            DASHBOARD_REMINDER_URL,
                            'siternak-login-welcome'
                        );
                    }
                })
                .catch(function() {});
            }

            // 11. Sinkronisasi Cronjob secara langsung dari Modal / Tombol
            function syncCronAndNotify() {
                fetch(SYNC_CRON_URL, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && Array.isArray(data.reminders)) {
                        latestReminders = data.reminders;
                        renderDropdownReminders(latestReminders);
                    }
                    testMobileNotification();
                })
                .catch(function() {
                    testMobileNotification();
                });
            }

            window.SiTernakNotifier = {
                requestPermission: requestPermission,
                testMobileNotification: testMobileNotification,
                syncCronAndNotify: syncCronAndNotify,
                hideToast: hideToast,
                poll: pollServerNotifications
            };

            document.addEventListener('DOMContentLoaded', function() {
                updatePermissionUI();
                pollServerNotifications(false);
                if (IS_OPERATOR) {
                    setInterval(tickOperatorCountdown, 1000);
                }
                // Polling berkala setiap 60 detik selama sesi login 8 jam aktif
                setInterval(function() {
                    pollServerNotifications(false);
                }, 60000);
            });
        })();
    </script>
    @endif
</body>
</html>
