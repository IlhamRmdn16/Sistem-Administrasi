<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Unit Global</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .info { margin-bottom: 15px; }
        table { w-full; border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-style: italic; font-size: 10px; color: #555; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Stok Unit Global (Tersedia)</h2>
        <div>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y') }} | Jam: {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
    </div>

    @if($search)
        <div class="info">
            <b>Filter Pencarian:</b> "{{ $search }}"
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Tipe</th>
                <th>Tipe Motor</th>
                <th style="width: 10%;">Gudang</th>
                <th style="width: 10%;">Showroom</th>
                <th style="width: 10%;">Sales/POP</th>
                <th style="width: 12%;">Showroom GP</th>
                <th style="width: 10%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stokTypes as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->kode_tipe }}</td>
                    <td>{{ $item->nama_type }}</td>
                    <td class="text-center">{{ $item->stok_gudang }}</td>
                    <td class="text-center">{{ $item->stok_showroom }}</td>
                    <td class="text-center">{{ $item->stok_pop }}</td>
                    <td class="text-center">{{ $item->stok_gp }}</td>
                    <td class="text-center font-bold">{{ $item->stok_total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data stok unit tersedia.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold uppercase">Total Keseluruhan :</td>
                <td class="text-center font-bold">{{ number_format($totals['gudang'], 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($totals['showroom'], 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($totals['pop'], 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($totals['gp'], 0, ',', '.') }}</td>
                <td class="text-center font-bold" style="font-size: 12px;">{{ number_format($totals['total'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name ?? 'Administrator' }}
    </div>

</body>
</html>
