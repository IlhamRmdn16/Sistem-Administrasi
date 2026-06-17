<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Unit Warna</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .info { margin-bottom: 15px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .grand-total { background-color: #e5e5e5; font-weight: bold; font-size: 11px; }
        .footer { text-align: right; margin-top: 30px; font-style: italic; font-size: 10px; color: #555; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Stok Unit Berdasarkan Warna (Tersedia)</h2>
        <div>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y') }} | Jam: {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
    </div>

    @if($searchTipe || $searchWarna)
        <div class="info">
            <b>Filter Pencarian:</b>
            @if($searchTipe) Tipe: "{{ $searchTipe }}" @endif
            @if($searchWarna) | Warna: "{{ $searchWarna }}" @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode Tipe</th>
                <th style="width: 20%;">Tipe Motor</th>
                <th>Warna</th>
                <th style="width: 8%;">Gudang</th>
                <th style="width: 8%;">Showroom</th>
                <th style="width: 8%;">POP</th>
                <th style="width: 9%;">Showroom GP</th>
                <th style="width: 8%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stokTypes as $index => $type)
                @foreach($type->colors as $color)
                    @if($color->stok_total > 0)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-bold">{{ $type->kode_tipe }}</td>
                            <td>{{ $type->nama_type }}</td>
                            <td class="font-bold text-transform: uppercase;">{{ $color->warna }}</td>
                            <td class="text-center">{{ $color->stok_gudang }}</td>
                            <td class="text-center">{{ $color->stok_showroom }}</td>
                            <td class="text-center">{{ $color->stok_pop }}</td>
                            <td class="text-center">{{ $color->stok_gp }}</td>
                            <td class="text-center font-bold">{{ $color->stok_total }}</td>
                        </tr>
                    @endif
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data stok unit tersedia.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="4" class="text-right uppercase">Grand Total :</td>
                <td class="text-center">{{ number_format($totals['gudang'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totals['showroom'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totals['pop'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totals['gp'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totals['total'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name ?? 'Administrator' }}
    </div>

</body>
</html>
