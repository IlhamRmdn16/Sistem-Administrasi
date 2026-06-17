<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Unit Detil</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 15px; text-transform: uppercase; }
        .info { margin-bottom: 15px; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 6px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        .footer { text-align: right; margin-top: 30px; font-style: italic; font-size: 9px; color: #555; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Stok Unit Detil (Tersedia)</h2>
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
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">No. Kunci</th>
                <th>Tipe Motor</th>
                <th style="width: 12%;">Warna</th>
                <th style="width: 15%;">No. Mesin</th>
                <th style="width: 18%;">No. Rangka</th>
                <th style="width: 16%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $index => $unit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold font-mono">{{ $unit->no_kunci ?? '-' }}</td>
                    <td>
                        <b>{{ $unit->type->nama_type ?? '-' }}</b><br>
                        <small style="color:#555;">{{ $unit->type->kode_tipe ?? '-' }}</small>
                    </td>
                    <td class="font-bold text-center" style="text-transform: uppercase;">{{ $unit->color->warna ?? '-' }}</td>
                    <td class="font-bold font-mono text-center" style="text-transform: uppercase;">{{ $unit->no_mesin }}</td>
                    <td class="font-mono uppercase">{{ $unit->no_rangka }}</td>
                    <td class="text-center font-bold" style="text-transform: uppercase;">
                        {{ $unit->posisi_stok }} {{ $unit->posisi_stok === 'POP' ? ($unit->lokasiPop->nama_sales ?? '') : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data unit motor tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name ?? 'Administrator' }}
    </div>

</body>
</html>
