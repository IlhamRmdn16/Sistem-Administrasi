<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Global Mutasi Ke POP</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">
    <div class="header">
        <h2>LAPORAN GLOBAL MUTASI KE POP / SALES</h2>
        <p>PERIODE: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</p>
        <p style="margin-top: 3px;"><strong>FILTER POP:</strong> {{ strtoupper($nama_pop_filter) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th>TUJUAN POP / SALES</th>
                <th style="width: 100px;">KODE TIPE</th>
                <th style="width: 180px;">TIPE MOTOR</th>
                <th style="width: 90px;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $row->nama_pop ?? 'Tidak Diketahui' }}</td>
                <td class="text-center font-bold">{{ $row->kode_tipe }}</td>
                <td>{{ $row->nama_type }}</td>
                <td class="text-center font-bold">{{ $row->jumlah_unit }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">TOTAL KESELURUHAN</td>
                <td class="text-center font-bold">{{ $total_unit }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>