<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Terperinci Mutasi Dari POP</title>
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
        <h2>LAPORAN TERPERINCI MUTASI DARI POP / SALES</h2>
        <p>PERIODE: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</p>
        <p style="margin-top: 3px;"><strong>FILTER POP ASAL:</strong> {{ strtoupper($nama_pop_filter) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 100px;">NO. BUKTI</th>
                <th style="width: 65px;">TANGGAL</th>
                <th style="width: 130px;">ASAL POP / SALES</th>
                <th style="width: 90px;">TUJUAN</th>
                <th style="width: 65px;">NO. KUNCI</th>
                <th>TIPE MOTOR</th>
                <th style="width: 80px;">WARNA</th>
                <th style="width: 100px;">NO. MESIN</th>
                <th style="width: 100px;">NO. RANGKA</th>
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
                        <td class="text-center" rowspan="{{ $totalUnits }}">{{ $printNo++ }}</td>
                        <td class="text-center font-bold font-mono" rowspan="{{ $totalUnits }}">{{ $mutasi->no_bukti }}</td>
                        <td class="text-center" rowspan="{{ $totalUnits }}">{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('d/m/Y') }}</td>
                        <td class="font-bold" rowspan="{{ $totalUnits }}">{{ $mutasi->asalPop->nama_sales ?? 'Tidak Diketahui' }}</td>
                        <td class="text-center font-bold" rowspan="{{ $totalUnits }}">{{ $mutasi->lokasi_tujuan }}</td>
                    @endif
                    <td class="text-center font-bold font-mono">{{ $unit->no_kunci ?? '-' }}</td>
                    <td>{{ $unit->type->nama_type ?? '-' }}</td>
                    <td class="text-center">{{ $unit->color->warna ?? '-' }}</td>
                    <td class="text-center font-mono uppercase">{{ $unit->no_mesin ?? '-' }}</td>
                    <td class="text-center font-mono uppercase">{{ $unit->no_rangka ?? '-' }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="10" class="text-center italic">Tidak ada data mutasi dari POP pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>