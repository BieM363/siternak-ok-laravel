@extends('layouts.app')

@section('title', 'Pendaftaran Inseminasi Buatan (IB) & Sapi Induk Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="cowForm()">

    <div class="glass-card p-5 sm:p-7 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <h1 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Pendaftaran Inseminasi Buatan (IB) &amp; Sapi Baru
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Petugas Pelaksana: <strong class="text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</strong> &bull; Pelayanan IB Lapangan Provinsi Gorontalo
            </p>
        </div>
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <a href="{{ route('straws.index') }}" class="min-h-[40px] px-3.5 py-2.5 bg-teal-700 hover:bg-teal-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 flex-1 sm:flex-none">
                <i class="fa-solid fa-vial"></i>
                <span>Database Straw</span>
            </a>
            <a href="{{ route('cows.index') }}" class="min-h-[40px] px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition flex items-center justify-center gap-1.5 flex-1 sm:flex-none">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="glass-card p-5 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-8">
        <form action="{{ route('cows.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center tabular-nums">1</div>
                    <div>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                            Hirarki Wilayah &amp; Formulasi NIK Sapi
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih lokasi desa atau kelurahan untuk menghasilkan NIK barcode ternak resmi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="create-regency" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Kabupaten / Kota <span class="text-rose-500">*</span>
                        </label>
                        <select id="create-regency" x-model="selectedRegency" @change="onRegencyChange()" required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            @foreach($regencies as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create-district" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <select id="create-district" x-model="selectedDistrict" @change="onDistrictChange()" required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="d in availableDistricts" :key="d.id">
                                <option :value="d.id" x-text="d.name + ' (' + d.code + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="create-village" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Desa / Kelurahan <span class="text-rose-500">*</span>
                        </label>
                        <select id="create-village" name="village_id" x-model="selectedVillage" @change="fetchAutoNik()" required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Desa/Kelurahan --</option>
                            <template x-for="v in availableVillages" :key="v.id">
                                <option :value="v.id" x-text="(isKota ? 'Kel. ' : 'Desa ') + v.name + ' (' + v.code + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="bg-emerald-50 dark:bg-slate-950 p-4 rounded-xl border border-emerald-300 dark:border-emerald-800 flex items-center justify-between gap-3 min-w-0">
                    <div class="space-y-0.5 min-w-0">
                        <p class="text-[10px] sm:text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                            <i class="fa-solid fa-barcode mr-1"></i> Formulasi NIK Sapi Betina Otomatis:
                        </p>
                        <p class="font-mono text-sm sm:text-xl font-black text-emerald-700 dark:text-emerald-400 tracking-wider tabular-nums break-all" x-text="generatedNik || 'Pilih wilayah desa/kelurahan di atas...'"></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-300 dark:border-emerald-800 shrink-0">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center tabular-nums">2</div>
                    <div>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                            Identitas Sapi Betina &amp; Peternak Pemilik
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih peternak dari master data dan masukkan identitas indukan sapi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="create-farmer" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Peternak Pemilik Sapi <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">NIK &amp; Nama Terpisah</span>
                        </label>
                        <select id="create-farmer" name="farmer_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Peternak Pemilik (Cari Nama/NIK) --</option>
                            @foreach($farmers as $f)
                            <option value="{{ $f->id }}">
                                {{ $f->name }} &bull; NIK: {{ $f->nik }} {{ $f->phone ? '(HP: '.$f->phone.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create-cow-name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Nama / Panggilan Sapi Induk
                        </label>
                        <input id="create-cow-name" type="text" name="name" placeholder="Misal: Si Cantik, Si Putih"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label for="create-cow-breed" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Ras / Rumpun Sapi Betina <span class="text-rose-500">*</span>
                        </label>
                        <select id="create-cow-breed" name="breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
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

            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-600 text-white font-extrabold text-xs flex items-center justify-center tabular-nums">3</div>
                    <div>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                            Pencatatan Tindakan Inseminasi Buatan (IB) Perdana
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih tanggal suntik IB dan kode straw pejantan yang diinseminasikan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ib_calendar" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Tanggal Suntik IB (Hari 0) <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="insemination_date" id="ib_calendar" value="{{ date('Y-m-d') }}" x-model="ibDate" @change="calculateMilestones()" required
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-600 tabular-nums">
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Sistem akan otomatis menghitung milestone 30, 90, 245 hari &amp; HPL.</p>
                    </div>

                    <div>
                        <label for="create-semen-code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Kode Straw Pejantan</span>
                            <a href="{{ route('straws.index') }}" target="_blank" class="text-[10px] text-teal-700 dark:text-teal-400 underline font-bold">
                                + Kelola Straw
                            </a>
                        </label>
                        <select id="create-semen-code" name="semen_code" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-600">
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
                    <label for="create-officer-display" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Petugas Pelaksana Inseminator IB
                    </label>
                    <input id="create-officer-display" type="text" name="officer_display" value="{{ auth()->user()->name }} (Akun Anda)" readonly
                        class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-bold cursor-not-allowed">
                    <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                        Tindakan IB otomatis dicatat atas nama akun inseminator yang sedang aktif.
                    </p>
                </div>

                <div x-show="ibDate" x-cloak class="bg-amber-50 dark:bg-slate-950 p-4 rounded-xl border border-amber-300 dark:border-amber-700/50 space-y-3">
                    <h3 class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-amber-600 dark:text-amber-400"></i> Estimasi Milestone Otomatis Pasca IB:
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs tabular-nums">
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">Pantau Birahi 30 Hr:</span>
                            <strong class="text-sky-700 dark:text-sky-400 font-mono font-black" x-text="date30"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">PKB Rektal 90 Hr:</span>
                            <strong class="text-emerald-700 dark:text-emerald-400 font-mono font-black" x-text="date90"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">Siap Partus 245 Hr:</span>
                            <strong class="text-amber-700 dark:text-amber-400 font-mono font-black" x-text="date245"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 space-y-0.5">
                            <span class="text-[10px] text-amber-800 dark:text-amber-300 block font-bold">HPL Melahirkan:</span>
                            <strong class="text-amber-700 dark:text-amber-400 font-mono font-black" x-text="dateHpl"></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('cows.index') }}" class="w-full sm:w-auto min-h-[42px] px-5 py-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 text-center transition flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto min-h-[42px] px-7 py-3 bg-emerald-700 hover:bg-emerald-600 active:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Sapi &amp; Rekam IB Baru</span>
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
