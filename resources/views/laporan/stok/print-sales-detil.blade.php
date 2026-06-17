<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Unit Sales Detil</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 15px; text-transform: uppercase; }
        .info { margin-bottom: 15px; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        .separator-row { background-color: #f5f5f5; height: 10px; }
        .footer { text-align: right; margin-top: 30px; font-style: italic; font-size: 9px; color: #555; }
        
        /* Instruksi ke browser agar mencetak dalam format Horizontal (Landscape) */
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Stok Unit Sales / POP Detil</h2>
        <div>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y') }} | Jam: {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
    </div>

    @if($search)
        <div class="info">
            <b>Kata Kunci Pencarian:</b> "{{ $search }}"
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 14%;">Sales / POP</th>
                <th style="width: 7%;">Tgl Mutasi</th>
                <th style="width: 8%;">No. Kunci</th>
                <th>Tipe Motor</th>
                <th style="width: 10%;">Warna</th>
                <th style="width: 5%;">Thn</th>
                <th style="width: 14%;">No. Mesin</th>
                <th style="width: 16%;">No. Rangka</th>
            </tr>
        </thead>
        <tbody>
            @php $prevSalesId = null; @endphp
            @forelse($salesDetils as $index => $item)
                
                @if($prevSalesId !== null && $prevSalesId !== $item->lokasi_pop_id)
                    <tr>
                        <td colspan="9" class="separator-row"></td>
                    </tr>
                @endif

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold" style="text-transform: uppercase;">{{ $item->lokasiPop->nama_sales ?? 'Tanpa Nama' }}</td>
                    <td class="text-center">{{ $item->tgl_mutasi ? \Carbon\Carbon::parse($item->tgl_mutasi)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center font-bold font-mono">{{ $item->no_kunci ?? '-' }}</td>
                    <td>
                        <b>{{ $item->type->nama_type ?? '-' }}</b><br>
                        <small style="color:#555;">{{ $item->type->kode_tipe ?? '-' }}</small>
                    </td>
                    <td class="font-bold text-center" style="text-transform: uppercase;">{{ $item->color->warna ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $item->tahun_pembuatan ?? '-' }}</td>
                    <td class="font-bold font-mono text-center" style="text-transform: uppercase;">{{ $item->no_mesin }}</td>
                    <td class="font-mono uppercase">{{ $item->no_rangka }}</td>
                </tr>

                @php $prevSalesId = $item->lokasi_pop_id; @endphp
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data unit motor di Sales / POP.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name ?? 'Administrator' }}
    </div>

</body>
</html>