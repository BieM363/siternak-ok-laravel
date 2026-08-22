<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Kelahiran Bulanan Anak Sapi - Dinas Peternakan dan Perkebunan Provinsi Gorontalo</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: #000; background: #fff; margin: 0; padding: 20px; }
        .kop-header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-header h2 { margin: 0; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .kop-header h1 { margin: 2px 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-header p { margin: 2px 0; font-size: 11px; font-style: italic; }
        .title-section { text-align: center; margin-bottom: 20px; }
        .title-section h3 { margin: 0; text-transform: uppercase; font-size: 14px; text-decoration: underline; }
        .title-section p { margin: 4px 0 0 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 8px; text-align: center; font-size: 11px; text-transform: uppercase; }
        td { padding: 6px 8px; font-size: 11px; }
        .text-center { text-align: center; }
        .signature-section { width: 100%; margin-top: 40px; }
        .signature-box { text-align: center; width: 250px; float: right; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Printable Header Bar -->
    <div class="no-print" style="margin-bottom: 20px; background: #0f172a; padding: 12px; border-radius: 8px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 13px; font-weight: bold;">Dokumen Laporan SiTernak-OK Siap Dicetak</span>
        <button onclick="window.print()" style="background: #059669; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak / Download PDF
        </button>
    </div>

    <!-- Kop Surat Dinas Peternakan dan Perkebunan Provinsi Gorontalo -->
    <div class="kop-header">
        <h2>PEMERINTAH PROVINSI GORONTALO</h2>
        <h1>DINAS PETERNAKAN DAN PERKEBUNAN</h1>
        <p>Kompleks Perkantoran Blok Plan Ayula, Provinsi Gorontalo Telp. (0435) 821900 Email: disnakbun@gorontaloprov.go.id</p>
    </div>

    <!-- Document Title -->
    <div class="title-section">
        <h3>REKAPITULASI KELAHIRAN BULANAN ANAK SAPI (PEDET)</h3>
        <p>Bulan: {{ $month }}/{{ $year }} {{ $calfGender ? '| Filter Gender Anak: ' . $calfGender : '' }}</p>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>NIK Anak Sapi</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Berat Lahir (kg)</th>
                <th>NIK Induk Sapi</th>
                <th>Nama Peternak</th>
                <th>NIK Peternak</th>
                <th>Wilayah Dinas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bornCalves as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $row->calf_nik }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $row->gender }}</td>
                <td class="text-center">{{ $row->birth_date ? $row->birth_date->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $row->birth_weight ?? '-' }} kg</td>
                <td style="font-family: monospace;">{{ $row->motherCow->cow_nik ?? '-' }}</td>
                <td>{{ $row->motherCow->farmer->name ?? '-' }}</td>
                <td style="font-family: monospace;">{{ $row->motherCow->farmer->nik ?? '-' }}</td>
                <td>{{ $row->motherCow->regency->name ?? '-' }}, Kec. {{ $row->motherCow->district->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data rekam kelahiran anak sapi pada filter ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Gorontalo, {{ date('d F Y') }}</p>
            <p>Kepala Dinas Peternakan dan Perkebunan<br>Provinsi Gorontalo,</p>
            <div style="height: 60px;"></div>
            <p style="text-decoration: underline; font-weight: bold;">Drh. H. Bambang Triyono, M.Si</p>
            <p>NIP. 19780512 200312 1 004</p>
        </div>
    </div>

</body>
</html>
