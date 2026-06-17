<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Unit Sales Global</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .info { margin-bottom: 15px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .separator-row { background-color: #f5f5f5; height: 12px; }
        .footer { text-align: right; margin-top: 30px; font-style: italic; font-size: 10px; color: #555; }
        
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Stok Unit Sales / POP Global</h2>
        <div>Tanggal Lihat: {{ \Carbon\Carbon::now()->format('d/m/Y') }} | Jam: {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
    </div>

    @if($search)
        <div class="info">
            <b>Kata Kunci Pencarian:</b> "{{ $search }}"
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 25%;">Sales / POP</th>
                <th>Tipe Motor</th>
                <th style="width: 18%;">Warna</th>
                <th style="width: 15%;">Stok Unit</th>
            </tr>
        </thead>
        <tbody>
            @php $prevSalesId = null; @endphp
            @forelse($salesStoks as $index => $item)
                
                @if($prevSalesId !== null && $prevSalesId !== $item->lokasi_pop_id)
                    <tr>
                        <td colspan="5" class="separator-row"></td>
                    </tr>
                @endif

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold" style="text-transform: uppercase;">{{ $item->lokasiPop->nama_sales ?? 'Tanpa Nama' }}</td>
                    <td>
                        <b>{{ $item->type->nama_type ?? '-' }}</b><br>
                        <small style="color:#555;">{{ $item->type->kode_tipe ?? '-' }}</small>
                    </td>
                    <td class="text-center font-bold" style="text-transform: uppercase;">{{ $item->color->warna ?? '-' }}</td>
                    <td class="text-center font-bold" style="font-size: 12px;">{{ $item->stok_unit }}</td>
                </tr>

                @php $prevSalesId = $item->lokasi_pop_id; @endphp
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data stok unit di Sales / POP.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e5e5; font-weight: bold;">
                <td colspan="4" class="text-right uppercase">Total Keseluruhan Stok POP:</td>
                <td class="text-center" style="font-size: 13px;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name ?? 'Administrator' }}
    </div>

</body>
</html>