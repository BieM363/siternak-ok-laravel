<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
    <title>Selamat Datang di SiTernak-OK - Dinas Peternakan</title>
    
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
<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-bold shadow-md">
                    <i class="fa-solid fa-cow text-xl"></i>
                </div>
                <div>
                    <span class="font-extrabold text-lg text-slate-900 dark:text-white tracking-tight">SiTernak<span class="text-emerald-500">-OK</span></span>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Dinas Peternakan</p>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <!-- Theme Switcher Button -->
                <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" class="inline-flex items-center space-x-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                    <i class="fa-solid" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-600'"></i>
                    <span class="hidden sm:inline" x-text="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>

                @auth
                    @if(!auth()->user()->isGuest())
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dashboard Pengelola</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Login Pengguna</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login Pengguna</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10 flex-1 w-full">

        <!-- Welcoming Hero Banner -->
        <div class="glass-hero rounded-3xl p-6 sm:p-10 text-center sm:text-left shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 dark:opacity-10 pointer-events-none text-emerald-600 dark:text-emerald-400">
                <i class="fa-solid fa-cow text-[240px]"></i>
            </div>

            <div class="max-w-3xl space-y-4 relative z-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40 rounded-full text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Pemantauan Kehamilan Sapi
                </span>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                    Halo, Selamat Datang di Web <span class="bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">SiTernak-OK</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                    Sistem Pemantauan Kehamilan Sapi Dinas Peternakan terpadu untuk memantau siklus kebuntingan (Pasca IB, PKB 90 Hari, 245 Hari Siap Melahirkan, hingga HPL Melahirkan), rekapitulasi pedet, pembagian admin regional kabupaten/kota & provinsi, serta rangking performansi inseminator.
                </p>

                <!-- Quick Stats Badges -->
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

        <!-- Section Title: Menu Akses Fitur Utama -->
        <div class="space-y-2">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-layer-group text-emerald-600 dark:text-emerald-400"></i>
                Akses Langsung Fitur Utama Sistem
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pilih salah satu menu di bawah untuk melihat data secara langsung (Mode Read-Only) atau masuk ke akun pengelola.</p>
        </div>

        <!-- 6 Feature Access Cards Grid -->
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
                    <span>Lihat Dashboard & Data Sapi (Read-Only)</span>
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

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">2. Rekapan Kelahiran Bulanan & Filter</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rekapitulasi kelahiran bulanan pedet, filter jenis kelamin (Jantan / Betina), serta ekspor laporan resmi ke format PDF dan Excel.
                    </p>
                </div>

                <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 text-purple-700 dark:text-purple-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Lihat Rekapan Kelahiran Bulanan & Filter (Read-Only)</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 3: Konfirmasi Status Sapi & Input Anak (Read-Only) -->
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

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">3. Konfirmasi Status & Input Anak</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pantau alur konfirmasi kebuntingan (YA/TIDAK), pengingat milestone 3 Hari Max, fase melahirkan, dan pencatatan anak sapi.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-600 text-amber-700 dark:text-amber-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-amber-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Konfirmasi YA / TIDAK Status Sapi & Input Anak (Read-Only)</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 4: Inseminator Filtering & Leaderboard Recap (Read-Only) -->
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

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">4. Leaderboard & Filter Inseminator</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Rangking inseminator dengan data pelayanan IB terbanyak, nilai Conception Rate (CR %) dan Calving Rate (CvR %) per petugas.
                    </p>
                </div>

                <a href="{{ route('reports.inseminators') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-teal-600 hover:text-white dark:hover:bg-teal-600 text-teal-700 dark:text-teal-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-teal-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Inseminator Filtering & Leaderboard Recap (Read-Only)</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 5: Dedicated Calf Data Management & Promotion to Breedable Cow (Read-Only) -->
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

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">5. Data Anak Sapi & Promosi Induk</h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Direktori khusus anak sapi (pedet), filter ras & rentang tanggal lahir, silsilah induk, serta fitur penarikan pedet betina ke Sapi Siap IB.
                    </p>
                </div>

                <a href="{{ route('calves.index') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-pink-600 hover:text-white dark:hover:bg-pink-600 text-pink-700 dark:text-pink-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:border-pink-500 transition-all flex items-center justify-center gap-2 group">
                    <span>Dedicated Calf Data Management & Promotion to Breedable Cow (Read-Only)</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Card 6: Login Pengguna (Operator, Admin Kabupaten/Kota, dan Admin Provinsi) -->
            <div class="glass-card p-6 rounded-2xl flex flex-col justify-between space-y-5 border-emerald-500/40 bg-gradient-to-b from-white to-emerald-50/50 dark:from-slate-900 dark:to-emerald-950/30">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-600/80 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-bold shadow-md">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-600 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">
                            Akses Interaktif
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">6. Login Pengguna Pengelola</h3>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Login khusus untuk Operator Inseminator, Admin Kabupaten/Kota, dan Admin Provinsi untuk mengisi, mengedit, dan mengonfirmasi seluruh data sistem.
                    </p>
                </div>

                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 group">
                    <span>Login Pengguna (Operator, Admin Kab/Kota & Provinsi)</span>
                    <i class="fa-solid fa-right-to-bracket group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="px-6 py-6 border-t border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-950 text-slate-500 text-xs text-center sm:text-left">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} Dinas Peternakan. SiTernak-OK (Sistem Pemantauan Kehamilan Sapi).</p>
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
