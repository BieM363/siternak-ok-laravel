@extends('layouts.app')

@section('title', 'Pendaftaran Inseminasi Buatan (IB) & Sapi Induk Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="cowForm()">

    <!-- Header Banner -->
    <div class="glass-card p-5 sm:p-7 rounded-3xl border border-emerald-500/20 dark:border-emerald-500/10 shadow-lg bg-gradient-to-r from-emerald-900/10 via-emerald-800/5 to-transparent flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[11px] font-extrabold uppercase border border-emerald-500/30">
                <i class="fa-solid fa-syringe text-emerald-500"></i>
                Pelayanan IB Lapangan Gorontalo
            </div>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Pendaftaran Inseminasi Buatan (IB) & Sapi Baru
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Petugas Pelaksana: <strong class="text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</strong> (Inseminator Resmi)
            </p>
        </div>
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <a href="{{ route('straws.index') }}" class="px-3.5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold transition shadow flex items-center justify-center gap-1.5 flex-1 sm:flex-none">
                <i class="fa-solid fa-vial"></i>
                <span>Database Straw</span>
            </a>
            <a href="{{ route('cows.index') }}" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition flex items-center justify-center gap-1.5 flex-1 sm:flex-none">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Registration Form -->
    <div class="glass-card p-5 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl space-y-8">
        <form action="{{ route('cows.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- STEP 1: Lokasi & Formulasi NIK Sapi -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shadow">1</div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            Hirarki Wilayah & Formulasi NIK Sapi
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih lokasi desa/kelurahan untuk menghasilkan NIK barcode ternak resmi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kabupaten -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Kabupaten / Kota <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="selectedRegency" @change="onRegencyChange()" required 
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            @foreach($regencies as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="selectedDistrict" @change="onDistrictChange()" required 
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="d in availableDistricts" :key="d.id">
                                <option :value="d.id" x-text="d.name + ' (' + d.code + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Desa / Kelurahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Desa / Kelurahan <span class="text-rose-500">*</span>
                        </label>
                        <select name="village_id" x-model="selectedVillage" @change="fetchAutoNik()" required 
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                            <option value="">-- Pilih Desa/Kelurahan --</option>
                            <template x-for="v in availableVillages" :key="v.id">
                                <option :value="v.id" x-text="(isKota ? 'Kel. ' : 'Desa ') + v.name + ' (' + v.code + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Preview NIK -->
                <div class="bg-emerald-50 dark:bg-slate-950/90 p-4 rounded-2xl border border-emerald-500/40 flex items-center justify-between shadow-inner">
                    <div class="space-y-0.5">
                        <p class="text-[10px] sm:text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                            <i class="fa-solid fa-barcode mr-1"></i> Formulasi NIK Sapi Betina Otomatis:
                        </p>
                        <p class="font-mono text-base sm:text-xl font-black text-emerald-700 dark:text-emerald-400 tracking-wider" x-text="generatedNik || 'Pilih wilayah desa/kelurahan di atas...'"></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-300 dark:border-emerald-700 shrink-0">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Identitas Sapi & Peternak Pemilik -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shadow">2</div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            Identitas Sapi Betina & Peternak Pemilik
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih peternak dari master data dan masukkan identitas indukan sapi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Searchable & Selectable Farmer -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Peternak Pemilik Sapi <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-400 font-normal">NIK & Nama Terpisah</span>
                        </label>
                        <select name="farmer_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                            <option value="">-- Pilih Peternak Pemilik (Cari Nama/NIK) --</option>
                            @foreach($farmers as $f)
                            <option value="{{ $f->id }}">
                                {{ $f->name }} &bull; NIK: {{ $f->nik }} {{ $f->phone ? '(HP: '.$f->phone.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Nama / Panggilan Sapi Induk
                        </label>
                        <input type="text" name="name" placeholder="Misal: Si Cantik, Si Putih" 
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Ras / Rumpun Sapi Betina <span class="text-rose-500">*</span>
                        </label>
                        <select name="breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 shadow-sm">
                            <option value="Sapi Bali (Lokal)">Sapi Bali (Lokal Gorontalo)</option>
                            <option value="Limosin">Limosin</option>
                            <option value="Simental">Simental</option>
                            <option value="PO (Peranakan Ongole)">PO (Peranakan Ongole)</option>
                            <option value="Brahman">Brahman</option>
                            <option value="Frisian Holstein">Frisian Holstein (Sapi Perah)</option>
                            <option value="Madura">Madura</option>
                            <option value="Cross / Persilangan">Cross / Persilangan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Rekam Tindakan Inseminasi Buatan (IB) Perdana -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-xl bg-amber-500 text-white font-black text-xs flex items-center justify-center shadow">3</div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            Pencatatan Tindakan Inseminasi Buatan (IB) Perdana
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih tanggal suntik IB dan kode straw pejantan yang diinseminasikan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Tanggal Suntik IB (Hari 0) <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="insemination_date" id="ib_calendar" value="{{ date('Y-m-d') }}" x-model="ibDate" @change="calculateMilestones()" required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 shadow-sm">
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Sistem akan otomatis menghitung milestone 30, 90, 245 hari & HPL.</p>
                    </div>

                    <!-- Database Dropdown Select Kode Straw Pejantan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Kode Straw Pejantan</span>
                            <a href="{{ route('straws.index') }}" target="_blank" class="text-[10px] text-purple-600 dark:text-purple-400 underline font-bold">
                                + Kelola Straw
                            </a>
                        </label>
                        <select name="semen_code" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 shadow-sm">
                            <option value="">-- Pilih Straw Pejantan Resmi --</option>
                            @foreach($straws as $straw)
                            <option value="{{ $straw->code }}">
                                {{ $straw->code }} &bull; {{ $straw->bull_name }} ({{ $straw->breed }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Dipilih dari database master straw pejantan resmi dinas.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Petugas Pelaksana Inseminator IB
                    </label>
                    <input type="text" name="officer_display" value="{{ auth()->user()->name }} (Akun Anda)" readonly
                        class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-bold cursor-not-allowed">
                    <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                        Tindakan IB otomatis dicatat atas nama akun inseminator yang sedang aktif.
                    </p>
                </div>

                <!-- Live Preview Hitungan Milestone Kebuntingan & HPL -->
                <div x-show="ibDate" x-cloak class="bg-amber-50/80 dark:bg-slate-950 p-4 rounded-2xl border border-amber-300/80 dark:border-amber-500/40 space-y-3 shadow-inner">
                    <h5 class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-amber-500"></i> Estimasi Milestone Otomatis Pasca IB:
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">1. Pantau Birahi 30 Hr:</span>
                            <strong class="text-blue-600 dark:text-blue-400 font-mono font-black" x-text="date30"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">2. PKB Rektal 90 Hr:</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-mono font-black" x-text="date90"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">3. Siap Partus 245 Hr:</span>
                            <strong class="text-orange-600 dark:text-orange-400 font-mono font-black" x-text="date245"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-amber-700 dark:text-amber-300 block font-bold">4. HPL Melahirkan:</span>
                            <strong class="text-amber-600 dark:text-amber-400 font-mono font-black" x-text="dateHpl"></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('cows.index') }}" class="w-full sm:w-auto px-5 py-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Sapi & Rekam IB Baru</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function cowForm() {
        const regenciesData = @json($regencies);
        return {
            selectedRegency: '{{ auth()->user()->regency_id ?? '' }}',
            selectedDistrict: '',
            selectedVillage: '',
            availableDistricts: [],
            availableVillages: [],
            isKota: false,
            generatedNik: '',
            ibDate: '{{ date('Y-m-d') }}',
            date30: '-',
            date90: '-',
            date245: '-',
            dateHpl: '-',

            init() {
                if (this.selectedRegency) {
                    this.onRegencyChange();
                }
                this.calculateMilestones();
            },

            onRegencyChange() {
                this.selectedDistrict = '';
                this.selectedVillage = '';
                this.availableVillages = [];
                this.generatedNik = '';
                const reg = regenciesData.find(r => r.id == this.selectedRegency);
                this.availableDistricts = reg ? reg.districts : [];
                this.isKota = reg ? (reg.name && reg.name.toLowerCase().includes('kota')) : false;
            },

            onDistrictChange() {
                this.selectedVillage = '';
                this.generatedNik = '';
                const dist = this.availableDistricts.find(d => d.id == this.selectedDistrict);
                this.availableVillages = dist ? dist.villages : [];
            },

            fetchAutoNik() {
                if (!this.selectedVillage) return;
                fetch(`/cows-auto-nik?village_id=${this.selectedVillage}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.nik) {
                            this.generatedNik = data.nik;
                        }
                    })
                    .catch(err => console.error('Error fetching Auto NIK:', err));
            },

            calculateMilestones() {
                if (!this.ibDate) return;
                const d = new Date(this.ibDate);
                if (isNaN(d.getTime())) return;

                const addDays = (date, days) => {
                    const res = new Date(date);
                    res.setDate(res.getDate() + days);
                    return res.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                };

                this.date30 = addDays(d, 30);
                this.date90 = addDays(d, 90);
                this.date245 = addDays(d, 245);
                this.dateHpl = addDays(d, 283);
            }
        }
    }
</script>
@endsection
