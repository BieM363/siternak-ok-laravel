<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Selamat Datang di SiTernak-OK - Dinas Peternakan dan Perkebunan Provinsi Gorontalo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Public Sans"', 'sans-serif'],
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

        ::selection {
            background-color: rgba(16, 185, 129, 0.22);
            color: inherit;
        }
        .font-mono, .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        a[href],
        button:not(:disabled),
        [role="button"]:not(:disabled),
        summary,
        select:not(:disabled),
        label[for] {
            cursor: pointer;
        }
        :disabled,
        [aria-disabled="true"] {
            cursor: not-allowed;
        }
        :focus-visible {
            outline: 2px solid #10b981 !important;
            outline-offset: 2px !important;
        }

        /* Solid Hero & Cards (R-10: No blur on content cards) */
        .glass-hero {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.06);
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
            transition: border-color 0.2s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.2s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .glass-card:hover {
            border-color: rgba(5, 150, 105, 0.45);
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08);
        }

        .dark .glass-hero {
            background: #0f172a;
            border: 1px solid #1e293b;
            box-shadow: 0 10px 25px -5px rgba(2, 6, 23, 0.6);
        }
        .dark .glass-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            box-shadow: 0 4px 12px -2px rgba(2, 6, 23, 0.5);
        }
        .dark .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.45);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col justify-between"
      x-data="{ mobileNav: false }"
      @keydown.escape.window="mobileNav = false">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[10060] focus:px-4 focus:py-2.5 focus:bg-emerald-600 focus:text-white focus:font-bold focus:text-xs focus:rounded-xl focus:shadow-lg">
        Lewati ke konten utama
    </a>

    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">

            <a href="{{ route('home') }}" class="flex items-center space-x-2.5 sm:space-x-3 shrink-0 rounded-xl py-0.5">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm">
                    <i class="fa-solid fa-cow text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white tracking-tight">
                        SiTernak<span class="text-emerald-600 dark:text-emerald-400">-OK</span>
                    </span>
                    <p class="text-[10px] text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider">
                        Dinas Peternakan & Perkebunan Prov. Gorontalo
                    </p>
                </div>
            </a>

            @php
                $navActive = 'bg-emerald-600 text-white shadow-sm';
                $navIdle = 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800';
            @endphp
            <nav aria-label="Navigasi Utama" class="hidden lg:flex flex-1 justify-center items-center gap-1 whitespace-nowrap min-w-0">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('home') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-house {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Utama</span>
                </a>
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-chart-line {{ request()->routeIs('dashboard') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('cows.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('cows.*') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-cow {{ request()->routeIs('cows.*') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Sapi IB</span>
                </a>
                <a href="{{ route('calves.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('calves.*') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-baby-carriage {{ request()->routeIs('calves.*') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Pedet</span>
                </a>
                <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('reports.index') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-file-invoice {{ request()->routeIs('reports.index') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Kelahiran</span>
                </a>
                <a href="{{ route('reports.inseminators') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 {{ request()->routeIs('reports.inseminators') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-trophy {{ request()->routeIs('reports.inseminators') ? 'text-white' : 'text-amber-600 dark:text-amber-400' }}" aria-hidden="true"></i>
                    <span>Inseminator</span>
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)"
                    type="button"
                    aria-label="Ganti Mode Terang atau Gelap"
                    class="min-h-[40px] inline-flex items-center space-x-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-colors">
                    <i class="fa-solid" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-slate-700'" aria-hidden="true"></i>
                    <span class="hidden xl:inline" x-text="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>

                @auth
                    @if(!auth()->user()->isGuest())
                        <div class="hidden xl:flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400" aria-hidden="true"></span>
                            <span class="truncate max-w-[140px] text-slate-900 dark:text-white">{{ auth()->user()->name }}</span>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[10px] font-extrabold uppercase">
                                {{ auth()->user()->getRoleBadgeText() }}
                            </span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="hidden lg:inline">
                            @csrf
                            <button type="submit" aria-label="Keluar Akun" title="Keluar Akun" class="min-w-[40px] min-h-[40px] p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-bold border border-rose-200 dark:border-rose-800 transition-colors">
                                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="min-h-[40px] hidden sm:flex px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center gap-1.5">
                            <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                            <span>Masuk Petugas</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="min-h-[40px] flex px-3.5 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center gap-1.5">
                        <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                        <span>Masuk</span>
                    </a>
                @endauth

                <button @click="mobileNav = !mobileNav"
                    :aria-expanded="mobileNav.toString()"
                    type="button"
                    aria-label="Buka menu navigasi"
                    class="lg:hidden min-w-[40px] min-h-[40px] px-2.5 flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors text-xs font-bold">
                    <i class="fa-solid text-base" :class="mobileNav ? 'fa-xmark' : 'fa-bars'" aria-hidden="true"></i>
                    <span class="hidden xs:inline">Menu</span>
                </button>
            </div>

        </div>

        <div x-cloak x-show="mobileNav"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="lg:hidden border-t border-slate-200 dark:border-slate-800 mt-3 pt-3 pb-1">
            <nav aria-label="Navigasi Mobile" class="max-w-7xl mx-auto space-y-1">
                <a href="{{ route('home') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('home') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-house w-5 text-center {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Utama</span>
                </a>
                <a href="{{ route('dashboard') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-chart-line w-5 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('cows.index') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('cows.*') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-cow w-5 text-center {{ request()->routeIs('cows.*') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Sapi IB</span>
                </a>
                <a href="{{ route('calves.index') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('calves.*') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-baby-carriage w-5 text-center {{ request()->routeIs('calves.*') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Pedet</span>
                </a>
                <a href="{{ route('reports.index') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('reports.index') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-file-invoice w-5 text-center {{ request()->routeIs('reports.index') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" aria-hidden="true"></i>
                    <span>Kelahiran</span>
                </a>
                <a href="{{ route('reports.inseminators') }}" @click="mobileNav = false" class="min-h-[44px] flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('reports.inseminators') ? $navActive : $navIdle }}">
                    <i class="fa-solid fa-trophy w-5 text-center {{ request()->routeIs('reports.inseminators') ? 'text-white' : 'text-amber-600 dark:text-amber-400' }}" aria-hidden="true"></i>
                    <span>Inseminator</span>
                </a>

                @auth
                    @if(!auth()->user()->isGuest())
                        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-800">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400 shrink-0" aria-hidden="true"></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-[10px] font-extrabold uppercase text-emerald-700 dark:text-emerald-400">{{ auth()->user()->getRoleBadgeText() }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" aria-label="Keluar Akun" title="Keluar" class="min-w-[40px] min-h-[40px] flex items-center justify-center bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 rounded-xl border border-rose-200 dark:border-rose-800">
                                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 sm:space-y-10 flex-1 w-full">

        @if(auth()->check())

            @if(auth()->user()->isAdminProvinsi())
            <section class="glass-hero rounded-3xl p-6 sm:p-10 border-slate-200 dark:border-slate-800">
                <div class="max-w-3xl space-y-4">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-emerald-700 dark:text-emerald-400">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-[68ch]">
                        Pusat pengawasan populasi ternak, rekam pelayanan Inseminasi Buatan (IB), dan pemantauan siklus kebuntingan terintegrasi pada seluruh <strong>6 Kabupaten & Kota</strong> di Provinsi Gorontalo (Boalemo, Bone Bolango, Kab. Gorontalo, Gorontalo Utara, Pohuwato, dan Kota Gorontalo).
                    </p>

                    <div class="pt-1 flex flex-wrap items-center gap-2.5 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-building-columns text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                            <span>Administrator Utama Provinsi Gorontalo</span>
                        </span>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold tabular-nums">
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCows) }} Sapi se-Provinsi</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-syringe text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalIb) }} Tindakan IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-baby-carriage text-teal-600 dark:text-teal-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-user-tie text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalInseminators) }} Inseminator</span>
                        </div>
                    </div>
                </div>
            </section>

            @elseif(auth()->user()->isAdminKota())
            <section class="glass-hero rounded-3xl p-6 sm:p-10 border-slate-200 dark:border-slate-800">
                <div class="max-w-3xl space-y-4">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-teal-700 dark:text-teal-400">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-[68ch]">
                        Sistem manajemen dan pemantauan peternakan wilayah Kota Gorontalo. Anda memiliki hak kelola atas <strong>9 Kecamatan & 50 Kelurahan</strong> aktif pada cakupan peternak perkotaan.
                    </p>

                    <div class="pt-1 flex flex-wrap items-center gap-2.5 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                            <i class="fa-solid fa-city text-teal-600 dark:text-teal-400" aria-hidden="true"></i>
                            <span>Administrator Wilayah Kota Gorontalo</span>
                        </span>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold tabular-nums">
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-cow text-teal-600 dark:text-teal-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCows) }} Sapi Kota</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-syringe text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalIb) }} Rekam IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-baby-carriage text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-map-location-dot text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                            <span>50 Kelurahan Aktif</span>
                        </div>
                    </div>
                </div>
            </section>

            @elseif(auth()->user()->isAdminKabupaten())
            <section class="glass-hero rounded-3xl p-6 sm:p-10 border-slate-200 dark:border-slate-800">
                <div class="max-w-3xl space-y-4">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Selamat Datang, <span class="text-sky-700 dark:text-sky-400">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-[68ch]">
                        Panel pengelolaan populasi peternakan Dinas Peternakan dan Kesehatan Hewan <strong>{{ auth()->user()->regency->name ?? 'Kabupaten' }}</strong>. Anda mengelola data peternak, inseminator lapangan, dan struktur wilayah desa serta kecamatan setempat.
                    </p>

                    <div class="pt-1 flex flex-wrap items-center gap-2.5 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            <i class="fa-solid fa-shield-halved text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                            <span>Administrator Wilayah {{ auth()->user()->regency->name ?? 'Kabupaten' }}</span>
                        </span>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold tabular-nums">
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-cow text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCows) }} Sapi {{ auth()->user()->regency->name ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-syringe text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalIb) }} Pelayanan IB</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-baby-carriage text-teal-600 dark:text-teal-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCalves) }} Anak Pedet</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-users text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalFarmers) }} Peternak Terdata</span>
                        </div>
                    </div>
                </div>
            </section>

            @elseif(auth()->user()->isOperator())
            <section class="glass-hero rounded-3xl p-6 sm:p-10 border-emerald-500/30 dark:border-emerald-500/30">
                <div class="max-w-3xl space-y-4">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Selamat Bertugas di Lapangan, <span class="text-emerald-700 dark:text-emerald-400">{{ auth()->user()->name }}</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-[68ch]">
                        Pusat operasional pencatatan tindakan suntik Inseminasi Buatan (IB), pengecekan kode straw pejantan, dan konfirmasi jadwal milestone kebuntingan sapi betina di wilayah tugas Anda ({{ auth()->user()->regency->name ?? 'Gorontalo' }}).
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold tabular-nums">
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-syringe text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalIb) }} Pelayanan IB Saya</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-cow text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCows) }} Sapi Binaan</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-baby-carriage text-teal-600 dark:text-teal-400" aria-hidden="true"></i>
                            <span>{{ number_format($totalCalves) }} Pedet Lahir</span>
                        </div>
                    </div>
                </div>
            </section>
            @endif

        @else
            <section class="glass-hero rounded-3xl p-6 sm:p-10 text-left">
                <div class="max-w-3xl space-y-4">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Sistem Pemantauan Kehamilan Sapi <span class="text-emerald-600 dark:text-emerald-400">SiTernak-OK</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-[68ch]">
                        Layanan informasi resmi Dinas Peternakan dan Perkebunan Provinsi Gorontalo untuk memantau siklus kebuntingan sapi betina (Pasca IB, PKB 90 Hari, 245 Hari Siap Melahirkan, hingga HPL 283 Hari), rekapitulasi kelahiran pedet, dan capaian kinerja inseminator lapangan.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-bold text-slate-800 dark:text-slate-200 tabular-nums">
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400 text-sm" aria-hidden="true"></i>
                            <span>{{ number_format($totalCows) }} Sapi Betina</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <i class="fa-solid fa-syringe text-sky-600 dark:text-sky-400 text-sm" aria-hidden="true"></i>
                            <span>{{ number_format($totalIb) }} Rekam Inseminasi (IB)</span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <i class="fa-solid fa-baby-carriage text-teal-600 dark:text-teal-400 text-sm" aria-hidden="true"></i>
                            <span>{{ number_format($totalCalves) }} Anak Sapi (Pedet)</span>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if(auth()->check() && auth()->user()->isAdminProvinsi())
        <section class="space-y-4">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pusat Kontrol & Administrasi Provinsi Gorontalo
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Akses langsung ke data master peternak, struktur wilayah 6 Kabupaten/Kota, dan pengawasan sistem se-Provinsi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 md:col-span-2 bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-500/40">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                            <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Dashboard Pemantauan Kebuntingan se-Provinsi</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-[60ch]">
                            Pantau seluruh antrean konfirmasi milestone 3 hari, filter per Kabupaten/Kota atau petugas inseminator, serta verifikasi status kelahiran pedet secara langsung.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="min-h-[42px] inline-flex py-2.5 px-5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center">
                            Buka Dashboard Provinsi
                        </a>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400 text-lg font-bold">
                            <i class="fa-solid fa-users" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Data Peternak se-Gorontalo</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kelola data peternak lengkap dengan validasi 16-digit NIK KTP, kontak telepon, dan distribusi lokasi di 6 Kabupaten/Kota.
                        </p>
                    </div>
                    <a href="{{ route('farmers.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Data Peternak
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400 text-lg font-bold">
                            <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Wilayah Administratif Gorontalo</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Struktur master 6 Kabupaten/Kota, 77 Kecamatan, serta 722 Desa dan Kelurahan di Provinsi Gorontalo.
                        </p>
                    </div>
                    <a href="{{ route('regions.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Kelola Wilayah Gorontalo
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg font-bold">
                            <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Kelola Pengguna & Role</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Atur akun Admin Kota/Kabupaten dan Petugas Inseminator Lapangan, perbarui kata sandi, dan atur cakupan wilayah.
                        </p>
                    </div>
                    <a href="{{ route('users.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Kelola User & Role
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                            <i class="fa-solid fa-vial" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Katalog bibit straw pejantan unggul (Limosin, Simental, Brahman, PO) dari BBIB Singosari dan BIB Lembang.
                        </p>
                    </div>
                    <a href="{{ route('straws.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Database Straw
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->check() && auth()->user()->isAdminKota())
        <section class="space-y-4">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pusat Kontrol Wilayah Kota Gorontalo
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Menu operasional tata kelola peternakan perkotaan (9 Kecamatan & 50 Kelurahan).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 md:col-span-2 bg-teal-50/40 dark:bg-teal-950/20 border-teal-500/40">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-teal-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                            <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Dashboard Sapi Kota Gorontalo</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-[60ch]">
                            Pemantauan siklus kehamilan sapi betina dan pengingat konfirmasi 3 hari di seluruh 9 Kecamatan Kota Gorontalo.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="min-h-[42px] inline-flex py-2.5 px-5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center">
                            Buka Dashboard Kota
                        </a>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400 text-lg font-bold">
                            <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Kelola 50 Kelurahan Kota</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Data administratif 9 Kecamatan dan 50 Kelurahan resmi di lingkungan Pemerintah Kota Gorontalo.
                        </p>
                    </div>
                    <a href="{{ route('regions.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka 50 Kelurahan
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg font-bold">
                            <i class="fa-solid fa-users" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Data Peternak Kota Gorontalo</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Daftar peternak sapi per Kelurahan di Kota Gorontalo lengkap dengan NIK KTP dan populasi kepemilikan.
                        </p>
                    </div>
                    <a href="{{ route('farmers.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Peternak Kota
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400 text-lg font-bold">
                            <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Operator & Inseminator Kota</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kelola akun petugas inseminator lapangan khusus wilayah Kota Gorontalo.
                        </p>
                    </div>
                    <a href="{{ route('users.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Kelola User Kota
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                            <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Laporan Kelahiran Pedet Kota</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Rekapitulasi kelahiran pedet perkotaan dengan fitur ekspor PDF dan Excel.
                        </p>
                    </div>
                    <a href="{{ route('reports.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Laporan Kota
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->check() && auth()->user()->isAdminKabupaten())
        <section class="space-y-4">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pusat Kontrol Dinas Peternakan & Keswan {{ auth()->user()->regency->name ?? 'Kabupaten' }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Manajemen data peternakan, wilayah desa & kecamatan, serta pengawasan inseminator lokal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 md:col-span-2 bg-sky-50/40 dark:bg-sky-950/20 border-sky-500/40">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                            <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Dashboard Sapi {{ auth()->user()->regency->name ?? '' }}</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-[60ch]">
                            Pemantauan siklus kebuntingan dan konfirmasi milestone sapi betina di cakupan wilayah {{ auth()->user()->regency->name ?? '' }}.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="min-h-[42px] inline-flex py-2.5 px-5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center">
                            Buka Dashboard {{ auth()->user()->regency->name ?? 'Lokal' }}
                        </a>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400 text-lg font-bold">
                            <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Desa & Kecamatan {{ auth()->user()->regency->name ?? '' }}</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Data administratif seluruh desa dan kecamatan di wilayah {{ auth()->user()->regency->name ?? 'kabupaten' }}.
                        </p>
                    </div>
                    <a href="{{ route('regions.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Kelola Desa & Kecamatan
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg font-bold">
                            <i class="fa-solid fa-users" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Peternak {{ auth()->user()->regency->name ?? 'Lokal' }}</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kelola data peternak lokal, verifikasi NIK KTP peternak, dan distribusi sapi indukan.
                        </p>
                    </div>
                    <a href="{{ route('farmers.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Data Peternak Lokal
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400 text-lg font-bold">
                            <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Operator & Inseminator Lokal</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kelola data petugas IB yang bertugas di cakupan wilayah {{ auth()->user()->regency->name ?? '' }}.
                        </p>
                    </div>
                    <a href="{{ route('users.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Kelola Operator Lokal
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                            <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Laporan Kelahiran Pedet</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Rekapitulasi kelahiran anak sapi lokal dan ekspor laporan PDF serta Excel.
                        </p>
                    </div>
                    <a href="{{ route('reports.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Laporan Kelahiran
                    </a>
                </div>
            </div>
        </section>

        @elseif(auth()->check() && auth()->user()->isOperator())
        <section class="space-y-4">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pusat Kerja Operasional Inseminator Lapangan
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Pencatatan rekam suntik IB baru, pengecekan straw pejantan, dan pengingat konfirmasi kebuntingan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4 md:col-span-2 border-2 border-emerald-600 dark:border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 font-extrabold text-[10px] uppercase tracking-wider rounded-lg border border-emerald-300 dark:border-emerald-700">
                                Tugas Utama Lapangan
                            </span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Pendaftaran IB & Sapi Induk Baru</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-[60ch]">
                            Rekam tindakan suntik Inseminasi Buatan (IB) baru pada sapi indukan peternak dengan formulasi NIK otomatis dan jadwal milestone 30, 90, 245, hingga 283 hari.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('cows.create') }}" class="min-h-[44px] inline-flex py-2.5 px-5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-sm transition-colors items-center justify-center gap-2">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            <span>Input Pendaftaran IB Baru</span>
                        </a>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                            <i class="fa-solid fa-bell" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Pengingat Konfirmasi (3 Hari)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Konfirmasi status kebuntingan (YA / TIDAK) untuk sapi binaan yang memasuki batas 3 hari masa milestone.
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Pengingat Dashboard
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400 text-lg font-bold">
                            <i class="fa-solid fa-vial" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Database Straw Pejantan</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Cari kode straw, jenis ras pejantan, dan produsen semen beku resmi sebelum melakukan inseminasi.
                        </p>
                    </div>
                    <a href="{{ route('straws.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Lihat Straw Pejantan
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400 text-lg font-bold">
                            <i class="fa-solid fa-cow" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Direktori Sapi Betina IB</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Lihat riwayat lengkap sapi binaan, catatan siklus kebuntingan, dan silsilah indukan.
                        </p>
                    </div>
                    <a href="{{ route('cows.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Direktori Sapi
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg font-bold">
                            <i class="fa-solid fa-baby-carriage" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Data Anak Sapi (Pedet)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Daftar anak sapi yang lahir dari hasil pelayanan inseminasi buatan di lapangan dan promosi sapi siap IB.
                        </p>
                    </div>
                    <a href="{{ route('calves.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center">
                        Buka Data Pedet
                    </a>
                </div>
            </div>
        </section>

        @else
        <section class="space-y-4">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Layanan Informasi & Pemantauan Publik
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Pilih menu di bawah untuk memantau data populasi secara langsung (Mode Lihat Publik) atau masuk ke akun petugas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5 md:col-span-2 border-emerald-500/40 bg-emerald-50/30 dark:bg-emerald-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                                <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-white dark:bg-slate-800 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-slate-700 rounded-lg text-[10px] font-extrabold uppercase">
                                Akses Publik Terbuka
                            </span>
                        </div>

                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Dashboard Pemantauan Kebuntingan & HPL Sapi</h3>

                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-[62ch]">
                            Pantau statistik populasi sapi betina produktif, jadwal milestone kebuntingan 30, 90, dan 245 hari, serta estimasi Hari Perkiraan Lahir (HPL 283 hari) di 6 Kabupaten dan Kota Provinsi Gorontalo.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('dashboard') }}" class="min-h-[42px] inline-flex py-2.5 px-5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors items-center justify-center">
                            Buka Dashboard Pemantauan
                        </a>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                                <i class="fa-solid fa-user-shield" aria-hidden="true"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-lg text-[10px] font-extrabold uppercase">
                                Petugas & Admin
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Masuk Akun Petugas & Admin</h3>

                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Akses khusus Operator Inseminator Lapangan (sesi kerja 8 jam dengan notifikasi HP), Admin Kabupaten/Kota, dan Admin Provinsi.
                        </p>
                    </div>

                    <a href="{{ route('login') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                        <span>Masuk ke Akun Pengelola</span>
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-400 text-lg font-bold">
                            <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Rekapan Kelahiran Bulanan</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Rekapitulasi kelahiran pedet bulanan, metrik Conception Rate (CR %) dan Calving Rate (CvR %), serta unduh laporan PDF dan Excel.
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center justify-center">
                        Lihat Rekapan Kelahiran
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-700 dark:text-amber-400 text-lg font-bold">
                            <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Peringkat Kinerja Inseminator</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Daftar capaian petugas inseminator lapangan berdasarkan jumlah pelayanan IB, sapi bunting positif, dan tingkat keberhasilan.
                        </p>
                    </div>

                    <a href="{{ route('reports.inseminators') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center justify-center">
                        Lihat Peringkat Inseminator
                    </a>
                </div>

                <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-700 dark:text-sky-400 text-lg font-bold">
                            <i class="fa-solid fa-baby-carriage" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Direktori Anak Sapi (Pedet)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Data anak sapi hasil Inseminasi Buatan lengkap dengan filter ras, rentang tanggal lahir, serta silsilah induk sapi.
                        </p>
                    </div>

                    <a href="{{ route('calves.index') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center justify-center">
                        Lihat Data Anak Sapi
                    </a>
                </div>

            </div>
        </section>
        @endif

    </main>

    <footer class="px-4 sm:px-6 py-6 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 text-xs text-center sm:text-left">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} Dinas Peternakan dan Perkebunan Provinsi Gorontalo. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400" aria-hidden="true"></span>
                    Layanan Aktif
                </span>
            </div>
        </div>
    </footer>

</body>
</html>
