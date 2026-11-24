@extends('layouts.app')

@section('title', 'Notifikasi & Konfirmasi 3 Tahap Kebuntingan')

@section('content')
<div class="space-y-6" x-data="{ showPushModal: false }" @keydown.escape.window="showPushModal = false">

    {{-- Header Menu Konfirmasi 3 Tahap --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-card p-5 sm:p-6 rounded-2xl border border-emerald-600/30 bg-slate-900 text-white shadow-sm">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white">
                    Notifikasi 3 Tahap Kebuntingan (Auto-Lanjut 3 Hari)
                </h1>
                <span class="px-2.5 py-0.5 rounded-lg bg-amber-500 text-slate-950 font-black text-xs tabular-nums">
                    {{ number_format($totalAll) }} Sapi Perlu Konfirmasi
                </span>
            </div>
            <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                @if(auth()->check() && auth()->user()->isAdminKabupaten())
                    Antrean konfirmasi khusus wilayah <strong>{{ auth()->user()->regency->name ?? 'Kabupaten/Kota Anda' }}</strong> pada <strong>Hari ke-30</strong>, <strong>PKB Hari ke-90</strong>, dan <strong>Bulan ke-9</strong>.
                @elseif(auth()->check() && auth()->user()->isOperator() && !auth()->user()->isAdmin())
                    Antrean konfirmasi khusus sapi yang Anda inseminasi. Konfirmasi hasil pengecekan atau koordinasikan dengan Admin Kabupaten/Kota.
                @else
                    Antrean konfirmasi 3 tahap kebuntingan seluruh Provinsi Gorontalo (dihitung dari Tanggal Inseminasi). Jika tidak direspons dalam <strong>3 hari</strong>, status otomatis berlanjut.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" @click="showPushModal = true"
                class="min-h-[40px] px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-bell"></i>
                <span>Notifikasi HP / Peramban</span>
            </button>
        </div>
    </div>

    {{-- Tab Ringkasan Per Tahap (Semua, Tahap 1, Tahap 2, Tahap 3) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 tabular-nums">
        <a href="{{ route('confirmations.index', request()->except('stage', 'page')) }}"
           class="glass-card p-4 rounded-2xl border {{ !$stage ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-extrabold uppercase text-slate-500">Semua Tahap</span>
            <strong class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ number_format($totalAll) }}</strong>
            <span class="text-[11px] text-slate-500">Total antrean aktif</span>
        </a>
        <a href="{{ route('confirmations.index', array_merge(request()->except('page'), ['stage' => '1'])) }}"
           class="glass-card p-4 rounded-2xl border {{ $stage === '1' ? 'border-sky-500 ring-1 ring-sky-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-extrabold uppercase text-sky-700 dark:text-sky-400">Tahap 1 · Hari ke-30</span>
            <strong class="text-2xl font-black text-sky-700 dark:text-sky-400 mt-0.5 block">{{ number_format($countStage1) }}</strong>
            <span class="text-[11px] text-slate-500">Evaluasi awal pasca IB</span>
        </a>
        <a href="{{ route('confirmations.index', array_merge(request()->except('page'), ['stage' => '2'])) }}"
           class="glass-card p-4 rounded-2xl border {{ $stage === '2' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-extrabold uppercase text-emerald-700 dark:text-emerald-400">Tahap 2 · PKB 90 Hari</span>
            <strong class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-0.5 block">{{ number_format($countStage2) }}</strong>
            <span class="text-[11px] text-slate-500">Pemeriksaan kebuntingan</span>
        </a>
        <a href="{{ route('confirmations.index', array_merge(request()->except('page'), ['stage' => '3'])) }}"
           class="glass-card p-4 rounded-2xl border {{ $stage === '3' ? 'border-amber-500 ring-1 ring-amber-500' : 'border-slate-200 dark:border-slate-800' }}">
            <span class="block text-[10px] font-extrabold uppercase text-amber-700 dark:text-amber-400">Tahap 3 · Bulan ke-9</span>
            <strong class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block">{{ number_format($countStage3) }}</strong>
            <span class="text-[11px] text-slate-500">Konfirmasi kelahiran pedet</span>
        </a>
    </div>

    {{-- Filter Antrean Konfirmasi --}}
    <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('confirmations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            @if($stage)
                <input type="hidden" name="stage" value="{{ $stage }}">
            @endif

            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama sapi, NIK, atau pemilik..."
                class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">

            @if(!auth()->check() || !auth()->user()->isAdminKabupaten())
                <select name="regency_id" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ $regencyId == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            @endif

            @if(!auth()->check() || !(auth()->user()->isOperator() && !auth()->user()->isAdmin()))
                <select name="officer_name" class="w-full min-h-[38px] px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="">-- Semua Inseminator --</option>
                    @foreach($officers as $off)
                        <option value="{{ $off }}" {{ $officerName == $off ? 'selected' : '' }}>{{ $off }}</option>
                    @endforeach
                </select>
            @endif

            <div class="flex gap-2">
                <button type="submit" class="flex-1 min-h-[38px] px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-xl">Filter</button>
                <a href="{{ route('confirmations.index') }}" class="min-h-[38px] px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl flex items-center justify-center" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Grid Kartu Konfirmasi (Paginated 12 per Halaman agar Ringan & Rapi) --}}
    @if($confirmationReminders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($confirmationReminders as $rec)
                @php
                    $cow = $rec->cow;
                    $stageInfo = $rec->reminder_stage_info;
                    $targetDate = $stageInfo['target_date'];
                @endphp

                @if($cow)
                <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3 flex flex-col justify-between shadow-sm">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-slate-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-slate-800 truncate tabular-nums">
                                {{ $cow->cow_nik }}
                            </span>
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-300 font-extrabold text-[10px] rounded border border-amber-300 dark:border-amber-800 shrink-0">
                                IB ke-{{ $rec->ib_ke ?? 1 }}
                            </span>
                        </div>

                        <div>
                            <a href="{{ route('cows.show', $cow->id) }}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 block">
                                {{ $cow->name ?? 'Sapi ' . $cow->cow_nik }}
                                <span class="text-slate-500 font-normal text-xs">({{ $cow->breed }})</span>
                            </a>
                            <p class="text-[11px] font-bold text-amber-700 dark:text-amber-400 mt-0.5">
                                {{ $stageInfo['label'] }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-500">Pemilik:</span>
                                <strong class="text-slate-900 dark:text-white truncate max-w-[60%] text-right">{{ $cow->farmer->name ?? '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-500">Wilayah:</span>
                                <strong class="text-slate-800 dark:text-slate-200 text-right truncate max-w-[65%]">{{ $cow->regency->name ?? '-' }} · Kec. {{ $cow->district->name ?? '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-500">Inseminator:</span>
                                <strong class="text-emerald-700 dark:text-emerald-400 text-right truncate max-w-[60%]">{{ $rec->officer_name ?? '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-2 tabular-nums">
                                <span class="text-slate-500">Tgl Konfirmasi:</span>
                                <strong class="text-slate-900 dark:text-white">{{ $targetDate ? \Carbon\Carbon::parse($targetDate)->format('d M Y') : '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-2 tabular-nums">
                                <span class="text-slate-500">Estimasi Lahir:</span>
                                <strong class="text-amber-700 dark:text-amber-400 font-mono">{{ $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d M Y') : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <a href="{{ $rec->getGoogleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" class="text-sky-700 dark:text-sky-400 hover:underline font-bold">
                                <i class="fa-solid fa-calendar-plus mr-1"></i> + Google Calendar
                            </a>
                            <span class="text-amber-700 dark:text-amber-400 font-bold">Auto-Lanjut 3 Hari</span>
                        </div>

                        @if(auth()->check() && auth()->user()->canOperate())
                            @if($rec->status === 'Fase Melahirkan')
                                <div x-data="{
                                        showBirthModal: false,
                                        calfName: 'Pedet {{ addslashes($cow->name ?? 'Sapi') }} 1',
                                        calfGender: 'Jantan',
                                        calfBreed: '{{ addslashes($rec->straw_rumpun ?: ($cow->breed ?? 'Limosin')) }}',
                                        calfWeight: '30',
                                        calfCondition: 'Sehat'
                                     }" @keydown.escape.window="showBirthModal = false">
                                    <div class="flex gap-2">
                                        <button type="button" @click="showBirthModal = true"
                                            class="flex-1 min-h-[38px] py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition">
                                            <i class="fa-solid fa-check mr-1"></i> YA (Melahirkan)
                                        </button>

                                        <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="action" value="birth_no">
                                            <button type="submit" onclick="return confirm('Tandai sapi gagal melahirkan / tidak bunting?')"
                                                class="min-h-[38px] py-2 px-3 bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition">
                                                <i class="fa-solid fa-xmark mr-1"></i> TIDAK
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal Pencatatan Kelahiran Pedet --}}
                                    <div x-show="showBirthModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
                                        <div @click.away="showBirthModal = false" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden max-h-[90vh] overflow-y-auto">
                                            <div class="px-5 py-4 bg-emerald-700 text-white flex items-center justify-between">
                                                <div>
                                                    <h3 class="font-extrabold text-sm">Konfirmasi Kelahiran Pedet</h3>
                                                    <p class="text-[11px] text-emerald-100">{{ $cow->name }} ({{ $cow->cow_nik }})</p>
                                                </div>
                                                <button type="button" @click="showBirthModal = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST" class="p-5 space-y-3.5 text-xs">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="action" value="birth_yes">

                                                <div>
                                                    <label class="block font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Nama Pedet</label>
                                                    <input type="text" name="calf_name" x-model="calfName" required class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
                                                        <select name="calf_gender" x-model="calfGender" required class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                                            <option value="Jantan">Jantan</option>
                                                            <option value="Betina">Betina</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Ras Pedet</label>
                                                        <select name="calf_breed" x-model="calfBreed" required class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                                            @foreach($breeds as $breed)
                                                                <option value="{{ $breed }}">{{ $breed }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Berat Lahir (kg)</label>
                                                        <input type="number" step="0.1" min="0" name="calf_weight" x-model="calfWeight" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">
                                                    </div>
                                                    <div>
                                                        <label class="block font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Kondisi</label>
                                                        <select name="calf_condition" x-model="calfCondition" class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                                            <option value="Sehat">Sehat</option>
                                                            <option value="Cacat">Cacat</option>
                                                            <option value="Mati">Mati</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 pt-2">
                                                    <button type="button" @click="showBirthModal = false" class="flex-1 min-h-[40px] py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold rounded-xl">Batal</button>
                                                    <button type="submit" class="flex-1 min-h-[40px] py-2 bg-emerald-700 hover:bg-emerald-600 text-white font-extrabold rounded-xl">Simpan Kelahiran</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="confirm_yes">
                                        <button type="submit" class="w-full min-h-[38px] py-2 px-3 bg-emerald-700 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl transition">
                                            <i class="fa-solid fa-check mr-1"></i> YA (Bunting)
                                        </button>
                                    </form>
                                    <form action="{{ route('pregnancy.update-status', $rec->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="confirm_no">
                                        <button type="submit" onclick="return confirm('Konfirmasi TIDAK (Tidak Bunting / Siap IB ke-2)?')" class="w-full min-h-[38px] py-2 px-3 bg-rose-700 hover:bg-rose-600 text-white font-extrabold text-xs rounded-xl transition">
                                            <i class="fa-solid fa-xmark mr-1"></i> TIDAK
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if($confirmationReminders->hasPages())
            <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
                {{ $confirmationReminders->links() }}
            </div>
        @endif
    @else
        <div class="glass-card p-10 rounded-2xl border border-slate-200 dark:border-slate-800 text-center space-y-2">
            <i class="fa-solid fa-circle-check text-4xl text-emerald-500"></i>
            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Tidak Ada Antrean Konfirmasi Saat Ini</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Seluruh sapi pada cakupan wilayah/tugas Anda sudah dikonfirmasi atau belum memasuki jendela pengingat Hari ke-30, PKB Hari ke-90, maupun Bulan ke-9.
            </p>
        </div>
    @endif

    {{-- Modal Pengaturan Notifikasi Peramban Ponsel --}}
    <div x-show="showPushModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="showPushModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-mobile-screen-button text-emerald-600"></i>
                    <span>Pengaturan Notifikasi Peramban HP</span>
                </h3>
                <button @click="showPushModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="bg-slate-100 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 text-xs space-y-2">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-semibold text-slate-600 dark:text-slate-400">Status Peramban:</span>
                    <span data-browser-perm-badge class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">Mendeteksi...</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                    <strong>Tips Android (Chrome / Opera):</strong> Jika status <em>Diblokir</em>, ketuk <strong>ikon Perisai / Gembok</strong> di samping kiri alamat URL atas &rarr; <strong>Izin Situs / Site Settings</strong> &rarr; <strong>Notifikasi &rarr; Izinkan</strong>, lalu pastikan notifikasi browser aktif di Pengaturan Android.
                </p>
            </div>

            <div class="space-y-2">
                <button data-btn-request-perm type="button" onclick="window.SiTernakNotifier && window.SiTernakNotifier.requestPermission()"
                    class="w-full min-h-[40px] py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bell"></i>
                    <span>Izinkan Notifikasi Bar Sistem HP</span>
                </button>
                <button type="button" @click="showPushModal = false; window.SiTernakNotifier && window.SiTernakNotifier.syncCronAndNotify()"
                    class="w-full min-h-[40px] py-2.5 px-4 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Uji Notifikasi ke HP Sekarang</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
