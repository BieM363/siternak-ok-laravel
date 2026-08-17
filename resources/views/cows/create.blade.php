@extends('layouts.app')

@section('title', 'Pendaftaran Sapi Betina Baru (Auto NIK)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="cowForm()">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-square-plus text-emerald-500"></i>
                Formulir Pendaftaran Sapi Betina Induk Baru
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sapi yang didaftarkan khusus sapi betina untuk pemantauan siklus kebuntingan.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('straws.index') }}" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold transition shadow flex items-center gap-1.5">
                <i class="fa-solid fa-vial"></i>
                <span>Database Straw Pejantan</span>
            </a>
            <a href="{{ route('cows.index') }}" class="px-3.5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('cows.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Lokasi & Formulasi NIK Sapi -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800 pb-2">
                    1. Hirarki Wilayah Dinas (Kabupaten, Kecamatan, Desa) & NIK Generator
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kabupaten -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kabupaten / Kota <span class="text-rose-500">*</span></label>
                        <select x-model="selectedRegency" @change="onRegencyChange()" required 
                            class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Kabupaten --</option>
                            @foreach($regencies as $r)
                            <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kecamatan <span class="text-rose-500">*</span></label>
                        <select x-model="selectedDistrict" @change="onDistrictChange()" required 
                            class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="d in availableDistricts" :key="d.id">
                                <option :value="d.id" x-text="d.code + ' - ' + d.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Desa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                        <select name="village_id" x-model="selectedVillage" @change="fetchAutoNik()" required 
                            class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Desa --</option>
                            <template x-for="v in availableVillages" :key="v.id">
                                <option :value="v.id" x-text="v.code + ' - ' + v.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Preview NIK -->
                <div class="bg-slate-100 dark:bg-slate-950 p-4 rounded-2xl border border-emerald-500/30 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hasil Formulasi NIK Sapi Betina Otomatis:</p>
                        <p class="font-mono text-xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-wider mt-1" x-text="generatedNik || 'Pilih lokasi desa lebih dulu...'"></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-600/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-barcode text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Section 2: Identitas Sapi & Pemilik (NIK & Nama Peternak Dipisah) -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800 pb-2">
                    2. Identitas Sapi Induk & Peternak Pemilik
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Searchable & Selectable Farmer -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pilih Peternak Pemilik (Pencarian NIK & Nama Terpisah) <span class="text-rose-500">*</span></label>
                        <select name="farmer_id" required class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Peternak Pemilik --</option>
                            @foreach($farmers as $f)
                            <option value="{{ $f->id }}">
                                NIK: {{ $f->nik }} | Nama: {{ $f->name }} (Telepon: {{ $f->phone ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">NIK KTP dan Nama Peternak dapat diketik dan dipilih dari daftar master peternak.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nama / Alias Sapi</label>
                        <input type="text" name="name" placeholder="Misal: Si Murni, Si Mawar" 
                            class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Ras / Jenis Sapi <span class="text-rose-500">*</span></label>
                        <select name="breed" required class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <option value="Limosin">Limosin</option>
                            <option value="Simental">Simental</option>
                            <option value="PO (Peranakan Ongole)">PO (Peranakan Ongole)</option>
                            <option value="Brahman">Brahman</option>
                            <option value="Frisian Holstein">Frisian Holstein (Sapi Perah)</option>
                            <option value="Madura">Madura</option>
                            <option value="Bali">Bali</option>
                            <option value="Cross / Persilangan">Cross / Persilangan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Inseminasi Buatan (IB) Awal dengan Kalender Picker Interaktif -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-amber-500"></i>
                    3. Rekam Inseminasi Buatan (IB) Awal (Pilihan Kalender Interaktif)
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pilih Tanggal Suntik IB (Hari 0) via Kalender</label>
                        <input type="date" name="insemination_date" id="ib_calendar" x-model="ibDate" @change="calculateMilestones()" 
                            class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Pilih tanggal dari kalender interaktif untuk menghitung otomatis tanggal HPL.</p>
                    </div>

                    <!-- Database Dropdown Select Kode Straw Pejantan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Kode Straw Pejantan (Database Master)</span>
                            <a href="{{ route('straws.index') }}" target="_blank" class="text-[10px] text-purple-600 dark:text-purple-400 underline font-bold">
                                + Kelola Straw
                            </a>
                        </label>
                        <select name="semen_code" class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                            <option value="">-- Pilih Kode Straw Pejantan --</option>
                            @foreach($straws as $straw)
                            <option value="{{ $straw->code }}">
                                {{ $straw->code }} - {{ $straw->bull_name }} ({{ $straw->breed }} | {{ $straw->producer }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Dipilih dari database master Kode Straw Pejantan resmi Dinas.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Operator / Inseminator <span class="text-rose-500">*</span>
                    </label>
                
                    <select
                        name="operator_id"
                        required
                        class="w-full px-4 py-3 bg-white dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500"
                    >
                        <option value="">-- Pilih Operator Pelaksana IB --</option>
                        @foreach($operators as $operator)
                            <option value="{{ $operator->id }}"
                                {{ (auth()->id() == $operator->id || old('operator_id') == $operator->id) ? 'selected' : '' }}>
                                {{ $operator->name }}
                                @if($operator->phone)
                                    - {{ $operator->phone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                        Pilih operator yang melakukan Inseminasi Buatan (IB) pada sapi ini.
                    </p>
                </div>

                <!-- Live Preview Hitungan Milestone Kebuntingan & HPL -->
                <div x-show="ibDate" x-cloak class="bg-amber-50 dark:bg-slate-950 p-4 rounded-2xl border border-amber-300 dark:border-amber-500/40 space-y-3">
                    <h5 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-clock text-amber-500"></i> Live Preview Hitungan Siklus Kebuntingan:
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">1. Estimasi 30 Hari:</span>
                            <strong class="text-blue-600 dark:text-blue-400 font-mono" x-text="date30"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">2. PKB 90 Hari:</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-mono" x-text="date90"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">3. Siap Melahirkan 245 Hr:</span>
                            <strong class="text-orange-600 dark:text-orange-400 font-mono" x-text="date245"></strong>
                        </div>
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <span class="text-[10px] text-amber-600 dark:text-amber-300 block font-bold">4. Perkiraan HPL 283 Hr:</span>
                            <strong class="text-amber-600 dark:text-amber-400 font-mono" x-text="dateHpl"></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('cows.index') }}" class="px-5 py-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Sapi Betina Baru</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function cowForm() {
        const regenciesData = @json($regencies);
        return {
            selectedRegency: '',
            selectedDistrict: '',
            selectedVillage: '',
            availableDistricts: [],
            availableVillages: [],
            generatedNik: '',
            ibDate: '',
            date30: '-',
            date90: '-',
            date245: '-',
            dateHpl: '-',

            onRegencyChange() {
                this.selectedDistrict = '';
                this.selectedVillage = '';
                this.availableVillages = [];
                this.generatedNik = '';
                const reg = regenciesData.find(r => r.id == this.selectedRegency);
                this.availableDistricts = reg ? reg.districts : [];
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
