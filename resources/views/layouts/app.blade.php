<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
    <title>@yield('title', 'Dashboard') - SiTernak-OK (Dinas Peternakan)</title>

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
<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col font-sans antialiased" x-data="{ mobileMenuOpen: false, adminDropdownOpen: false, userDropdownOpen: false }">

    <!-- Top Sticky Header Navigation Bar (Posisi Di Atas untuk Semua Halaman) -->
    <header class="glass-header sticky top-0 z-50 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-2">

                <!-- Left Side: Brand Logo & Title -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-500 to-emerald-400 flex items-center justify-center text-white shadow-md shadow-emerald-600/30 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-cow text-base sm:text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white tracking-tight leading-none">
                                SiTernak<span class="text-emerald-500">-OK</span>
                            </span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-semibold tracking-wider uppercase mt-0.5">Dinas Peternakan</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Desktop Horizontal Navigation Links (No wrapping, whitespace-nowrap) -->
                <nav class="hidden xl:flex items-center space-x-1 whitespace-nowrap">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('home') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-house text-emerald-500 {{ request()->routeIs('home') ? 'text-white' : '' }}"></i>
                        <span>Utama</span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-chart-line text-emerald-500 {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('cows.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('cows.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-cow text-emerald-500 {{ request()->routeIs('cows.*') ? 'text-white' : '' }}"></i>
                        <span>Sapi IB</span>
                    </a>

                    <a href="{{ route('calves.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('calves.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-baby-carriage text-emerald-500 {{ request()->routeIs('calves.*') ? 'text-white' : '' }}"></i>
                        <span>Pedet</span>
                    </a>

                    <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('reports.index') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-file-invoice text-emerald-500 {{ request()->routeIs('reports.index') ? 'text-white' : '' }}"></i>
                        <span>Kelahiran</span>
                    </a>

                    <a href="{{ route('reports.inseminators') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('reports.inseminators') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-trophy text-amber-500 {{ request()->routeIs('reports.inseminators') ? 'text-white' : '' }}"></i>
                        <span>Inseminator</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <a href="{{ route('straws.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('straws.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-vial text-purple-500 {{ request()->routeIs('straws.*') ? 'text-white' : '' }}"></i>
                        <span>Straw Pejantan</span>
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <!-- Dropdown Menu Administrasi untuk Admin -->
                    <div class="relative" @click.away="adminDropdownOpen = false">
                        <button @click="adminDropdownOpen = !adminDropdownOpen" type="button" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('farmers.*') || request()->routeIs('regions.*') || request()->routeIs('users.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}">
                            <i class="fa-solid fa-user-shield text-emerald-500"></i>
                            <span>Administrasi</span>
                            <i class="fa-solid fa-chevron-down text-[10px] ml-0.5"></i>
                        </button>

                        <div x-show="adminDropdownOpen" x-cloak x-transition class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl py-2 z-50">
                            <a href="{{ route('farmers.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-users text-emerald-500 w-4 text-center"></i> Data Peternak
                            </a>
                            <a href="{{ route('regions.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-map-location-dot text-emerald-500 w-4 text-center"></i> Kabupaten & Wilayah
                            </a>
                            <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-user-gear text-emerald-500 w-4 text-center"></i> Kelola Role User
                            </a>
                        </div>
                    </div>
                    @endif
                </nav>

                <!-- Right Side: Theme Switcher & Profile User Dropdown -->
                <div class="flex items-center space-x-2 flex-shrink-0">
                    
                    <!-- Mode Terang / Gelap Switcher Toggle -->
                    <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" 
                        class="p-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl text-xs transition border border-slate-200 dark:border-slate-700 flex items-center justify-center" 
                        title="Ganti Mode Terang / Gelap">
                        <i class="fa-solid text-sm" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-600'"></i>
                    </button>

                    @if(auth()->check() && !auth()->user()->isGuest())
                    <!-- User Profile Dropdown Button -->
                    <div class="relative" @click.away="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" type="button" class="flex items-center space-x-2 p-1.5 pr-3 bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 transition">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-bold text-xs flex items-center justify-center shadow">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <span class="text-[9px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                    @if(auth()->user()->isAdminProvinsi())
                                        Admin Prov
                                    @elseif(auth()->user()->isAdminKabupaten())
                                        Admin Kab
                                    @else
                                        Operator
                                    @endif
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                        </button>

                        <div x-show="userDropdownOpen" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 space-y-1">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
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
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                    @endif

                    <!-- Mobile Hamburger Toggle Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="xl:hidden p-2.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-sm border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>

                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu (Menu Seluler jika Layar Kecil) -->
        <div x-cloak x-show="mobileMenuOpen" class="xl:hidden fixed inset-0 z-50">
            <div x-show="mobileMenuOpen" x-transition.opacity class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

            <aside x-show="mobileMenuOpen" x-transition:enter="transform transition duration-300" x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transform transition duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full" class="absolute top-0 left-0 right-0 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col p-5 space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-bold">
                            <i class="fa-solid fa-cow"></i>
                        </div>
                        <span class="font-extrabold text-slate-900 dark:text-white text-base">SiTernak-OK</span>
                    </div>
                    <button @click="mobileMenuOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-house text-emerald-500"></i> Utama
                    </a>
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-chart-line text-emerald-500"></i> Dashboard
                    </a>
                    <a href="{{ route('cows.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-cow text-emerald-500"></i> Data Sapi IB
                    </a>
                    <a href="{{ route('calves.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-baby-carriage text-emerald-500"></i> Pedet
                    </a>
                    <a href="{{ route('reports.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-file-invoice text-emerald-500"></i> Kelahiran
                    </a>
                    <a href="{{ route('reports.inseminators') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-trophy text-amber-500"></i> Inseminator
                    </a>
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <a href="{{ route('straws.index') }}" @click="mobileMenuOpen = false" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200 col-span-2">
                        <i class="fa-solid fa-vial text-purple-500"></i> Kode Straw Pejantan
                    </a>
                    @endif
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Administrasi Master</p>
                    <div class="grid grid-cols-1 gap-2 text-xs font-bold">
                        <a href="{{ route('farmers.index') }}" @click="mobileMenuOpen = false" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-users text-emerald-500"></i> Data Peternak
                        </a>
                        <a href="{{ route('regions.index') }}" @click="mobileMenuOpen = false" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-map-location-dot text-emerald-500"></i> Kabupaten & Wilayah
                        </a>
                        <a href="{{ route('users.index') }}" @click="mobileMenuOpen = false" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center gap-2 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-user-gear text-emerald-500"></i> Kelola Role User
                        </a>
                    </div>
                </div>
                @endif

                <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                    @if(auth()->check() && !auth()->user()->isGuest())
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-rose-600 text-white font-bold text-xs rounded-xl shadow">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i> Keluar / Sign Out
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="block text-center py-3 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Login Pengguna
                    </a>
                    @endif
                </div>
            </aside>
        </div>
    </header>

    <!-- Sub-Header Status Bar & Actions (Top Bar di bawah Navigation Header) -->
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800/80 py-3 px-4 sm:px-6 lg:px-8 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    @yield('title', 'Pemantauan Kehamilan Sapi')
                </h2>
            </div>

            <div class="flex items-center space-x-2">
                @if(auth()->check() && auth()->user()->isOperator())
                <form action="{{ route('pregnancy.trigger-cron') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-emerald-700 dark:text-emerald-400 rounded-xl text-xs font-extrabold border border-slate-200 dark:border-slate-700 transition" title="Jalankan otomatisasi simulasi 3 hari secara manual">
                        <i class="fa-solid fa-rotate text-emerald-500"></i>
                        <span>Check Auto-Advance (3 Hari)</span>
                    </button>
                </form>
                @endif

                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                <a href="{{ route('cows.create') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow transition whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Sapi Baru</span>
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
                Login Pengguna
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
            <p>&copy; {{ date('Y') }} Dinas Peternakan. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    System Active
                </span>
            </div>
        </div>
    </footer>

    <!-- Flatpickr Date Picker Script -->
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
</body>
</html>
