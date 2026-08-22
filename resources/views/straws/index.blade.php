@extends('layouts.app')

@section('title', 'Database Kode Straw Pejantan IB')

@section('content')
<div class="space-y-6" x-data="{ 
    openAddStrawModal: false,
    openEditStrawModal: false,
    editStrawData: {
        id: '',
        code: '',
        bull_name: '',
        breed: '',
        producer: '',
        is_active: 1,
        notes: ''
    },
    openEdit(s) {
        this.editStrawData = {
            id: s.id,
            code: s.code,
            bull_name: s.bull_name,
            breed: s.breed,
            producer: s.producer || '',
            is_active: s.is_active ? 1 : 0,
            notes: s.notes || ''
        };
        this.openEditStrawModal = true;
    }
}">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-vial text-purple-500"></i>
                Database Master Kode Straw Pejantan IB
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar semen / Kode Straw Pejantan resmi dari BBIB Singosari & Balai Inseminasi untuk pilihan otomatis saat input sapi betina baru.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <button @click="openAddStrawModal = true" class="flex-1 sm:flex-initial px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kode Straw Baru</span>
            </button>
            @if(auth()->check() && auth()->user()->isOperator())
            <a href="{{ route('cows.create') }}" class="flex-1 sm:flex-initial px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-square-plus"></i>
                <span>Input Sapi Induk Baru</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Table Straw Pejantan -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 dark:text-slate-300 border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kode Straw</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Pejantan</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Ras / Rumpun</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Balai / Produsen</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80 bg-white dark:bg-slate-900/30">
                    @forelse($straws as $straw)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="px-4 py-3.5 font-mono font-extrabold text-purple-600 dark:text-purple-400 text-sm whitespace-nowrap">
                            <i class="fa-solid fa-vial text-purple-500 mr-1.5"></i>{{ $straw->code }}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white text-sm whitespace-nowrap">
                            {{ $straw->bull_name }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                                {{ $straw->breed }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            {{ $straw->producer ?? 'BBIB Singosari' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($straw->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 uppercase">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-700 uppercase">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <!-- Tombol Edit Straw -->
                                <button type="button" 
                                    @click="openEdit({{ json_encode($straw) }})" 
                                    class="px-2.5 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-950/80 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-xl text-[11px] font-semibold border border-blue-300 dark:border-blue-800 transition" 
                                    title="Edit Data Straw">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <!-- Tombol Hapus Straw -->
                                <form action="{{ route('straws.destroy', $straw->id) }}" method="POST" onsubmit="return confirm('Hapus Kode Straw {{ $straw->code }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/80 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 rounded-xl text-[11px] font-semibold border border-rose-300 dark:border-rose-800 transition" title="Hapus Straw">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400">Belum ada Kode Straw Pejantan terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Straw Pejantan -->
    <div x-show="openAddStrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openAddStrawModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Kode Straw Pejantan Baru</h4>
            <form action="{{ route('straws.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Straw (Unique Code) *</label>
                    <input type="text" name="code" required placeholder="Contoh: LIM-9901, SIM-5520" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bull Pejantan *</label>
                    <input type="text" name="bull_name" required placeholder="Contoh: Bima Limosin Super" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ras / Rumpun Pejantan *</label>
                    <select name="breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($breeds as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai Inseminasi</label>
                    <input type="text" name="producer" placeholder="Contoh: BBIB Singosari / BIB Lembang" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                    <textarea name="notes" placeholder="Catatan keunggulan genetik bull straw..." class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openAddStrawModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow">Simpan Kode Straw</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Straw Pejantan -->
    <div x-show="openEditStrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openEditStrawModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-purple-500"></i>
                <span>Edit Data Kode Straw Pejantan</span>
            </h4>
            <form :action="'/straws/' + editStrawData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Straw (Unique Code) *</label>
                    <input type="text" name="code" x-model="editStrawData.code" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bull Pejantan *</label>
                    <input type="text" name="bull_name" x-model="editStrawData.bull_name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ras / Rumpun Pejantan *</label>
                    <select name="breed" x-model="editStrawData.breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($breeds as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai Inseminasi</label>
                    <input type="text" name="producer" x-model="editStrawData.producer" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Ketersediaan</label>
                    <select name="is_active" x-model="editStrawData.is_active" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-bold">
                        <option value="1">Aktif (Tersedia untuk IB)</option>
                        <option value="0">Nonaktif (Stok Habis / Tidak Digunakan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                    <textarea name="notes" x-model="editStrawData.notes" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openEditStrawModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow">Update Data Straw</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
