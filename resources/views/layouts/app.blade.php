<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
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
<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col font-sans antialiased overflow-x-hidden" x-data="{ mobileMenuOpen: false, adminDropdownOpen: false, userDropdownOpen: false }">

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

                <!-- Right Side: Theme Switcher & Profile User Dropdown -->
                <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
                    
                    <!-- Mode Terang / Gelap Switcher Toggle -->
                    <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" 
                        class="p-2 sm:p-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl text-xs transition border border-slate-200 dark:border-slate-700 flex items-center justify-center" 
                        title="Ganti Mode Terang / Gelap">
                        <i class="fa-solid text-sm" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-600'"></i>
                    </button>

                    @if(auth()->check() && !auth()->user()->isGuest())
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
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Login Pengguna
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
            <p>&copy; {{ date('Y') }} Dinas Peternakan dan Perkebunan Provinsi Gorontalo. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
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
