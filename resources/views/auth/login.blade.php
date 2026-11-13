<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiTernak-OK (Dinas Peternakan & Perkebunan Prov. Gorontalo)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex items-center justify-center p-4">

    <div class="w-full max-w-lg space-y-6">
        
        <!-- Header Brand Logo -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 items-center justify-center text-white shadow-xl shadow-emerald-900/30 mb-2">
                <i class="fa-solid fa-cow text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">SiTernak<span class="text-emerald-500">-OK</span></h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Sistem Pemantauan Kehamilan Sapi & Otomasi HPL</p>
            <span class="inline-block px-3 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-emerald-600 dark:text-emerald-400 text-xs font-semibold rounded-full shadow-sm">Dinas Peternakan & Perkebunan Prov. Gorontalo</span>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">

            @if($errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-500/50 text-rose-700 dark:text-rose-300 p-4 rounded-xl text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Email Pengguna</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" value="admin@siternak.go.id" required 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" value="password" required 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-emerald-600 focus:ring-emerald-500 mr-2">
                        Ingat Saya
                    </label>

                    <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" class="text-slate-500 hover:text-emerald-600 text-xs font-semibold flex items-center gap-1">
                        <i class="fa-solid" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-600'"></i>
                        <span x-text="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"></span>
                    </button>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk Aplikasi</span>
                </button>
            </form>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                <span class="flex-shrink mx-4 text-[11px] uppercase tracking-widest text-slate-400 font-semibold">Preset Akses Demo Wilayah Gorontalo</span>
                <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
            </div>

            <!-- Demo Preset Buttons for Admin Provinsi vs Admin Kab/Kota vs Operators -->
            <div class="space-y-2">
                <!-- Group Admin -->
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Akses Tingkat Administrasi</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button onclick="setCredentials('admin@siternak.go.id', 'password')" class="px-2.5 py-2 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 border border-purple-200 dark:border-purple-800/60 text-purple-700 dark:text-purple-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-purple-600 dark:text-purple-400 font-bold uppercase">Semua Kab/Kota</span>
                        Admin Prov. Gorontalo
                    </button>
                    <button onclick="setCredentials('admin.gorontalo@siternak.go.id', 'password')" class="px-2.5 py-2 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800/60 text-blue-700 dark:text-blue-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-blue-600 dark:text-blue-400 font-bold uppercase">Scope Kab. Gorontalo</span>
                        Admin Kab. Gorontalo
                    </button>
                    <button onclick="setCredentials('admin.kotagorontalo@siternak.go.id', 'password')" class="px-2.5 py-2 bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/60 border border-teal-200 dark:border-teal-800/60 text-teal-700 dark:text-teal-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-teal-600 dark:text-teal-400 font-bold uppercase">Scope Kota Gorontalo</span>
                        Admin Kota Gorontalo
                    </button>
                    <button onclick="setCredentials('admin.boalemo@siternak.go.id', 'password')" class="px-2.5 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-emerald-600 dark:text-emerald-400 font-bold uppercase">Scope Kab. Boalemo</span>
                        Admin Kab. Boalemo
                    </button>
                </div>

                <!-- Group Operator -->
                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Akses Operator Inseminator</p>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-[9px] font-bold text-emerald-700 dark:text-emerald-300 flex items-center gap-1">
                        <i class="fa-solid fa-mobile-screen-button"></i> Sesi 8 Jam & Notif HP
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="setCredentials('operator@siternak.go.id', 'password')" class="px-2.5 py-2.5 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-amber-600 dark:text-amber-400 font-bold uppercase">Operator A</span>
                        Budi (Inseminator Gorontalo)
                    </button>
                    <button onclick="setCredentials('operator2@siternak.go.id', 'password')" class="px-2.5 py-2.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 text-[11px] font-semibold rounded-xl transition-all text-center">
                        <span class="block text-[9px] text-indigo-600 dark:text-indigo-400 font-bold uppercase">Operator B</span>
                        Taufik (Inseminator Gorontalo)
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed pt-1">
                    <i class="fa-solid fa-stopwatch text-emerald-500 mr-1"></i>
                    Khusus <strong>Operator Lapangan</strong>: Waktu login aktif dibatasi <strong>8 jam</strong> (via Cronjob) dengan notifikasi otomatis ke peramban ponsel (HP).
                </p>
            </div>

            <a href="{{ route('home') }}" class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Halaman Utama (Public Mode)</span>
            </a>

        </div>

    </div>

    <script>
        function setCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
