<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Sisa Stok Accu</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; border: 1px solid #000; }
        th, td { border: none; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; padding: 8px 6px; border-bottom: 1px solid #000; }
        td { padding: 6px; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 11px; font-weight: bold; }
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Kontrol Accu - Sisa Stok Toko</h2>
        <div>Waktu Cetak: {{ date('d/m/Y H:i') }} WIB</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 25%; text-align: left;">No. Accu</th>
                <th style="width: 15%;">No. Kunci</th>
                <th style="text-align: left;">Tipe Motor</th>
                <th style="width: 25%; text-align: left;">Posisi Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $row)
                <tr>
                    <td class="text-center" style="color: #666;">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $row->no_accu }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $row->no_kunci }}</td>
                    <td>{{ $row->nama_type }}</td>
                    <td style="font-weight: bold; text-transform: uppercase;">{{ $row->posisi_display }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Tidak ada sisa stok accu di dalam gudang.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
