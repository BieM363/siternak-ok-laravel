<!DOCTYPE html>
<html lang="id" class="h-full overflow-x-hidden max-w-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Masuk Aplikasi - SiTernak-OK (Sistem Pemantauan Kebuntingan Sapi)</title>
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
                    fontFamily: { sans: ['"Public Sans"', 'sans-serif'] },
                    colors: { emerald: { 500: '#10b981', 600: '#059669', 700: '#047857' } }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::selection { background-color: rgba(16, 185, 129, 0.22); color: inherit; }
        input { caret-color: #059669; }
        a[href], button:not(:disabled), label[for], input[type="checkbox"]:not(:disabled) { cursor: pointer; }
        :focus-visible { outline: 2px solid #10b981 !important; outline-offset: 2px !important; }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex items-center justify-center p-4 overflow-x-hidden max-w-full">

    <main id="main-content" class="w-full max-w-lg min-w-0 space-y-6 my-6">

        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-emerald-600 items-center justify-center text-white shadow-sm mb-1">
                <i class="fa-solid fa-cow text-2xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                SiTernak<span class="text-emerald-600 dark:text-emerald-400">-OK</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
                Sistem Pemantauan Kebuntingan Sapi &amp; Otomasi 3 Tahap
            </p>
            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">
                Dinas Peternakan dan Perkebunan Provinsi Gorontalo
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-6">

            @if($errors->any())
            <div role="alert" class="bg-rose-50 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-700 text-rose-800 dark:text-rose-200 p-4 rounded-xl text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1.5" aria-hidden="true"></i>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- 1 Pintu Login Tunggal untuk 3 Role Utama (Admin Provinsi, Admin Kabupaten/Kota, Inseminator) --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Email Akun</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@siternak.go.id') }}" required autocomplete="email"
                            placeholder="nama@siternak.go.id"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:border-emerald-500 transition-colors placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        </div>
                        <input type="password" id="password" name="password" value="password" required autocomplete="current-password"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:border-emerald-500 transition-colors">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label for="remember" class="flex items-center text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                        <input type="checkbox" id="remember" name="remember" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 mr-2">
                        <span>Ingat Saya</span>
                    </label>

                    <button @click="theme = (theme === 'light' ? 'dark' : 'light'); localStorage.setItem('theme', theme)" type="button" class="min-h-[36px] px-2.5 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-semibold flex items-center gap-1.5 rounded-lg">
                        <i class="fa-solid" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-slate-700'" aria-hidden="true"></i>
                        <span x-text="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"></span>
                    </button>
                </div>

                <button type="submit" class="min-h-[44px] w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-sm transition-colors flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                    <span>Masuk Aplikasi</span>
                </button>
            </form>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                <span class="flex-shrink mx-3 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">3 Role Utama (Contoh Akun Demo)</span>
                <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
            </div>

            <div class="grid grid-cols-1 gap-2">
                <button type="button" onclick="setCredentials('admin@siternak.go.id', 'password')" class="min-h-[48px] px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-bold rounded-xl transition-colors text-left">
                    <span class="block text-[10px] text-emerald-700 dark:text-emerald-400 font-extrabold uppercase">Role 1 · Admin Provinsi</span>
                    Seluruh Gorontalo, Master Wilayah &amp; Distribusi Straw
                </button>

                <button type="button" onclick="setCredentials('admin.gorontalo@siternak.go.id', 'password')" class="min-h-[48px] px-3.5 py-2.5 bg-sky-50 dark:bg-sky-950/50 hover:bg-sky-100 dark:hover:bg-sky-900/60 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs font-bold rounded-xl transition-colors text-left">
                    <span class="block text-[10px] text-sky-700 dark:text-sky-400 font-extrabold uppercase">Role 2 · Admin Kabupaten/Kota (Recorder Dinas)</span>
                    1 Pintu Login · Data otomatis terfilter sesuai Kabupaten/Kota akun
                </button>

                <button type="button" onclick="setCredentials('operator@siternak.go.id', 'password')" class="min-h-[48px] px-3.5 py-2.5 bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800 text-amber-950 dark:text-amber-200 text-xs font-bold rounded-xl transition-colors text-left">
                    <span class="block text-[10px] text-amber-700 dark:text-amber-400 font-extrabold uppercase">Role 3 · Inseminator (Mobile-First)</span>
                    Input laporan IB ringan lintas kabupaten &amp; riwayat pribadi
                </button>
            </div>

            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                <i class="fa-solid fa-circle-info text-emerald-600 dark:text-emerald-400 mr-1"></i>
                Admin Kab/Kota lainnya cukup login di pintu yang sama dengan email daerahnya (contoh: <code class="font-mono text-[10px]">admin.kotagorontalo@siternak.go.id</code> atau <code class="font-mono text-[10px]">admin.boalemo@siternak.go.id</code>, sandi <code class="font-mono text-[10px]">password</code>).
            </p>

            <a href="{{ route('home') }}" class="min-h-[42px] w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center justify-center space-x-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                <span>Kembali ke Halaman Utama (Mode Publik)</span>
            </a>

        </div>

    </main>

    <script>
        function setCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
