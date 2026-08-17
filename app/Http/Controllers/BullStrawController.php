<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BullStraw;

/**
 * Controller Manajemen Database Straw Pejantan (Semen Inseminasi Buatan)
 */
class BullStrawController extends Controller
{
    public function index()
    {
        $straws = BullStraw::latest()->get();
        $breeds = ['Limosin', 'Simental', 'Frisian Holstein', 'PO (Peranakan Ongole)', 'Brahman', 'Madura', 'Cross'];
        return view('straws.index', compact('straws', 'breeds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:bull_straws,code',
            'bull_name' => 'required|string|max:100',
            'breed' => 'required|string|max:50',
            'producer' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        BullStraw::create([
            'code' => strtoupper(trim($request->code)),
            'bull_name' => $request->bull_name,
            'breed' => $request->breed,
            'producer' => $request->producer ?: 'BBIB Singosari',
            'is_active' => true,
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Kode Straw Pejantan {$request->code} berhasil ditambahkan ke database.");
    }

    public function destroy($id)
    {
        $straw = BullStraw::findOrFail($id);
        $code = $straw->code;
        $straw->delete();

        return back()->with('success', "Kode Straw Pejantan {$code} berhasil dihapus.");
    }
}
