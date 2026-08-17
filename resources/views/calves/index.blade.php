@extends('layouts.app')

@section('title', 'Data Anak Sapi (Pedet)')

@section('content')
<div class="space-y-6">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-baby-carriage text-emerald-500"></i>
                Data Anak Sapi (Pedet)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Rekapitulasi seluruh anak sapi lahir dari hasil Inseminasi Buatan (IB) & penarikan data ke Sapi Siap IB.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-700 transition flex items-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-file-invoice text-emerald-500"></i>
                <span>Rekapan Kelahiran</span>
            </a>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <form method="GET" action="{{ route('calves.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Cari NIK / Nama / Peternak</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NIK Anak, Induk, atau Peternak..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 pl-9 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs"></i>
                </div>
            </div>

            <!-- Tanggal Dari -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
            </div>

            <!-- Tanggal Sampai -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
            </div>

            <!-- Ras -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ras / Rumpun</label>
                <select name="breed" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Semua Ras --</option>
                    @foreach($breeds as $b)
                        <option value="{{ $b }}" {{ request('breed') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gender & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Gender</label>
                    <select name="gender" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        <option value="">Semua</option>
                        <option value="Jantan" {{ request('gender') == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                        <option value="Betina" {{ request('gender') == 'Betina' ? 'selected' : '' }}>Betina</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Calf Table Card -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-100 dark:bg-slate-900/50">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list text-emerald-500"></i>
                Daftar Anak Sapi Terdaftar ({{ $calves->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-700 dark:text-slate-300 font-extrabold">
                        <th class="py-3 px-4 whitespace-nowrap">NIK Anak Sapi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Nama Pedet</th>
                        <th class="py-3 px-4 whitespace-nowrap">Ras & Gender</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tgl Lahir</th>
                        <th class="py-3 px-4 whitespace-nowrap">Silsilah Induk & Peternak</th>
                        <th class="py-3 px-4 whitespace-nowrap">Petugas IB</th>
                        <th class="py-3 px-4 whitespace-nowrap">Status Promosi</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 text-xs bg-white dark:bg-slate-900/30">
                    @forelse($calves as $calf)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <!-- NIK Anak -->
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700 dark:text-emerald-400">
                            {{ $calf->calf_nik }}
                        </td>

                        <!-- Nama Pedet -->
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $calf->name }}
                        </td>

                        <!-- Ras & Gender -->
                        <td class="py-3.5 px-4">
                            <span class="inline-block font-semibold text-slate-800 dark:text-slate-300 mr-1">{{ $calf->breed ?? '-' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $calf->gender == 'Jantan' ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700' : 'bg-pink-100 dark:bg-pink-900/60 text-pink-800 dark:text-pink-300 border border-pink-200 dark:border-pink-700' }}">
                                {{ $calf->gender }}
                            </span>
                        </td>

                        <!-- Tgl Lahir -->
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">
                            {{ $calf->birth_date ? \Carbon\Carbon::parse($calf->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>

                        <!-- Silsilah Induk & Peternak -->
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $calf->motherCow->name ?? 'Induk Sapi' }} <span class="text-[10px] text-slate-400 font-mono">({{ $calf->motherCow->cow_nik ?? '-' }})</span></p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400"><i class="fa-solid fa-user text-[10px] text-emerald-500 mr-1"></i>{{ $calf->motherCow->farmer->name ?? '-' }} ({{ $calf->motherCow->district->name ?? '-' }})</p>
                        </td>

                        <!-- Inseminator -->
                        <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-semibold">
                            <i class="fa-solid fa-user-doctor text-emerald-500 mr-1"></i>{{ $calf->pregnancyRecord->officer_name ?? '-' }}
                        </td>

                        <!-- Status Promosi Sapi Siap IB -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($calf->is_promoted)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 text-[10px] font-extrabold uppercase">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    Promosi Sapi Siap IB
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-semibold">
                                    Belum Dipromosikan
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('calves.show', $calf->id) }}" class="p-1.5 bg-blue-100 dark:bg-blue-900/60 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-bold transition" title="Detail Pedet">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                                    <a href="{{ route('calves.edit', $calf->id) }}" class="p-1.5 bg-amber-100 dark:bg-amber-900/60 hover:bg-amber-200 dark:hover:bg-amber-800 text-amber-700 dark:text-amber-300 rounded-lg text-xs font-bold transition" title="Edit Data Pedet">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if($calf->gender == 'Betina' && !$calf->is_promoted)
                                        <form action="{{ route('calves.promote', $calf->id) }}" method="POST" onsubmit="return confirm('Promosikan anak sapi betina ini menjadi Sapi Siap IB? Data sapi baru akan dibuat otomatis.');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[10px] font-extrabold shadow transition flex items-center gap-1 whitespace-nowrap" title="Tarik ke Sapi Siap IB">
                                                <i class="fa-solid fa-angles-up"></i>
                                                <span>Tarik ke Sapi Siap IB</span>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <form action="{{ route('calves.destroy', $calf->id) }}" method="POST" onsubmit="return confirm('Hapus data anak sapi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-100 dark:bg-rose-900/60 hover:bg-rose-200 dark:hover:bg-rose-800 text-rose-700 dark:text-rose-300 rounded-lg text-xs font-bold transition" title="Hapus Pedet">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-baby-carriage text-3xl mb-2 text-slate-300 dark:text-slate-700 block"></i>
                            Belum ada data anak sapi (pedet) yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($calves->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40">
            {{ $calves->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
