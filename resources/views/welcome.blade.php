<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiTernak-OK | Dinas Peternakan dan Perkebunan Provinsi Gorontalo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Public Sans', sans-serif; }
        button:not(:disabled), [role="button"]:not(:disabled), a[href] { cursor: pointer; }
        :focus-visible { outline: 2px solid #047857; outline-offset: 2px; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-6">
    <main class="max-w-lg w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-700 flex items-center justify-center text-white text-xl font-bold shrink-0">
                <i class="fa-solid fa-cow"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">SiTernak-OK Gorontalo</h1>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider">Dinas Peternakan dan Perkebunan Provinsi Gorontalo</p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
            Sistem Informasi Pemantauan Inseminasi Buatan (IB), Pemeriksaan Kebuntingan (PKB 30/90/245 Hari), dan Rekapitulasi Kelahiran Pedet terintegrasi untuk 6 Kabupaten dan Kota di Provinsi Gorontalo.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <a href="{{ route('home') }}" class="flex-1 min-h-[42px] px-5 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i>
                <span>Beranda Utama</span>
            </a>
            <a href="{{ route('dashboard') }}" class="flex-1 min-h-[42px] px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-600 dark:text-emerald-400"></i>
                <span>Buka Dashboard</span>
            </a>
        </div>
    </main>
</body>
</html>
