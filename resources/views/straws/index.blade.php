@extends('layouts.app')

@section('title', 'Database Kode Straw Pejantan IB')

@section('content')
<div class="space-y-6"
     x-data="{
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
     }"
     @keydown.escape.window="openAddStrawModal = false; openEditStrawModal = false">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-vial text-teal-600 dark:text-teal-400"></i>
                Database Master Kode Straw Pejantan IB
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar semen beku atau Kode Straw Pejantan resmi dari BBIB Singosari dan Balai Inseminasi untuk pilihan saat input IB baru.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <button type="button" @click="openAddStrawModal = true" class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-teal-700 hover:bg-teal-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kode Straw Baru</span>
            </button>
            @if(auth()->check() && auth()->user()->isOperator())
            <a href="{{ route('cows.create') }}" class="flex-1 sm:flex-initial min-h-[40px] px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 whitespace-nowrap">
                <i class="fa-solid fa-square-plus"></i>
                <span>Input Sapi Induk Baru</span>
            </a>
            @endif
        </div>
    </div>

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
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($straws as $straw)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-3.5 font-mono font-extrabold text-teal-700 dark:text-teal-400 text-sm whitespace-nowrap tabular-nums">
                            <i class="fa-solid fa-vial text-teal-600 dark:text-teal-400 mr-1.5"></i>{{ $straw->code }}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white text-sm whitespace-nowrap">
                            {{ $straw->bull_name }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-sky-50 dark:bg-sky-950 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                {{ $straw->breed }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            {{ $straw->producer ?? 'BBIB Singosari' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($straw->is_active)
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700 uppercase">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 dark:bg-rose-950 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-700 uppercase">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button"
                                    @click="openEdit({{ json_encode($straw) }})"
                                    aria-label="Edit straw {{ $straw->code }}"
                                    class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950 dark:hover:bg-sky-900 text-sky-700 dark:text-sky-300 rounded-lg text-[11px] font-semibold border border-sky-200 dark:border-sky-800 transition"
                                    title="Edit Data Straw">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('straws.destroy', $straw->id) }}" method="POST" onsubmit="return confirm('Hapus Kode Straw {{ $straw->code }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Hapus straw {{ $straw->code }}" class="min-w-[32px] min-h-[32px] px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 rounded-lg text-[11px] font-semibold border border-rose-200 dark:border-rose-800 transition" title="Hapus Straw">
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

    <div x-show="openAddStrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openAddStrawModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h2 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Tambah Kode Straw Pejantan Baru</h2>
            <form action="{{ route('straws.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="add-straw-code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Straw (Unique Code) <span class="text-rose-500">*</span></label>
                    <input id="add-straw-code" type="text" name="code" required placeholder="Contoh: LIM-9901, SIM-5520" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase font-mono">
                </div>
                <div>
                    <label for="add-straw-bull" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bull Pejantan <span class="text-rose-500">*</span></label>
                    <input id="add-straw-bull" type="text" name="bull_name" required placeholder="Contoh: Bima Limosin Super" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="add-straw-breed" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ras / Rumpun Pejantan <span class="text-rose-500">*</span></label>
                    <select id="add-straw-breed" name="breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($breeds as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="add-straw-producer" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai Inseminasi</label>
                    <input id="add-straw-producer" type="text" name="producer" placeholder="Contoh: BBIB Singosari / BIB Lembang" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="add-straw-notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                    <textarea id="add-straw-notes" name="notes" placeholder="Catatan keunggulan genetik bull straw..." class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openAddStrawModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-teal-700 hover:bg-teal-600 text-white font-bold text-xs rounded-xl">Simpan Kode Straw</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditStrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80" role="dialog" aria-modal="true">
        <div @click.away="openEditStrawModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h2 class="font-extrabold text-sm text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-teal-600 dark:text-teal-400"></i>
                <span>Edit Data Kode Straw Pejantan</span>
            </h2>
            <form :action="'/straws/' + editStrawData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-straw-code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Straw (Unique Code) <span class="text-rose-500">*</span></label>
                    <input id="edit-straw-code" type="text" name="code" x-model="editStrawData.code" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase font-mono">
                </div>
                <div>
                    <label for="edit-straw-bull" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bull Pejantan <span class="text-rose-500">*</span></label>
                    <input id="edit-straw-bull" type="text" name="bull_name" x-model="editStrawData.bull_name" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="edit-straw-breed" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ras / Rumpun Pejantan <span class="text-rose-500">*</span></label>
                    <select id="edit-straw-breed" name="breed" x-model="editStrawData.breed" required class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @foreach($breeds as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit-straw-producer" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Produsen / Balai Inseminasi</label>
                    <input id="edit-straw-producer" type="text" name="producer" x-model="editStrawData.producer" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="edit-straw-status" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Ketersediaan</label>
                    <select id="edit-straw-status" name="is_active" x-model="editStrawData.is_active" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-bold">
                        <option value="1">Aktif (Tersedia untuk IB)</option>
                        <option value="0">Nonaktif (Stok Habis / Tidak Digunakan)</option>
                    </select>
                </div>
                <div>
                    <label for="edit-straw-notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                    <textarea id="edit-straw-notes" name="notes" x-model="editStrawData.notes" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="openEditStrawModal = false" class="min-h-[38px] px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="min-h-[38px] px-5 py-2 bg-teal-700 hover:bg-teal-600 text-white font-bold text-xs rounded-xl">Update Data Straw</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
