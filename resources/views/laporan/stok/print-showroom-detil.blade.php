<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 15px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>{{ $judulLaporan }} (Tersedia)</h2>
        <div>Tanggal Lihat: {{ \Carbon\Carbon::now()->format('d/m/Y') }} | Jam: {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">No. Kunci</th>
                <th>Tipe Motor</th>
                <th style="width: 15%;">Warna</th>
                <th style="width: 8%;">Tahun</th>
                <th style="width: 20%;">No. Mesin</th>
                <th style="width: 22%;">No. Rangka</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $index => $unit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold font-mono">{{ $unit->no_kunci ?? '-' }}</td>
                    <td><b>{{ $unit->type->nama_type ?? '-' }}</b><br><small>{{ $unit->type->kode_tipe ?? '-' }}</small></td>
                    <td class="text-center font-bold uppercase">{{ $unit->color->warna ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $unit->tahun_pembuatan ?? '-' }}</td>
                    <td class="font-bold font-mono text-center uppercase">{{ $unit->no_mesin }}</td>
                    <td class="font-mono uppercase">{{ $unit->no_rangka }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data unit di {{ strtolower($posisiStok) }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>