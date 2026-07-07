<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Terperinci Mutasi Dari Showroom</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 15px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0 0 5px 0; font-size: 14px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        td { vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">
    <div class="header">
        <h2>LAPORAN TERPERINCI MUTASI DARI SHOWROOM PUSAT</h2>
        <p>PERIODE: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 100px;">NO. BUKTI</th>
                <th style="width: 65px;">TANGGAL</th>
                <th style="width: 100px;">TUJUAN MUTASI</th>
                <th style="width: 70px;">NO. KUNCI</th>
                <th>TIPE MOTOR</th>
                <th style="width: 80px;">WARNA</th>
                <th style="width: 100px;">NO. MESIN</th>
                <th style="width: 100px;">NO. RANGKA</th>
                <th style="width: 45px;">TAHUN</th>
            </tr>
        </thead>
        <tbody>
            @php $printNo = 1; @endphp
            @forelse($data as $mutasi)
                @php $totalUnits = count($mutasi->details); @endphp
                @foreach($mutasi->details as $subIndex => $detail)
                @php $unit = $detail->motorUnit; @endphp
                <tr>
                    @if($subIndex === 0)
                        <td rowspan="{{ $totalUnits }}" class="text-center">{{ $printNo++ }}</td>
                        <td rowspan="{{ $totalUnits }}" class="text-center font-bold font-mono">{{ $mutasi->no_bukti }}</td>
                        <td rowspan="{{ $totalUnits }}" class="text-center">{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('d/m/Y') }}</td>
                        <td rowspan="{{ $totalUnits }}" class="text-center">{{ $mutasi->lokasi_tujuan === 'POP' ? 'POP - ' . ($mutasi->tujuanPop->nama_sales ?? '') : $mutasi->lokasi_tujuan }}</td>
                    @endif
                    <td class="text-center font-bold font-mono">{{ $unit->no_kunci ?? '-' }}</td>
                    <td>{{ $unit->type->nama_type ?? '-' }}</td>
                    <td class="text-center">{{ $unit->color->warna ?? '-' }}</td>
                    <td class="text-center font-mono uppercase">{{ $unit->no_mesin ?? '-' }}</td>
                    <td class="text-center font-mono uppercase">{{ $unit->no_rangka ?? '-' }}</td>
                    <td class="text-center">{{ $unit->tahun_pembuatan ?? '-' }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="10" class="text-center italic">Tidak ada data mutasi dari Showroom pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>