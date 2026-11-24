@extends('layouts.app')

@section('title', $selectedCow ? 'Input IB Lanjutan (IB ke-' . $selectedCow->getNextIbNumber() . ')' : 'Input Laporan Inseminasi Buatan (IB)')

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
                        return ['id' => $vil->id, 'name' => $vil->name];
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
                    'cow_nik' => $c->cow_nik,
                    'breed' => $c->breed,
                    'status' => $c->current_status,
                ];
            })->values(),
        ];
    })->values();

    $defaultRegencyId = old('regency_id', auth()->user()->isAdminKabupaten() ? auth()->user()->regency_id : '');
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    @if($errors->any())
        <div role="alert" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-700 text-rose-800 dark:text-rose-200 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- MODE A: INPUT IB LANJUTAN (IB ke-2, dst.) UNTUK SAPI YANG SUDAH ADA --}}
    @if($selectedCow)
        <div class="glass-card p-5 sm:p-6 rounded-2xl border border-amber-300 dark:border-amber-800 shadow-sm space-y-5"
             x-data="{ strawMode: '{{ old('straw_mode', 'master') }}', selectedStrawId: '{{ old('bull_straw_id', '') }}', straws: @js($strawsPayload), get selectedStrawObj() { return this.straws.find(s => String(s.id) === String(this.selectedStrawId)) || null; } }">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-300 text-[11px] font-extrabold uppercase border border-amber-300 dark:border-amber-800">
                        <i class="fa-solid fa-repeat"></i> IB Lanjutan · IB ke-{{ $selectedCow->getNextIbNumber() }}
                    </span>
                    <h1 class="text-lg font-extrabold text-slate-900 dark:text-white mt-1.5">
                        {{ $selectedCow->name }} ({{ $selectedCow->breed }})
                    </h1>
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        NIK Sapi: <strong class="font-mono text-emerald-700 dark:text-emerald-400">{{ $selectedCow->cow_nik }}</strong>
                        · Pemilik: <strong>{{ $selectedCow->farmer->name ?? '-' }}</strong>
                        · {{ $selectedCow->regency->name ?? '-' }}, Kec. {{ $selectedCow->district->name ?? '-' }}
                    </p>
                </div>
                <a href="{{ route('cows.show', $selectedCow->id) }}" class="min-h-[38px] px-3.5 py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Sapi
                </a>
            </div>

            <form action="{{ route('pregnancy.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="cow_id" value="{{ $selectedCow->id }}">
                <input type="hidden" name="straw_mode" :value="strawMode">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="next-ib-date" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Inseminasi <span class="text-rose-500">*</span></label>
                        <input id="next-ib-date" type="date" name="insemination_date" value="{{ old('insemination_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                            class="w-full min-h-[42px] px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">
                        <p class="text-[10px] text-slate-500 mt-1">Mendukung tanggal mundur (*backdate*).</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div>
                        <label for="next-ib-operator" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Petugas Inseminator</label>
                        <select id="next-ib-operator" name="operator_id" class="w-full min-h-[42px] px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                            <option value="">-- Pilih Inseminator --</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="officer_name" value="{{ auth()->user()->name }}">
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-extrabold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider text-[11px]">
                            Identitas Pejantan (Straw) — Rumpun, Kode Bull, Kode Batch
                        </span>
                        <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[11px] font-bold">
                            <button type="button" @click="strawMode = 'master'" :class="strawMode === 'master' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">Master Provinsi</button>
                            <button type="button" @click="strawMode = 'custom'" :class="strawMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">Input Custom</button>
                        </div>
                    </div>

                    <div x-show="strawMode === 'master'" class="space-y-2">
                        <select name="bull_straw_id" x-model="selectedStrawId" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                            <option value="">-- Pilih Master Straw Provinsi --</option>
                            @foreach($straws as $st)
                                <option value="{{ $st->id }}">{{ $st->display_label }}</option>
                            @endforeach
                        </select>
                        <template x-if="selectedStrawObj">
                            <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800 text-[11px]">
                                <div><span class="block text-[10px] text-slate-500">Rumpun</span><strong x-text="selectedStrawObj.breed"></strong></div>
                                <div><span class="block text-[10px] text-slate-500">Kode Bull</span><strong class="font-mono text-emerald-700 dark:text-emerald-400" x-text="selectedStrawObj.kode_bull"></strong></div>
                                <div><span class="block text-[10px] text-slate-500">Kode Batch</span><strong class="font-mono" x-text="selectedStrawObj.kode_batch || '-'"></strong></div>
                            </div>
                        </template>
                    </div>

                    <div x-show="strawMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rumpun</label>
                            <select name="straw_rumpun" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                <option value="">-- Pilih Rumpun --</option>
                                @foreach($breeds as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Bull</label>
                            <input type="text" name="straw_kode_bull" placeholder="Contoh: LIM-8890" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Batch</label>
                            <input type="text" name="straw_kode_batch" placeholder="Contoh: B26-014" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="next-ib-notes" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea id="next-ib-notes" name="notes" rows="2" placeholder="Catatan kondisi sapi saat IB lanjutan..." class="w-full px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('cows.show', $selectedCow->id) }}" class="min-h-[44px] px-4 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold rounded-xl inline-flex items-center">Batal</a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-sm transition">
                        Simpan IB ke-{{ $selectedCow->getNextIbNumber() }}
                    </button>
                </div>
            </form>
        </div>

    {{-- MODE B: INPUT LAPORAN IB BARU / INPUT SUSULAN (BACKDATE) --}}
    @else
        <div class="glass-card p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5"
             x-data="{
                farmerMode: '{{ old('farmer_mode', 'existing') }}',
                strawMode: '{{ old('straw_mode', 'master') }}',
                regencies: @js($regenciesPayload),
                straws: @js($strawsPayload),
                farmersList: @js($farmersPayload),
                selectedFarmerId: '{{ old('farmer_id', '') }}',
                selectedRegencyId: '{{ $defaultRegencyId }}',
                selectedDistrictId: '{{ old('district_id', '') }}',
                selectedVillageId: '{{ old('village_id', '') }}',
                selectedStrawId: '{{ old('bull_straw_id', '') }}',
                autoNikPreview: 'Pilih Desa/Kelurahan...',
                get selectedFarmerObj() {
                    return this.farmersList.find(f => String(f.id) === String(this.selectedFarmerId)) || null;
                },
                get autoCowNamePlaceholder() {
                    if (this.farmerMode === 'existing' && this.selectedFarmerObj) {
                        return 'Jika kosong otomatis: ' + this.selectedFarmerObj.name + ' ' + (this.selectedFarmerObj.cows_count + 1);
                    }
                    return 'Jika kosong otomatis: [Nama Pemilik] 1';
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
                fetchAutoNik() {
                    if (!this.selectedVillageId) { this.autoNikPreview = 'Pilih Desa/Kelurahan...'; return; }
                    fetch('/cows-auto-nik?village_id=' + this.selectedVillageId)
                        .then(r => r.json())
                        .then(d => { if (d.nik) this.autoNikPreview = d.nik; })
                        .catch(() => {});
                }
             }">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-syringe text-emerald-600 dark:text-emerald-400"></i>
                        <span>{{ auth()->user()->isAdmin() ? 'Input Laporan IB Susulan (Backdate)' : 'Input Laporan Inseminasi Buatan (IB)' }}</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        @if(auth()->user()->isAdminKabupaten())
                            Mode Recorder Dinas ({{ auth()->user()->regency->name ?? 'Kabupaten/Kota' }}) — Input laporan IB susulan bagi inseminator yang mencatat manual.
                        @elseif(auth()->user()->isAdminProvinsi())
                            Mode Admin Provinsi — Dapat menginput laporan IB susulan untuk seluruh wilayah Gorontalo.
                        @else
                            Mode Inseminator — Bebas memilih Kabupaten, Kecamatan, dan Desa lintas wilayah.
                        @endif
                    </p>
                </div>
                <div class="min-w-0 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800">
                    <span class="block text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-400">NIK Sapi Otomatis</span>
                    <span class="font-mono font-black text-sm sm:text-base text-emerald-900 dark:text-emerald-300 break-all" x-text="autoNikPreview"></span>
                </div>
            </div>

            <form action="{{ route('cows.store') }}" method="POST" class="space-y-5 text-xs">
                @csrf
                <input type="hidden" name="farmer_mode" :value="farmerMode">
                <input type="hidden" name="straw_mode" :value="strawMode">

                {{-- 1. WILAYAH --}}
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-3">
                    <h2 class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px]">
                        1. Lokasi Wilayah Pelayanan IB
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="create-regency" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kabupaten/Kota <span class="text-rose-500">*</span></label>
                            <select id="create-regency" name="regency_id" x-model="selectedRegencyId" @change="selectedDistrictId = ''; selectedVillageId = ''; autoNikPreview = 'Pilih Desa/Kelurahan...'" required
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                <template x-for="r in regencies" :key="r.id">
                                    <option :value="r.id" x-text="r.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="create-district" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                            <select id="create-district" name="district_id" x-model="selectedDistrictId" @change="selectedVillageId = ''; autoNikPreview = 'Pilih Desa/Kelurahan...'" :disabled="!selectedRegencyId" required
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white disabled:opacity-50">
                                <option value="">-- Pilih Kecamatan --</option>
                                <template x-for="d in availableDistricts" :key="d.id">
                                    <option :value="d.id" x-text="d.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="create-village" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Desa/Kelurahan <span class="text-rose-500">*</span></label>
                            <select id="create-village" name="village_id" x-model="selectedVillageId" @change="fetchAutoNik()" :disabled="!selectedDistrictId" required
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white disabled:opacity-50">
                                <option value="">-- Pilih Desa/Kelurahan --</option>
                                <template x-for="v in availableVillages" :key="v.id">
                                    <option :value="v.id" x-text="v.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. PEMILIK SAPI (EXISTING ATAU CUSTOM, NIK OPSIONAL) --}}
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px]">
                            2. Data Pemilik Sapi (Peternak)
                        </h2>
                        <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[11px] font-bold">
                            <button type="button" @click="farmerMode = 'existing'" :class="farmerMode === 'existing' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">Pilih Terdaftar</button>
                            <button type="button" @click="farmerMode = 'custom'" :class="farmerMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">+ Ketik Manual (Custom)</button>
                        </div>
                    </div>

                    <div x-show="farmerMode === 'existing'" class="space-y-2.5">
                        <div>
                            <label for="create-farmer-id" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Pemilik Sapi <span class="text-rose-500">*</span></label>
                            <select id="create-farmer-id" name="farmer_id" x-model="selectedFarmerId" :required="farmerMode === 'existing'"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                <option value="">-- Pilih Peternak Terdaftar --</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}" {{ old('farmer_id') == $f->id ? 'selected' : '' }}>
                                        {{ $f->name }} — {{ $f->nik ?: 'Tanpa NIK' }} ({{ $f->cows_count ?? $f->cows->count() }} ekor sapi terdata)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <template x-if="selectedFarmerObj">
                            <div class="p-3.5 rounded-xl border transition"
                                 :class="selectedFarmerObj.cows_count > 0 ? 'bg-sky-50 dark:bg-sky-950/40 border-sky-200 dark:border-sky-800' : 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800'">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-info text-sm" :class="selectedFarmerObj.cows_count > 0 ? 'text-sky-600 dark:text-sky-400' : 'text-emerald-600 dark:text-emerald-400'"></i>
                                        <p class="text-xs font-extrabold text-slate-900 dark:text-white">
                                            Pemilik <span x-text="selectedFarmerObj.name"></span> sudah memiliki
                                            <span class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-black tabular-nums" x-text="selectedFarmerObj.cows_count + ' ekor sapi'"></span>
                                            terdata di sistem.
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                        Input ini akan menjadi sapi ke-<strong x-text="selectedFarmerObj.cows_count + 1"></strong>
                                    </span>
                                </div>

                                <template x-if="selectedFarmerObj.cows.length > 0">
                                    <div class="mt-2.5 pt-2.5 border-t border-sky-200/70 dark:border-sky-800/70 space-y-1.5">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                            Daftar Sapi Terdata Milik Peternak Ini (Klik jika ingin IB Lanjutan / IB ke-2):
                                        </p>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="c in selectedFarmerObj.cows" :key="c.id">
                                                <a :href="'/cows-create?cow_id=' + c.id"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 hover:bg-emerald-50 dark:hover:bg-emerald-950 border border-slate-200 dark:border-slate-700 text-[11px] font-semibold text-slate-800 dark:text-slate-200 transition"
                                                   title="Klik untuk input IB Lanjutan (IB ke-2, dst.) pada sapi ini">
                                                    <i class="fa-solid fa-cow text-emerald-600 dark:text-emerald-400"></i>
                                                    <strong x-text="c.name"></strong>
                                                    <span class="text-slate-500" x-text="'(' + c.breed + ' · ' + c.status + ')'"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div x-show="farmerMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="create-custom-farmer-name" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Baru <span class="text-rose-500">*</span></label>
                            <input id="create-custom-farmer-name" type="text" name="custom_farmer_name" value="{{ old('custom_farmer_name') }}" :required="farmerMode === 'custom'" placeholder="Nama lengkap peternak"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label for="create-custom-farmer-nik" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK KTP (Opsional)</label>
                            <input id="create-custom-farmer-nik" type="text" name="custom_farmer_nik" value="{{ old('custom_farmer_nik') }}" maxlength="16" placeholder="16 digit (boleh kosong)"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono tabular-nums">
                        </div>
                        <div>
                            <label for="create-custom-farmer-phone" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp (Opsional)</label>
                            <input id="create-custom-farmer-phone" type="text" name="custom_farmer_phone" value="{{ old('custom_farmer_phone') }}" placeholder="08xxxxxxxxxx"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">
                        </div>
                    </div>
                </div>

                {{-- 3. DATA SAPI BETINA (AKSEPTOR) --}}
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-3">
                    <h2 class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px]">
                        3. Data Sapi Betina (Akseptor)
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create-breed" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis / Ras Sapi <span class="text-rose-500">*</span></label>
                            <select id="create-breed" name="breed" required
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                <option value="">-- Pilih Jenis / Ras Sapi --</option>
                                @foreach($breeds as $breed)
                                    <option value="{{ $breed }}" {{ old('breed') === $breed ? 'selected' : '' }}>{{ $breed }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create-cow-name" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan Sapi (Opsional)</label>
                            <input id="create-cow-name" type="text" name="name" value="{{ old('name') }}" :placeholder="autoCowNamePlaceholder"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- 4. TANGGAL INSEMINASI (BACKDATE) & IDENTITAS PEJANTAN (3 KOLOM) --}}
                <div class="p-4 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-extrabold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider text-[11px]">
                            4. Tanggal Inseminasi &amp; Identitas Pejantan (Straw)
                        </h2>
                        <div class="inline-flex rounded-lg bg-slate-200 dark:bg-slate-800 p-0.5 text-[11px] font-bold">
                            <button type="button" @click="strawMode = 'master'" :class="strawMode === 'master' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">Master Provinsi</button>
                            <button type="button" @click="strawMode = 'custom'" :class="strawMode === 'custom' ? 'bg-emerald-600 text-white' : 'text-slate-800 dark:text-white'" class="px-2.5 py-1 rounded">Input Custom</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create-ib-date" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Inseminasi <span class="text-rose-500">*</span></label>
                            <input id="create-ib-date" type="date" name="insemination_date" value="{{ old('insemination_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white tabular-nums">
                            <p class="text-[10px] text-slate-500 mt-1">Boleh mundur (*backdate*). Estimasi 30 hr, PKB 90 hr &amp; Bulan ke-9 dihitung dari tanggal ini.</p>
                        </div>

                        <div>
                            <label for="create-operator-id" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Petugas Inseminator</label>
                            @if(auth()->user()->isAdmin())
                                <select id="create-operator-id" name="operator_id" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                    <option value="">-- Pilih Inseminator Pelaksana --</option>
                                    @foreach($operators as $op)
                                        <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="officer_name" value="{{ old('officer_name') }}" placeholder="Atau ketik nama inseminator manual (opsional)"
                                    class="w-full mt-2 min-h-[38px] px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                            @else
                                <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                                <input type="text" name="officer_name" value="{{ auth()->user()->name }}" readonly
                                    class="w-full min-h-[42px] px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 font-bold">
                            @endif
                        </div>
                    </div>

                    <div x-show="strawMode === 'master'" class="space-y-2">
                        <label for="create-straw-master" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Straw Pejantan (Master Provinsi)</label>
                        <select id="create-straw-master" name="bull_straw_id" x-model="selectedStrawId"
                            class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                            <option value="">-- Pilih Identitas Pejantan --</option>
                            @foreach($straws as $st)
                                <option value="{{ $st->id }}">{{ $st->display_label }}</option>
                            @endforeach
                        </select>
                        <template x-if="selectedStrawObj">
                            <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800 text-[11px]">
                                <div><span class="block text-[10px] text-slate-500">Rumpun</span><strong x-text="selectedStrawObj.breed"></strong></div>
                                <div><span class="block text-[10px] text-slate-500">Kode Bull</span><strong class="font-mono text-emerald-700 dark:text-emerald-400" x-text="selectedStrawObj.kode_bull"></strong></div>
                                <div><span class="block text-[10px] text-slate-500">Kode Batch</span><strong class="font-mono" x-text="selectedStrawObj.kode_batch || '-'"></strong></div>
                            </div>
                        </template>
                    </div>

                    <div x-show="strawMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rumpun Pejantan</label>
                            <select name="straw_rumpun" class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                                <option value="">-- Pilih Rumpun --</option>
                                @foreach($breeds as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Bull</label>
                            <input type="text" name="straw_kode_bull" value="{{ old('straw_kode_bull') }}" placeholder="Contoh: LIM-8890"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Batch</label>
                            <input type="text" name="straw_kode_batch" value="{{ old('straw_kode_batch') }}" placeholder="Contoh: B26-014"
                                class="w-full min-h-[42px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-slate-900 dark:text-white">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai IB (Opsional)</label>
                            <input type="text" name="straw_producer" value="{{ old('straw_producer') }}" placeholder="Contoh: BBIB Singosari / BIB Lembang / Stok Lama"
                                class="w-full min-h-[40px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="create-notes" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan (Opsional)</label>
                    <textarea id="create-notes" name="notes" rows="2" placeholder="Catatan kondisi sapi atau keterangan tambahan..."
                        class="w-full px-3.5 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] px-4 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold rounded-xl inline-flex items-center">Batal</a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Laporan IB</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
