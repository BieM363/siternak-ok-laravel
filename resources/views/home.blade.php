<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
    <title>Selamat Datang di SiTernak-OK - Dinas Peternakan dan Perkebunan Provinsi Gorontalo</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    <style>
        [x-cloak] { display: none !important; }
        
        /* Light Mode Hero & Cards (Default) */
        .glass-hero {
            background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.12), transparent 50%),
                        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.12), transparent 50%),
                        rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(16, 185, 129, 0.5);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(16, 185, 129, 0.1);
        }

        /* Dark Mode Hero & Cards */
        .dark .glass-hero {
            background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.15), transparent 40%),
                        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.15), transparent 40%),
                        rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        .dark .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(16, 185, 129, 0.1);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: #1e293b; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 flex flex-col justify-between" x-data="{ mobileNav: false }">

<!-- ========================================================= -->
<!-- TOP NAVIGATION HEADER -->
<!-- ========================================================= -->
<header
    class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 px-4 sm:px-6 py-3.5 shadow-sm"
>
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">

        <!-- ================================================= -->
        <!-- LOGO -->
        <!-- ================================================= -->
        <a
            href="{{ route('home') }}"
            class="flex items-center space-x-2.5 sm:space-x-3 shrink-0"
        >
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-bold shadow-md">
                <i class="fa-solid fa-cow text-lg sm:text-xl"></i>
            </div>

            <div>
                <span class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white tracking-tight">
                    SiTernak<span class="text-emerald-500">-OK</span>
                </span>

                <p class="text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                    Dinas Peternakan & Perkebunan Prov. Gorontalo
                </p>
            </div>
        </a>


        <!-- ================================================= -->
        <!-- DESKTOP NAVIGATION -->
        <!-- ================================================= -->
        <nav class="hidden lg:flex flex-1 justify-center items-center gap-1 whitespace-nowrap min-w-0">

            <!-- Utama -->
            <a
                href="{{ route('home') }}"
                class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5
                {{ request()->routeIs('home')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-house {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-500' }}"></i>
                <span>Utama</span>
            </a>
           


            <!-- Sapi IB -->
            <a
                href="{{ route('cows.index') }}"
                class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5
                {{ request()->routeIs('cows.*')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-cow {{ request()->routeIs('cows.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                <span>Sapi IB</span>
            </a>


            <!-- Pedet -->
            <a
                href="{{ route('calves.index') }}"
                class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5
                {{ request()->routeIs('calves.*')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-baby-carriage {{ request()->routeIs('calves.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                <span>Pedet</span>
            </a>


            <!-- Kelahiran -->
            <a
                href="{{ route('reports.index') }}"
                class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5
                {{ request()->routeIs('reports.index')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-file-invoice {{ request()->routeIs('reports.index') ? 'text-white' : 'text-emerald-500' }}"></i>
                <span>Kelahiran</span>
            </a>


            <!-- Inseminator -->
            <a
                href="{{ route('reports.inseminators') }}"
                class="px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5
                {{ request()->routeIs('reports.inseminators')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-trophy {{ request()->routeIs('reports.inseminators') ? 'text-white' : 'text-amber-500' }}"></i>
                <span>Inseminator</span>
            </a>

        </nav>


        <!-- ================================================= -->
        <!-- RIGHT SIDE DESKTOP -->
        <!-- ================================================= -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">

            <!-- Theme Switcher -->
            <button
                @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)"
                type="button"
                class="inline-flex items-center space-x-1.5 px-2.5 sm:px-3 py-2
                       bg-slate-100 dark:bg-slate-800
                       hover:bg-slate-200 dark:hover:bg-slate-700
                       text-slate-800 dark:text-slate-200
                       rounded-xl text-xs font-semibold
                       border border-slate-200 dark:border-slate-700
                       transition-all"
            >
                <i
                    class="fa-solid"
                    :class="theme === 'dark'
                        ? 'fa-sun text-amber-400'
                        : 'fa-moon text-indigo-600'"
                ></i>

                <span
                    class="hidden xl:inline"
                    x-text="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
                ></span>
            </button>


            <!-- ================================================= -->
            <!-- DESKTOP USER -->
            <!-- ================================================= -->
            @auth

                @if(!auth()->user()->isGuest())

                    <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-xl
                                bg-slate-100 dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                text-xs font-bold">

                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>

                        <span class="truncate max-w-[140px] text-slate-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </span>

                        <span class="px-1.5 py-0.5 rounded
                                     bg-emerald-500/20
                                     text-emerald-700 dark:text-emerald-300
                                     text-[10px] font-black uppercase">
                            {{ auth()->user()->getRoleBadgeText() }}
                        </span>
                    </div>


                    <!-- Logout -->
                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="hidden lg:inline"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="p-2
                                   bg-rose-50 hover:bg-rose-100
                                   dark:bg-rose-950/40 dark:hover:bg-rose-900/60
                                   text-rose-600 dark:text-rose-400
                                   rounded-xl text-xs font-bold
                                   border border-rose-200 dark:border-rose-800
                                   transition"
                            title="Keluar"
                        >
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="hidden sm:flex px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition items-center gap-1.5"
                    >
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login Petugas</span>
                    </a>

                @endif
            @else
                <a
                    href="{{ route('login') }}"
                    class="flex px-3 sm:flex px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition items-center gap-1.5">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>

            @endauth

            <!-- ================================================= -->
            <!-- MOBILE HAMBURGER -->
            <!-- ================================================= -->
            <button
                @click="mobileNav = !mobileNav"
                type="button"
                class="lg:hidden w-10 h-10 flex items-center justify-center
                       bg-slate-100 dark:bg-slate-800
                       hover:bg-emerald-100 dark:hover:bg-emerald-950
                       text-slate-700 dark:text-slate-200
                       hover:text-emerald-600 dark:hover:text-emerald-400
                       rounded-xl
                       border border-slate-200 dark:border-slate-700
                       transition-all"
                aria-label="Buka menu navigasi"
            >
                <i
                    class="fa-solid text-lg"
                    :class="mobileNav ? 'fa-xmark' : 'fa-bars'"
                ></i>
            </button>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- MOBILE NAVIGATION -->
    <!-- ========================================================= -->
    <div
        x-cloak
        x-show="mobileNav"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-slate-200 dark:border-slate-800 mt-3 pt-3 pb-1"
    >

        <nav class="max-w-7xl mx-auto space-y-1">

            <!-- Utama -->
            <a
                href="{{ route('home') }}"
                @click="mobileNav = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-extrabold transition-all
                {{ request()->routeIs('home')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-house w-5 text-center
                    {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-500' }}">
                </i>

                <span>Utama</span>
            </a>





            <!-- Sapi IB -->
            <a
                href="{{ route('cows.index') }}"
                @click="mobileNav = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-extrabold transition-all
                {{ request()->routeIs('cows.*')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-cow w-5 text-center
                    {{ request()->routeIs('cows.*') ? 'text-white' : 'text-emerald-500' }}">
                </i>

                <span>Sapi IB</span>
            </a>


            <!-- Pedet -->
            <a
                href="{{ route('calves.index') }}"
                @click="mobileNav = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-extrabold transition-all
                {{ request()->routeIs('calves.*')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-baby-carriage w-5 text-center
                    {{ request()->routeIs('calves.*') ? 'text-white' : 'text-emerald-500' }}">
                </i>

                <span>Pedet</span>
            </a>


            <!-- Kelahiran -->
            <a
                href="{{ route('reports.index') }}"
                @click="mobileNav = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-extrabold transition-all
                {{ request()->routeIs('reports.index')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-file-invoice w-5 text-center
                    {{ request()->routeIs('reports.index') ? 'text-white' : 'text-emerald-500' }}">
                </i>

                <span>Kelahiran</span>
            </a>


            <!-- Inseminator -->
            <a
                href="{{ route('reports.inseminators') }}"
                @click="mobileNav = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-extrabold transition-all
                {{ request()->routeIs('reports.inseminators')
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-trophy w-5 text-center
                    {{ request()->routeIs('reports.inseminators') ? 'text-white' : 'text-amber-500' }}">
                </i>

                <span>Inseminator</span>
            </a>


            <!-- ================================================= -->
            <!-- MOBILE USER INFO -->
            <!-- ================================================= -->
            @auth

                @if(!auth()->user()->isGuest())

                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800">

                        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-800">

                            <div class="flex items-center gap-2 min-w-0">

                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>

                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ auth()->user()->name }}
                                    </p>

                                    <p class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400">
                                        {{ auth()->user()->getRoleBadgeText() }}
                                    </p>
                                </div>

                            </div>

                            <!-- Mobile Logout -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf

                                <button
                                    type="submit"
                                    class="w-9 h-9 flex items-center justify-center
                                           bg-rose-50 hover:bg-rose-100
                                           dark:bg-rose-950/40 dark:hover:bg-rose-900/60
                                           text-rose-600 dark:text-rose-400
                                           rounded-xl
                                           border border-rose-200 dark:border-rose-800"
                                    title="Keluar"
                                >
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>

                        </div>

                    </div>

                @endif

            @endauth

        </nav>

    </div>

</header>
    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 sm:space-y-10 flex-1 w-full">

        <!-- ========================================================= -->
        <!-- DYNAMIC ROLE-ADAPTIVE HERO SECTION -->
        <!-- ========================================================= -->
        @if(auth()->check())

            @if(auth()->user()->isAdminProvinsi())
            <!-- HERO: ADMIN PROVINSI GORONTALO -->
            <div class="glass-card rounded-3xl p-6 sm:p-10 border-2 border-purple-500/30 bg-gradient-to-br from-purple-950/90 via-slate-900 to-indigo-950/90 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-purple-400">
                    <i class="fa-solid fa-crown text-[260px]"></i>
                </div>
                <div class="max-w-3xl space-y-4 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-500/20 text-purple-300 text-xs font-black uppercase border border-purple-500/40">
                        <i class="fa-solid fa-crown text-amber-400"></i>
                        <span>Super Admin &bull; Administrator Utama Dinas Peternakan & Perkebunan Provinsi Gorontalo</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-purple-300">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-purple-200/90 leading-relaxed font-normal">
                        Sistem pusat pengawasan populasi ternak, rekam pelayanan Inseminasi Buatan (IB), dan monitoring siklus kebuntingan terintegrasi pada seluruh <strong>6 Kabupaten & Kota</strong> di Provinsi Gorontalo (Boalemo, Bone Bolango, Kab. Gorontalo, Gorontalo Utara, Pohuwato, dan Kota Gorontalo).
                    </p>

                    <!-- Quick Stats Badges Provinsi -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold">
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-purple-500/30 shadow">
                            <i class="fa-solid fa-cow text-emerald-400"></i>
                            <span>{{ number_format($totalCows) }} Sapi se-Provinsi</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-purple-500/30 shadow">
                            <i class="fa-solid fa-syringe text-blue-400"></i>
                            <span>{{ number_format($totalIb) }} Tindakan IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-purple-500/30 shadow">
                            <i class="fa-solid fa-baby-carriage text-purple-400"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-purple-500/30 shadow">
                            <i class="fa-solid fa-user-tie text-amber-400"></i>
                            <span>{{ number_format($totalInseminators) }} Inseminator</span>
                        </div>
                    </div>
                </div>
            </div>

            @elseif(auth()->user()->isAdminKota())
            <!-- HERO: ADMIN KOTA GORONTALO -->
            <div class="glass-card rounded-3xl p-6 sm:p-10 border-2 border-teal-500/30 bg-gradient-to-br from-teal-950/90 via-slate-900 to-cyan-950/90 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-teal-400">
                    <i class="fa-solid fa-city text-[260px]"></i>
                </div>
                <div class="max-w-3xl space-y-4 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-teal-500/20 text-teal-300 text-xs font-black uppercase border border-teal-500/40">
                        <i class="fa-solid fa-city text-teal-400"></i>
                        <span>Administrator Wilayah Kota Gorontalo (Urban Livestock)</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-teal-300">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-teal-200/90 leading-relaxed font-normal">
                        Sistem manajemen dan pemantauan peternakan wilayah Kota Gorontalo. Anda memiliki hak kelola atas <strong>9 Kecamatan & 50 Kelurahan</strong> aktif dengan terminologi Kelurahan dan peternak urban.
                    </p>

                    <!-- Quick Stats Badges Kota -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold">
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-teal-500/30 shadow">
                            <i class="fa-solid fa-cow text-teal-400"></i>
                            <span>{{ number_format($totalCows) }} Sapi Kota</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-teal-500/30 shadow">
                            <i class="fa-solid fa-syringe text-cyan-400"></i>
                            <span>{{ number_format($totalIb) }} Rekam IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-teal-500/30 shadow">
                            <i class="fa-solid fa-baby-carriage text-purple-400"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-teal-500/30 shadow">
                            <i class="fa-solid fa-map-location-dot text-amber-400"></i>
                            <span>50 Kelurahan Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            @elseif(auth()->user()->isAdminKabupaten())
            <!-- HERO: ADMIN KABUPATEN -->
            <div class="glass-card rounded-3xl p-6 sm:p-10 border-2 border-blue-500/30 bg-gradient-to-br from-blue-950/90 via-slate-900 to-indigo-950/90 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-blue-400">
                    <i class="fa-solid fa-shield-halved text-[260px]"></i>
                </div>
                <div class="max-w-3xl space-y-4 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase border border-blue-500/40">
                        <i class="fa-solid fa-shield-halved text-blue-400"></i>
                        <span>Administrator Wilayah {{ auth()->user()->regency->name ?? 'Kabupaten' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-blue-300">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-blue-200/90 leading-relaxed font-normal">
                        Panel pengelolaan populasi peternakan Dinas Peternakan & Keswan <strong>{{ auth()->user()->regency->name ?? 'Kabupaten' }}</strong>. Anda mengelola data peternak, inseminator lapangan, dan struktur wilayah desa/kecamatan setempat.
                    </p>

                    <!-- Quick Stats Badges Kab -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold">
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-blue-500/30 shadow">
                            <i class="fa-solid fa-cow text-blue-400"></i>
                            <span>{{ number_format($totalCows) }} Sapi {{ auth()->user()->regency->name ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-blue-500/30 shadow">
                            <i class="fa-solid fa-syringe text-emerald-400"></i>
                            <span>{{ number_format($totalIb) }} Pelayanan IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-blue-500/30 shadow">
                            <i class="fa-solid fa-baby-carriage text-purple-400"></i>
                            <span>{{ number_format($totalCalves) }} Anak Pedet</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-blue-500/30 shadow">
                            <i class="fa-solid fa-users text-amber-400"></i>
                            <span>{{ number_format($totalFarmers) }} Peternak Terdata</span>
                        </div>
                    </div>
                </div>
            </div>

            @elseif(auth()->user()->isOperator())
            <!-- HERO: OPERATOR / INSEMINATOR LAPANGAN -->
            <div class="glass-card rounded-3xl p-6 sm:p-10 border-2 border-emerald-500/40 bg-gradient-to-br from-emerald-950/90 via-slate-900 to-teal-950/90 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-emerald-400">
                    <i class="fa-solid fa-syringe text-[260px]"></i>
                </div>
                <div class="max-w-3xl space-y-4 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase border border-emerald-500/40">
                        <i class="fa-solid fa-syringe text-emerald-400"></i>
                        <span>Petugas Inseminator IB Lapangan Resmi &bull; {{ auth()->user()->regency->name ?? 'Gorontalo' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Selamat Bertugas di Lapangan, <span class="text-emerald-300">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-emerald-200/90 leading-relaxed font-normal">
                        Pusat operasional pencatatan tindakan suntik Inseminasi Buatan (IB), pengecekan database kode straw pejantan, dan konfirmasi jadwal milestone kebuntingan sapi betina di wilayah tugas Anda.
                    </p>

                    <!-- Quick Stats Badges Operator -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold">
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-emerald-500/30 shadow">
                            <i class="fa-solid fa-syringe text-emerald-400"></i>
                            <span>{{ number_format($totalIb) }} Pelayanan IB Saya</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-emerald-500/30 shadow">
                            <i class="fa-solid fa-cow text-blue-400"></i>
                            <span>{{ number_format($totalCows) }} Sapi Binaan</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-900/90 px-3.5 py-2 rounded-xl border border-emerald-500/30 shadow">
                            <i class="fa-solid fa-baby-carriage text-purple-400"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        @else
            <!-- HERO: TAMU / AKSES PUBLIK (BELUM LOGIN) -->
            <div class="glass-hero rounded-3xl p-6 sm:p-10 text-center sm:text-left shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 dark:opacity-10 pointer-events-none text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-cow text-[240px]"></i>
                </div>

                <div class="max-w-3xl space-y-4 relative z-10">
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40 rounded-full text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Akses Publik &bull; Pemantauan Kehamilan Sapi
                    </span>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Halo, Selamat Datang di Web <span class="bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">SiTernak-OK</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                        Sistem Pemantauan Kehamilan Sapi Dinas Peternakan dan Perkebunan Provinsi Gorontalo terpadu untuk memantau siklus kebuntingan (Pasca IB, PKB 90 Hari, 245 Hari Siap Melahirkan, hingga HPL Melahirkan), rekapitulasi pedet, dan transparansi data peternakan Gorontalo.
                    </p>

                    <!-- Quick Stats Badges Publik -->
                    <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-bold text-slate-700 dark:text-slate-300">
                        <div class="flex items-center gap-2 bg-white dark:bg-slate-900/80 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            <span>{{ number_format($totalCows) }} Sapi Betina</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white dark:bg-slate-900/80 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <i class="fa-solid fa-syringe text-blue-600 dark:text-blue-400 text-sm"></i>
                            <span>{{ number_format($totalIb) }} Rekam Inseminasi (IB)</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white dark:bg-slate-900/80 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <i class="fa-solid fa-baby-carriage text-purple-600 dark:text-purple-400 text-sm"></i>
                            <span>{{ number_format($totalCalves) }} Anak Sapi (Pedet)</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================================= -->
        <!-- ROLE-SPECIFIC ACTION CARDS SECTION -->
        <!-- ========================================================= -->

        @if(auth()->check() && auth()->user()->isAdminProvinsi())
        <!-- ========================================================= -->
        <!-- CARDS: ADMIN PROVINSI GORONTALO -->
        <!-- ========================================================= -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-crown text-purple-600 dark:text-purple-400"></i>
                Pusat Kontrol & Administrasi Provinsi Gorontalo
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Akses langsung ke data master peternak, hirarki wilayah 6 Kab/Kota, dan pengawasan sistem se-Provinsi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Card 1: Data Peternak se-Gorontalo -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-purple-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Data Peternak se-Gorontalo</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Kelola data peternak lengkap dengan validasi 16-digit NIK KTP, kontak telepon, dan distribusi lokasi di 6 Kabupaten/Kota.
                    </p>
                </div>
                <a href="{{ route('farmers.index') }}" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka Data Peternak</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 2: Wilayah Administratif Gorontalo -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-indigo-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl font-bold">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Wilayah Administratif Gorontalo</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Struktur master 6 Kabupaten/Kota, 77 Kecamatan, serta 722 Desa dan Kelurahan lengkap se-Provinsi Gorontalo.
                    </p>
                </div>
                <a href="{{ route('regions.index') }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Kelola Wilayah Gorontalo</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Kelola Pengguna & Operator -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-emerald-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-bold">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Kelola Pengguna & Role</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Atur akun Admin Kota/Kabupaten dan Petugas Inseminator Lapangan, reset password, dan pantau aktivitas akun.
                    </p>
                </div>
                <a href="{{ route('users.index') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Kelola User & Role</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Database Straw Pejantan -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-purple-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                        <i class="fa-solid fa-vial"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Katalog bibit straw pejantan unggul (Limosin, Simental, Brahman, Angus, dll) dari BIB Lembang & Singosari.
                    </p>
                </div>
                <a href="{{ route('straws.index') }}" class="w-full py-2.5 px-4 bg-purple-700 hover:bg-purple-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka Database Straw</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Laporan Kelahiran se-Provinsi -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-amber-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Rekapitulasi Laporan Kelahiran</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Laporan kelahiran pedet bulanan, filter per-kabupaten/kota, dan ekspor resmi ke format cetak PDF & Excel.
                    </p>
                </div>
                <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Rekap & Ekspor Laporan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        @elseif(auth()->check() && auth()->user()->isAdminKota())
        <!-- ========================================================= -->
        <!-- CARDS: ADMIN KOTA GORONTALO -->
        <!-- ========================================================= -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-city text-teal-600 dark:text-teal-400"></i>
                Pusat Kontrol Wilayah Kota Gorontalo
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Menu operasional tata kelola peternakan perkotaan (9 Kecamatan & 50 Kelurahan).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Card 1: 50 Kelurahan Kota -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-teal-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-600 dark:text-teal-400 text-xl font-bold">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Kelola 50 Kelurahan Kota</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Data administratif 9 Kecamatan dan 50 Kelurahan resmi di lingkungan Pemerintah Kota Gorontalo.
                    </p>
                </div>
                <a href="{{ route('regions.index') }}" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka 50 Kelurahan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 2: Peternak Kota Gorontalo -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-emerald-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-bold">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Data Peternak Kota Gorontalo</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Daftar peternak sapi per-Kelurahan di Kota Gorontalo, lengkap dengan NIK KTP dan populasi kepemilikan.
                    </p>
                </div>
                <a href="{{ route('farmers.index') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Peternak Kota</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Operator Wilayah Kota -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-cyan-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/80 border border-cyan-200 dark:border-cyan-800 flex items-center justify-center text-cyan-600 dark:text-cyan-400 text-xl font-bold">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Operator & Inseminator Kota</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Kelola akun petugas inseminator lapangan khusus wilayah Kota Gorontalo.
                    </p>
                </div>
                <a href="{{ route('users.index') }}" class="w-full py-2.5 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>User Kota</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Database Straw -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-purple-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                        <i class="fa-solid fa-vial"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Informasi kode semen beku pejantan unggul untuk layanan Inseminasi Buatan.
                    </p>
                </div>
                <a href="{{ route('straws.index') }}" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Cek Straw Pejantan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 5: Dashboard Sapi Kota -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-teal-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-600 dark:text-teal-400 text-xl font-bold">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Dashboard Sapi Kota Gorontalo</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Monitoring siklus kehamilan sapi betina di 9 Kecamatan Kota Gorontalo.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Dashboard Kota</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Laporan Kelahiran Kota -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-amber-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Laporan Kelahiran Pedet Kota</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rekapitulasi kelahiran pedet perkotaan dengan fitur ekspor PDF dan Excel.
                    </p>
                </div>
                <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Laporan Kota</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        @elseif(auth()->check() && auth()->user()->isAdminKabupaten())
        <!-- ========================================================= -->
        <!-- CARDS: ADMIN KABUPATEN -->
        <!-- ========================================================= -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-shield-halved text-blue-600 dark:text-blue-400"></i>
                Pusat Kontrol Dinas Peternakan & Keswan {{ auth()->user()->regency->name ?? 'Kabupaten' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Manajemen data peternakan, wilayah desa & kecamatan, serta pengawasan inseminator lokal.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Card 1: Desa & Kecamatan -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-blue-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Desa & Kecamatan {{ auth()->user()->regency->name ?? '' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Data administratif seluruh desa dan kecamatan di wilayah {{ auth()->user()->regency->name ?? 'kabupaten' }}.
                    </p>
                </div>
                <a href="{{ route('regions.index') }}" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Kelola Desa & Kecamatan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 2: Peternak Lokal -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-emerald-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-bold">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Peternak {{ auth()->user()->regency->name ?? 'Lokal' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Kelola data peternak lokal, verifikasi NIK KTP peternak, dan distribusi sapi indukan.
                    </p>
                </div>
                <a href="{{ route('farmers.index') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Data Peternak Lokal</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Operator Lokal -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-indigo-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl font-bold">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Operator & Inseminator Lokal</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Kelola data petugas IB yang bertugas di cakupan wilayah {{ auth()->user()->regency->name ?? '' }}.
                    </p>
                </div>
                <a href="{{ route('users.index') }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Kelola Operator Lokal</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Database Straw -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-purple-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                        <i class="fa-solid fa-vial"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Katalog semen beku pejantan unggul untuk peningkatan mutu genetik sapi lokal.
                    </p>
                </div>
                <a href="{{ route('straws.index') }}" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Cek Straw Pejantan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 5: Dashboard Sapi Lokal -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-blue-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Dashboard Sapi {{ auth()->user()->regency->name ?? '' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Monitoring siklus kebuntingan dan konfirmasi milestone sapi di {{ auth()->user()->regency->name ?? '' }}.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Dashboard {{ auth()->user()->regency->name ?? 'Lokal' }}</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Laporan Kelahiran Lokal -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-amber-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Laporan Kelahiran Pedet</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rekapitulasi kelahiran anak sapi lokal dan ekspor laporan PDF/Excel.
                    </p>
                </div>
                <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Laporan Kelahiran</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        @elseif(auth()->check() && auth()->user()->isOperator())
        <!-- ========================================================= -->
        <!-- CARDS: OPERATOR / INSEMINATOR LAPANGAN -->
        <!-- ========================================================= -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-syringe text-emerald-600 dark:text-emerald-400"></i>
                Pusat Kerja Operasional Inseminator Lapangan
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pencatatan rekam suntik IB baru, cek straw pejantan, dan reminder konfirmasi kebuntingan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Card 1: Pendaftaran IB Baru (SPECIAL GLOWING HERO CARD!) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-2 border-emerald-500 bg-gradient-to-b from-emerald-50/50 to-white dark:from-emerald-950/40 dark:to-slate-900 shadow-lg">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold shadow-md shadow-emerald-600/30">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300 font-black text-[10px] uppercase tracking-wider rounded-lg border border-emerald-300 dark:border-emerald-700 animate-pulse">
                            Aksi Utama
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Pendaftaran IB & Sapi Baru</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Rekam tindakan suntik Inseminasi Buatan (IB) baru pada sapi indukan peternak dengan milestone otomatis.
                    </p>
                </div>
                <a href="{{ route('cows.create') }}" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-2 group">
                    <i class="fa-solid fa-plus"></i>
                    <span>Input Pendaftaran IB Baru</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 2: Database Straw Pejantan -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-purple-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                        <i class="fa-solid fa-vial"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Cari kode straw, jenis ras pejantan, dan produsen semen beku resmi sebelum melakukan inseminasi.
                    </p>
                </div>
                <a href="{{ route('straws.index') }}" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Lihat Straw Pejantan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Reminder Konfirmasi Kebuntingan -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-amber-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Reminder Konfirmasi Kebuntingan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Konfirmasi status kebuntingan (YA/TIDAK) untuk sapi binaan yang memasuki batas 3 hari masa milestone.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka Reminder Dashboard</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Leaderboard Inseminator -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-teal-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-600 dark:text-teal-400 text-xl font-bold">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Leaderboard & Skor IB</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Cek peringkat performansi pelayanan IB, rasio Conception Rate (CR %), dan Calving Rate (CvR %).
                    </p>
                </div>
                <a href="{{ route('reports.inseminators') }}" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Lihat Leaderboard</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 5: Direktori Sapi Betina -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-blue-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Direktori Sapi Betina IB</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Lihat riwayat lengkap sapi binaan, catatan siklus kebuntingan masa lalu, dan silsilah indukan.
                    </p>
                </div>
                <a href="{{ route('cows.index') }}" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka Direktori Sapi</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Rekapan Kelahiran Pedet -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 border-pink-500/20">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-pink-50 dark:bg-pink-950/80 border border-pink-200 dark:border-pink-800 flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl font-bold">
                        <i class="fa-solid fa-baby-carriage"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Data Anak Sapi (Pedet)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Lihat daftar anak sapi yang berhasil lahir dari hasil pelayanan inseminasi buatan di lapangan.
                    </p>
                </div>
                <a href="{{ route('calves.index') }}" class="w-full py-2.5 px-4 bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 group">
                    <span>Buka Data Pedet</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        @else
        <!-- ========================================================= -->
        <!-- CARDS: TAMU / PENGUNJUNG PUBLIK (BELUM LOGIN) -->
        <!-- ========================================================= -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-layer-group text-emerald-600 dark:text-emerald-400"></i>
                Akses Langsung Fitur Utama Sistem
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pilih salah satu menu di bawah untuk melihat data secara langsung (Mode Read-Only) atau masuk ke akun pengelola.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Card 1: Dashboard & Data Sapi (Read-Only) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-700/60 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                            Read-Only
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">1. Dashboard & Data Sapi</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Lihat statistik populasi ternak real-time, grafik siklus kebuntingan 30/90/245 hari, serta direktori sapi betina produktif milik peternak.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Lihat Dashboard (Read-Only)</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 2: Rekapan Kelahiran Bulanan & Filter (Read-Only) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-700/60 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl font-bold">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                            Read-Only
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">2. Rekapan Kelahiran Bulanan</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rekapitulasi kelahiran bulanan pedet, filter jenis kelamin (Jantan / Betina), serta ekspor laporan resmi ke format PDF dan Excel.
                    </p>
                </div>

                <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 text-purple-700 dark:text-purple-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Lihat Rekapan Kelahiran</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Konfirmasi Status Sapi (Read-Only) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-700/60 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                            Read-Only
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">3. Konfirmasi Status & Siklus</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pantau alur konfirmasi kebuntingan (YA/TIDAK), pengingat milestone 3 Hari Max, fase melahirkan, dan pencatatan anak sapi.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-600 text-amber-700 dark:text-amber-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-amber-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Pantau Status Kebuntingan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Leaderboard Inseminator (Read-Only) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-700/60 flex items-center justify-center text-teal-600 dark:text-teal-400 text-xl font-bold">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                            Read-Only
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">4. Leaderboard & Peringkat IB</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rangking inseminator dengan data pelayanan IB terbanyak, nilai Conception Rate (CR %) dan Calving Rate (CvR %) per petugas.
                    </p>
                </div>

                <a href="{{ route('reports.inseminators') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-teal-600 hover:text-white dark:hover:bg-teal-600 text-teal-700 dark:text-teal-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-teal-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Lihat Leaderboard Inseminator</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 5: Data Anak Sapi / Pedet (Read-Only) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-pink-50 dark:bg-pink-950/80 border border-pink-200 dark:border-pink-700/60 flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl font-bold">
                            <i class="fa-solid fa-baby-carriage"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                            Read-Only
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">5. Data Anak Sapi (Pedet)</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Direktori khusus anak sapi (pedet), filter ras & rentang tanggal lahir, silsilah induk, serta statistik kelahiran.
                    </p>
                </div>

                <a href="{{ route('calves.index') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-pink-600 hover:text-white dark:hover:bg-pink-600 text-pink-700 dark:text-pink-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-pink-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Lihat Data Anak Sapi</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Login Pengguna Pengelola -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5 border-emerald-500/40 bg-gradient-to-b from-white to-emerald-50/50 dark:from-slate-900 dark:to-emerald-950/30">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-600/80 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-bold shadow-md">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-600 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">
                            Akses Pengelola
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">6. Login Petugas & Admin</h3>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Login khusus untuk Operator Inseminator, Admin Kabupaten/Kota, dan Admin Provinsi untuk menginput dan mengelola data peternakan.
                    </p>
                </div>

                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 group">
                    <span>Masuk ke Akun Petugas / Admin</span>
                    <i class="fa-solid fa-right-to-bracket group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

        </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="px-4 sm:px-6 py-6 border-t border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-950 text-slate-500 text-xs text-center sm:text-left">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} Dinas Peternakan dan Perkebunan Provinsi Gorontalo. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>
    </footer>

</body>
</html>
