<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BullStraw;
use App\Models\Cow;

/**
 * Controller Master Data Identitas Pejantan (Straw) IB
 * Dikelola oleh Admin Provinsi (distribusi dari provinsi) dengan 3 kolom utama:
 * Rumpun (breed), Kode Bull (kode_bull), dan Kode Batch (kode_batch), plus Produsen (opsional).
 */
class BullStrawController extends Controller
{
    public function index()
    {
        $straws = BullStraw::latest()->get();
        $breeds = Cow::BREEDS;

        return view('straws.index', compact('straws', 'breeds'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdminProvinsi()) {
            return back()->with('error', 'Master data straw pejantan hanya dapat dikelola oleh Admin Provinsi.');
        }

        $kodeBull = strtoupper(trim((string) ($request->input('kode_bull') ?: $request->input('code'))));
        $kodeBatch = strtoupper(trim((string) $request->input('kode_batch', '')));
        $compositeCode = $kodeBatch ? "{$kodeBull}-{$kodeBatch}" : $kodeBull;

        // Jika kode sama persis sudah ada, pastikan unik
        $codeToSave = $request->filled('code')
            ? strtoupper(trim($request->code))
            : (BullStraw::where('code', $kodeBull)->exists() ? $compositeCode : $kodeBull);

        $request->merge([
            'code' => $codeToSave,
            'kode_bull' => $kodeBull,
            'kode_batch' => $kodeBatch ?: null,
        ]);

        $request->validate([
            'breed' => 'required|string|max:80',
            'kode_bull' => 'required|string|max:60',
            'kode_batch' => 'nullable|string|max:60',
            'code' => 'required|string|max:80|unique:bull_straws,code',
            'bull_name' => 'nullable|string|max:100',
            'producer' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        BullStraw::create([
            'code' => $request->code,
            'kode_bull' => $request->kode_bull,
            'kode_batch' => $request->kode_batch,
            'bull_name' => trim((string) $request->bull_name) ?: ($request->breed . ' ' . $request->kode_bull),
            'breed' => $request->breed,
            'producer' => trim((string) $request->producer) ?: 'BBIB Singosari',
            'is_active' => true,
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Identitas Pejantan (Bull: {$request->kode_bull}) berhasil ditambahkan ke master straw Provinsi.");
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isAdminProvinsi()) {
            return back()->with('error', 'Master data straw pejantan hanya dapat diperbarui oleh Admin Provinsi.');
        }

        $straw = BullStraw::findOrFail($id);

        $kodeBull = strtoupper(trim((string) ($request->input('kode_bull') ?: $request->input('code') ?: $straw->effective_kode_bull)));
        $kodeBatch = strtoupper(trim((string) $request->input('kode_batch', '')));
        $codeToSave = $request->filled('code') ? strtoupper(trim($request->code)) : $kodeBull;

        $request->merge([
            'code' => $codeToSave,
            'kode_bull' => $kodeBull,
            'kode_batch' => $kodeBatch ?: null,
        ]);

        $request->validate([
            'breed' => 'required|string|max:80',
            'kode_bull' => 'required|string|max:60',
            'kode_batch' => 'nullable|string|max:60',
            'code' => 'required|string|max:80|unique:bull_straws,code,' . $straw->id,
            'bull_name' => 'nullable|string|max:100',
            'producer' => 'nullable|string|max:100',
            'is_active' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $straw->update([
            'code' => $request->code,
            'kode_bull' => $request->kode_bull,
            'kode_batch' => $request->kode_batch,
            'bull_name' => trim((string) $request->bull_name) ?: ($request->breed . ' ' . $request->kode_bull),
            'breed' => $request->breed,
            'producer' => trim((string) $request->producer) ?: 'BBIB Singosari',
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Master Straw Pejantan ({$straw->kode_bull}) berhasil diperbarui.");
    }

    public function destroy($id)
    {
        if (!auth()->check() || !auth()->user()->isAdminProvinsi()) {
            return back()->with('error', 'Master data straw pejantan hanya dapat dihapus oleh Admin Provinsi.');
        }

        $straw = BullStraw::findOrFail($id);
        $code = $straw->effective_kode_bull;
        $straw->delete();

        return back()->with('success', "Master Straw Pejantan {$code} berhasil dihapus.");
    }
}
