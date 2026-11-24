@extends('layouts.app')

@section('title', 'Input & Riwayat IB — Inseminator')

@section('content')
@php
    $regenciesPayload = $regencies->map(function ($reg) {
        return [
            'id' => $reg->id,
            'name' => $reg->name,
            'districts' => $reg->districts->map(function ($dist) {
                return [
                    'id' => $dist->id,
                    'name' => $dist->name,
                    'villages' => $dist->villages->map(function ($vil) {
                        return [
                            'id' => $vil->id,
                            'name' => $vil->name,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    })->values();

    $strawsPayload = $straws->map(function ($st) {
        return [
            'id' => $st->id,
            'breed' => $st->breed,
            'kode_bull' => $st->effective_kode_bull,
            'kode_batch' => $st->kode_batch ?? '',
            'producer' => $st->producer ?? '',
            'label' => $st->display_label,
        ];
    })->values();

    $farmersPayload = $farmers->map(function ($f) {
        return [
            'id' => $f->id,
            'name' => $f->name,
            'nik' => $f->nik ?: 'Tanpa NIK',
            'cows_count' => (int) ($f->cows_count ?? $f->cows->count()),
            'cows' => $f->cows->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name ?: $c->cow_nik,
                    'breed' => $c->breed,
                    'status' => $c->current_status,
                ];
            })->values(),
        ];
    })->values();
@endphp

<div class="max-w-3xl mx-auto space-y-5"
     x-data="{
        tab: 'input',
        farmerMode: '{{ old('farmer_mode', 'existing') }}',
        strawMode: '{{ old('straw_mode', 'master') }}',
        regencies: @js($regenciesPayload),
        straws: @js($strawsPayload),
        farmersList: @js($farmersPayload),
        selectedFarmerId: '{{ old('farmer_id', '') }}',
        selectedRegencyId: '{{ old('regency_id', '') }}',
        selectedDistrictId: '{{ old('district_id', '') }}',
        selectedVillageId: '{{ old('village_id', '') }}',
        selectedStrawId: '{{ old('bull_straw_id', '') }}',
        nextIbModalOpen: false,
        nextIbCow: { id: '', name: '', cow_nik: '', farmer_name: '', next_ib: 2 },
        nextIbStrawMode: 'master',
        get selectedFarmerObj() {
            return this.farmersList.find(f => String(f.id) === String(this.selectedFarmerId)) || null;
        },
        get autoCowNamePlaceholder() {
            if (this.farmerMode === 'existing' && this.selectedFarmerObj) {
                return 'Kosong = otomatis ' + this.selectedFarmerObj.name + ' ' + (this.selectedFarmerObj.cows_count + 1);
            }
            return 'Kosong = otomatis [Nama Pemilik] 1';
        },
        get availableDistricts() {
            const reg = this.regencies.find(r => String(r.id) === String(this.selectedRegencyId));
            return reg ? reg.districts : [];
        },
        get availableVillages() {
            const dist = this.availableDistricts.find(d => String(d.id) === String(this.selectedDistrictId));
            return dist ? dist.villages : [];
        },
        get selectedStrawObj() {
            return this.straws.find(s => String(s.id) === String(this.selectedStrawId)) || null;
        },
        openNextIb(cow) {
            this.nextIbCow = cow;
            this.nextIbStrawMode = 'master';
            this.nextIbModalOpen = true;
        }
     }"
     @keydown.escape.window="nextIbModalOpen = false">

    {{-- Header Ringkas Mobile-First Inseminator --}}
    <section class="glass-card p-4 sm:p-5 rounded-2xl border border-emerald-700/30 bg-slate-900 text-white shadow-sm space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-950 text-emerald-300 text-[10px] font-extrabold uppercase border border-emerald-700/60">
                    <i class="fa-solid fa-syringe text-emerald-400"></i>
                    <span>Inseminator Lapangan</span>
                </span>
                <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white mt-1 truncate">
                    {{ auth()->user()->name }}
                </h1>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-950 text-amber-300 text-xs font-bold border border-amber-700/60 tabular-nums shrink-0">
                <i class="fa-solid fa-stopwatch text-amber-400"></i>
                <span data-operator-countdown class="font-mono text-white">{{ auth()->user()->getFormattedRemainingSession() }}</span>
            </span>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center tabular-nums">
            <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                <span class="block text-[10px] text-slate-300 uppercase font-bold">Total IB Saya</span>
                <strong class="text-lg font-black text-white">{{ number_format($myTotalIb) }}</strong>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                <span class="block text-[10px] text-emerald-300 uppercase font-bold">Bunting Aktif</span>
                <strong class="text-lg font-black text-emerald-400">{{ number_format($myTotalBunting) }}</strong>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                <span class="block text-[10px] text-teal-300 uppercase font-bold">Pedet Lahir</span>
                <strong class="text-lg font-black text-teal-300">{{ number_format($myTotalLahir) }}</strong>
            </div>
        </div>
    </section>

    {{-- Banner Ringkas Jika Ada Antrean Konfirmasi 3 Tahap --}}
    @if(isset($confirmationReminders) && $confirmationReminders->count() > 0)
    <a href="{{ route('confirmations.index') }}"
       class="glass-card p-3.5 rounded-2xl border-2 border-amber-500/70 bg-amber-50 dark:bg-amber-950/30 flex items-center justify-between gap-3 transition hover:border-amber-400">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0 font-black">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-extrabold text-amber-950 dark:text-amber-200 truncate">
                    {{ $confirmationReminders->count() }} Sapi Anda Perlu Konfirmasi 3 Tahap
                </p>
                <p class="text-[11px] text-amber-800 dark:text-amber-300/90 truncate">
                    Ketuk untuk buka menu Konfirmasi (Batas 3 hari auto-lanjut)
                </p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg bg-amber-500 text-slate-950 text-[11px] font-extrabold shrink-0">Buka &rarr;</span>
    </a>
    @endif

    {{-- Tab Switcher Ringan --}}
    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-200 dark:bg-slate-900 rounded-2xl border border-slate-300 dark:border-slate-800">
        <button type="button" @click="tab = 'input'"
            :class="tab === 'input' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-800 dark:text-white'"
            class="min-h-[44px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-extrabold transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Input Laporan IB</span>
        </button>
        <button type="button" @click="tab = 'history'"
            :class="tab === 'history' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-800 dark:text-white'"
            class="min-h-[44px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-extrabold transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Riwayat Saya ({{ $myRecords->total() }})</span>
        </button>
    </div>

    {{-- TAB 1: FORM INPUT LAPORAN IB RINGAN (MOBILE-FIRST) --}}
    <section x-show="tab === 'input'" class="glass-card p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-syringe text-emerald-600 dark:text-emerald-400"></i>
                <span>Form Input Laporan Inseminasi Buatan (IB)</span>
            </h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                Bebas pilih wilayah lintas kabupaten · Tanpa upload foto/GPS · Cepat di HP Android.
            </p>
        </div>

        @if($errors->any())
            <div role="alert" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('cows.store') }}" method="POST" class="space-y-5 text-xs">
            @csrf
            <input type="hidden" name="redirect_to" value="dashboard">
            <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
            <input type="hidden" name="officer_name" value="{{ auth()->user()->name }}">

            {{-- 1. LOKASI BEBAS LINTAS KABUPATEN --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <p class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot text-emerald-600 dark:text-emerald-400"></i>
                    <span>1. Lokasi Pelayanan IB (Bebas Lintas Kabupaten)</span>
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label for="ins-regency" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kabupaten/Kota <span class="text-rose-500">*</span></label>
                        <select id="ins-regency" name="regency_id" x-model="selectedRegencyId" @change="selectedDistrictId = ''; selectedVillageId = ''" required
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                            <option value="">-- Pilih Kab/Kota --</option>
                            <template x-for="r in regencies" :key="r.id">
                                <option :value="r.id" x-text="r.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="ins-district" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                        <select id="ins-district" name="district_id" x-model="selectedDistrictId" @change="selectedVillageId = ''" :disabled="!selectedRegencyId" required
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs disabled:opacity-50">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="d in availableDistricts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="ins-village" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Desa/Kelurahan <span class="text-rose-500">*</span></label>
                        <select id="ins-village" name="village_id" x-model="selectedVillageId" :disabled="!selectedDistrictId" required
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs disabled:opacity-50">
                            <option value="">-- Pilih Desa/Kel --</option>
                            <template x-for="v in availableVillages" :key="v.id">
                                <option :value="v.id" x-text="v.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. DATA PEMILIK SAPI (EXISTING ATAU CUSTOM, NIK OPSIONAL) --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-emerald-600 dark:text-emerald-400"></i>
                        <span>2. Pemilik Sapi (Peternak)</span>
                    </p>
                    <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[11px] font-bold">
                        <button type="button" @click="farmerMode = 'existing'"
                            :class="farmerMode === 'existing' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'"
                            class="px-2.5 py-1 rounded-md transition">Pilih Terdaftar</button>
                        <button type="button" @click="farmerMode = 'custom'"
                            :class="farmerMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'"
                            class="px-2.5 py-1 rounded-md transition">+ Ketik Baru (Custom)</button>
                    </div>
                    <input type="hidden" name="farmer_mode" :value="farmerMode">
                </div>

                <div x-show="farmerMode === 'existing'" class="space-y-2">
                    <label for="ins-farmer-select" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Pemilik Terdaftar <span class="text-rose-500">*</span></label>
                    <select id="ins-farmer-select" name="farmer_id" x-model="selectedFarmerId" :required="farmerMode === 'existing'"
                        class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                        <option value="">-- Pilih Nama Pemilik Sapi --</option>
                        @foreach($farmers as $f)
                            <option value="{{ $f->id }}" {{ old('farmer_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }} ({{ $f->nik ?: 'Tanpa NIK' }}) — {{ $f->cows_count ?? $f->cows->count() }} ekor sapi
                            </option>
                        @endforeach
                    </select>

                    <template x-if="selectedFarmerObj">
                        <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 space-y-1.5 text-[11px]">
                            <p class="font-extrabold text-slate-900 dark:text-white">
                                <i class="fa-solid fa-circle-info text-sky-600 dark:text-sky-400 mr-1"></i>
                                <span x-text="selectedFarmerObj.name"></span> sudah memiliki
                                <strong class="text-emerald-700 dark:text-emerald-400" x-text="selectedFarmerObj.cows_count + ' ekor sapi'"></strong> terdata.
                                (Sapi baru ini jadi ke-<strong x-text="selectedFarmerObj.cows_count + 1"></strong>)
                            </p>
                            <template x-if="selectedFarmerObj.cows.length > 0">
                                <div class="flex flex-wrap gap-1 pt-1">
                                    <template x-for="c in selectedFarmerObj.cows" :key="c.id">
                                        <a :href="'/cows-create?cow_id=' + c.id" class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] font-semibold text-slate-800 dark:text-slate-200">
                                            <span x-text="c.name + ' (' + c.breed + ')'"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div x-show="farmerMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="sm:col-span-2">
                        <label for="ins-custom-farmer-name" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Sapi Baru <span class="text-rose-500">*</span></label>
                        <input id="ins-custom-farmer-name" type="text" name="custom_farmer_name" value="{{ old('custom_farmer_name') }}" :required="farmerMode === 'custom'"
                            placeholder="Contoh: Pak Rahman Daud"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                    </div>
                    <div>
                        <label for="ins-custom-farmer-nik" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK KTP (Opsional, 16 Digit)</label>
                        <input id="ins-custom-farmer-nik" type="text" name="custom_farmer_nik" value="{{ old('custom_farmer_nik') }}" maxlength="16"
                            placeholder="Kosongkan jika tidak ada NIK"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-mono tabular-nums">
                    </div>
                    <div>
                        <label for="ins-custom-farmer-phone" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp (Opsional)</label>
                        <input id="ins-custom-farmer-phone" type="text" name="custom_farmer_phone" value="{{ old('custom_farmer_phone') }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs tabular-nums">
                    </div>
                </div>
            </div>

            {{-- 3. DATA SAPI BETINA (AKSEPTOR) --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <p class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400"></i>
                    <span>3. Data Sapi Betina (Akseptor)</span>
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="ins-breed" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis / Ras Sapi <span class="text-rose-500">*</span></label>
                        <select id="ins-breed" name="breed" required
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                            <option value="">-- Pilih Jenis Sapi --</option>
                            @foreach($breeds as $breed)
                                <option value="{{ $breed }}" {{ old('breed') === $breed ? 'selected' : '' }}>{{ $breed }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="ins-cow-name" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan Sapi (Opsional)</label>
                        <input id="ins-cow-name" type="text" name="name" value="{{ old('name') }}"
                            :placeholder="autoCowNamePlaceholder"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                    </div>
                </div>
            </div>

            {{-- 4. TANGGAL INSEMINASI (BACKDATE) & IDENTITAS PEJANTAN (3 KOLOM) --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="font-extrabold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                        <i class="fa-solid fa-vial text-emerald-600 dark:text-emerald-400"></i>
                        <span>4. Tanggal Inseminasi &amp; Identitas Pejantan (Straw)</span>
                    </p>
                    <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[11px] font-bold">
                        <button type="button" @click="strawMode = 'master'"
                            :class="strawMode === 'master' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'"
                            class="px-2.5 py-1 rounded-md transition">Master Provinsi</button>
                        <button type="button" @click="strawMode = 'custom'"
                            :class="strawMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'"
                            class="px-2.5 py-1 rounded-md transition">Input Custom</button>
                    </div>
                    <input type="hidden" name="straw_mode" :value="strawMode">
                </div>

                <div>
                    <label for="ins-date" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Inseminasi <span class="text-rose-500">*</span></label>
                    <input id="ins-date" type="date" name="insemination_date" value="{{ old('insemination_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                        class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs tabular-nums">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Bisa pilih tanggal mundur (*backdate*). Jadwal 30 hari, PKB 90 hari, dan Bulan ke-9 dihitung dari tanggal ini.</p>
                </div>

                <div x-show="strawMode === 'master'" class="space-y-2">
                    <label for="ins-straw-master" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Straw Pejantan (Master Provinsi)</label>
                    <select id="ins-straw-master" name="bull_straw_id" x-model="selectedStrawId"
                        class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                        <option value="">-- Pilih Identitas Pejantan --</option>
                        @foreach($straws as $st)
                            <option value="{{ $st->id }}">{{ $st->display_label }}</option>
                        @endforeach
                    </select>
                    <template x-if="selectedStrawObj">
                        <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800 text-[11px]">
                            <div><span class="block text-[10px] text-slate-500">Rumpun</span><strong class="text-slate-900 dark:text-white" x-text="selectedStrawObj.breed"></strong></div>
                            <div><span class="block text-[10px] text-slate-500">Kode Bull</span><strong class="font-mono text-emerald-700 dark:text-emerald-400" x-text="selectedStrawObj.kode_bull"></strong></div>
                            <div><span class="block text-[10px] text-slate-500">Kode Batch</span><strong class="font-mono text-slate-900 dark:text-white" x-text="selectedStrawObj.kode_batch || '-'"></strong></div>
                        </div>
                    </template>
                </div>

                <div x-show="strawMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label for="ins-straw-rumpun" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rumpun Pejantan</label>
                        <select id="ins-straw-rumpun" name="straw_rumpun"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                            <option value="">-- Pilih Rumpun --</option>
                            @foreach($breeds as $breed)
                                <option value="{{ $breed }}">{{ $breed }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ins-straw-bull" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Bull</label>
                        <input id="ins-straw-bull" type="text" name="straw_kode_bull" value="{{ old('straw_kode_bull') }}" placeholder="Mis: LIM-8890"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-mono">
                    </div>
                    <div>
                        <label for="ins-straw-batch" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Batch</label>
                        <input id="ins-straw-batch" type="text" name="straw_kode_batch" value="{{ old('straw_kode_batch') }}" placeholder="Mis: B26-014"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-mono">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="ins-straw-producer" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai IB (Opsional)</label>
                        <input id="ins-straw-producer" type="text" name="straw_producer" value="{{ old('straw_producer') }}" placeholder="Contoh: BBIB Singosari / BIB Lembang / Mandiri"
                            class="w-full min-h-[40px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs">
                    </div>
                </div>
            </div>

            <button type="submit" class="min-h-[48px] w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Simpan Laporan IB</span>
            </button>
        </form>
    </section>

    {{-- SECTION IB LANJUTAN CEPAT (IB ke-2, dst.) --}}
    @if($myCowsForNextIb->count() > 0)
    <section x-show="tab === 'input'" class="glass-card p-4 rounded-2xl border border-amber-300 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-950/20 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
                <i class="fa-solid fa-repeat"></i>
                <span>Sapi Siap IB Lanjutan (IB ke-2, dst.)</span>
            </h2>
            <span class="text-[11px] font-bold text-amber-800 dark:text-amber-400 tabular-nums">{{ $myCowsForNextIb->count() }} Ekor</span>
        </div>
        <div class="space-y-2">
            @foreach($myCowsForNextIb->take(5) as $readyCow)
                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/70 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-extrabold text-xs text-slate-900 dark:text-white truncate">{{ $readyCow->name }} ({{ $readyCow->breed }})</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Pemilik: {{ $readyCow->farmer->name ?? '-' }} · {{ $readyCow->regency->name ?? '-' }}</p>
                    </div>
                    <button type="button"
                        @click="openNextIb({ id: {{ $readyCow->id }}, name: '{{ addslashes($readyCow->name) }}', cow_nik: '{{ $readyCow->cow_nik }}', farmer_name: '{{ addslashes($readyCow->farmer->name ?? '-') }}', next_ib: {{ $readyCow->getNextIbNumber() }} })"
                        class="min-h-[36px] px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[11px] rounded-lg shrink-0 transition">
                        + IB ke-{{ $readyCow->getNextIbNumber() }}
                    </button>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- TAB 2: RIWAYAT INPUTAN PRIBADI INSEMINATOR --}}
    <section x-show="tab === 'history'" x-cloak class="space-y-3">
        <form action="{{ route('dashboard') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama sapi, NIK, atau pemilik..."
                class="flex-1 min-h-[42px] px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
            <button type="submit" class="min-h-[42px] px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shrink-0">Cari</button>
        </form>

        <div class="space-y-3">
            @forelse($myRecords as $rec)
                @php
                    $cow = $rec->cow;
                    $badge = $rec->status_badge;
                @endphp
                <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 font-extrabold text-[10px] border border-emerald-200 dark:border-emerald-800">
                                    IB ke-{{ $rec->ib_ke ?? 1 }}
                                </span>
                                <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400 tabular-nums">{{ $cow->cow_nik }}</span>
                            </div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mt-1 truncate">
                                {{ $cow->name }} <span class="font-normal text-xs text-slate-500">({{ $cow->breed }})</span>
                            </h3>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                Pemilik: <strong class="text-slate-800 dark:text-slate-200">{{ $cow->farmer->name ?? '-' }}</strong>
                                · {{ $cow->regency->name ?? '-' }}, Kec. {{ $cow->district->name ?? '-' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold border shrink-0 {{ $badge['bg'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[11px]">
                        <div>
                            <span class="block text-[10px] text-slate-500">Tanggal Inseminasi</span>
                            <strong class="text-slate-900 dark:text-white tabular-nums">{{ $rec->insemination_date ? $rec->insemination_date->format('d M Y') : '-' }}</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Identitas Pejantan</span>
                            <strong class="text-emerald-700 dark:text-emerald-400 truncate block">{{ $rec->straw_identity_label }}</strong>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <span class="text-[10px] text-slate-500 tabular-nums">Dilaporkan: {{ $rec->created_at ? $rec->created_at->format('d/m/Y H:i') : '-' }}</span>
                        <div class="flex items-center gap-1.5">
                            @if($cow->canReceiveNextIb())
                                <button type="button"
                                    @click="openNextIb({ id: {{ $cow->id }}, name: '{{ addslashes($cow->name) }}', cow_nik: '{{ $cow->cow_nik }}', farmer_name: '{{ addslashes($cow->farmer->name ?? '-') }}', next_ib: {{ $cow->getNextIbNumber() }} })"
                                    class="min-h-[34px] px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[11px] rounded-lg transition">
                                    + IB ke-{{ $cow->getNextIbNumber() }}
                                </button>
                            @endif
                            <a href="{{ route('cows.show', $cow->id) }}" class="min-h-[34px] px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-[11px] rounded-lg border border-slate-200 dark:border-slate-700 inline-flex items-center">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card p-8 rounded-2xl text-center text-xs text-slate-500">
                    Belum ada riwayat laporan IB Anda. Silakan input pada tab "Input Laporan IB".
                </div>
            @endforelse
        </div>

        @if($myRecords->hasPages())
            <div class="pt-2">{{ $myRecords->links() }}</div>
        @endif
    </section>

    {{-- MODAL INPUT IB LANJUTAN (IB ke-2, dst.) --}}
    <div x-show="nextIbModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="nextIbModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl w-full max-w-md shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2.5">
                <div>
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-white">
                        Input IB Lanjutan (IB ke-<span x-text="nextIbCow.next_ib"></span>)
                    </h2>
                    <p class="text-[11px] text-slate-500" x-text="nextIbCow.name + ' · Pemilik: ' + nextIbCow.farmer_name"></p>
                </div>
                <button type="button" @click="nextIbModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('pregnancy.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="cow_id" :value="nextIbCow.id">
                <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                <input type="hidden" name="officer_name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="straw_mode" :value="nextIbStrawMode">

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Inseminasi <span class="text-rose-500">*</span></label>
                    <input type="date" name="insemination_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required
                        class="w-full min-h-[42px] px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">
                </div>

                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Identitas Pejantan (Straw)</span>
                    <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[10px] font-bold">
                        <button type="button" @click="nextIbStrawMode = 'master'" :class="nextIbStrawMode === 'master' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2 py-1 rounded">Master</button>
                        <button type="button" @click="nextIbStrawMode = 'custom'" :class="nextIbStrawMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2 py-1 rounded">Custom</button>
                    </div>
                </div>

                <div x-show="nextIbStrawMode === 'master'">
                    <select name="bull_straw_id" class="w-full min-h-[42px] px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="">-- Pilih Straw Pejantan --</option>
                        @foreach($straws as $st)
                            <option value="{{ $st->id }}">{{ $st->display_label }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="nextIbStrawMode === 'custom'" x-cloak class="grid grid-cols-1 gap-2">
                    <select name="straw_rumpun" class="w-full min-h-[40px] px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="">-- Pilih Rumpun Pejantan --</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed }}">{{ $breed }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="straw_kode_bull" placeholder="Kode Bull (mis: LIM-8890)" class="w-full min-h-[40px] px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                    <input type="text" name="straw_kode_batch" placeholder="Kode Batch (mis: B26-014)" class="w-full min-h-[40px] px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="nextIbModalOpen = false" class="min-h-[40px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="min-h-[40px] px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl">Simpan IB Lanjutan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
