<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan Leasing Global</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; border: 1px solid #000; }
        th, td { border: none; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; padding: 8px 6px; border-bottom: 1px solid #000; }
        td { padding: 5px 6px; }
        tr.leasing-row td { font-weight: bold; text-transform: uppercase; font-size: 11px; padding-top: 14px; padding-bottom: 8px; color: #222; }
        tr.sub-total td { border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #f9f9f9; font-weight: bold; padding: 8px 6px; }
        tr.grand-total td { border-top: 2px solid #000; background-color: #e2e2e2; font-weight: bold; padding: 10px 6px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Penjualan Leasing - Global</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Kode Tipe</th>
                <th style="width: 25%;">Nama Tipe Motor</th>
                <th style="width: 15%;">Jumlah Unit</th>
                <th style="width: 15%;">Total OTR</th>
                <th style="width: 15%;">Uang Muka</th>
                <th style="width: 15%;">Tanda Jadi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandUnit = 0; $grandOtr = 0; $grandUm = 0; $grandTj = 0;
            @endphp
            @forelse($reports as $leasingName => $items)
                <tr class="leasing-row">
                    <td colspan="6">{{ $leasingName }}</td>
                </tr>
                @php 
                    $subUnit = 0; $subOtr = 0; $subUm = 0; $subTj = 0;
                @endphp
                @foreach($items as $row)
                    <tr>
                        <td class="text-center font-bold">{{ $row->kode_tipe }}</td>
                        <td>{{ $row->nama_type }}</td>
                        <td class="text-center font-bold">{{ $row->jumlah_unit }}</td>
                        <td class="text-right">{{ number_format($row->total_otr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->total_um, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->total_tj, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $subUnit += $row->jumlah_unit; $subOtr += $row->total_otr; $subUm += $row->total_um; $subTj += $row->total_tj;
                    @endphp
                @endforeach
                <tr class="sub-total">
                    <td colspan="2" class="text-right uppercase">Sub Total {{ $leasingName }} :</td>
                    <td class="text-center">{{ $subUnit }}</td>
                    <td class="text-right">{{ number_format($subOtr, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subUm, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subTj, 0, ',', '.') }}</td>
                </tr>
                @php
                    $grandUnit += $subUnit; $grandOtr += $subOtr; $grandUm += $subUm; $grandTj += $subTj;
                @endphp
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        @if($reports->count() > 0)
        <tfoot>
            <tr class="grand-total">
                <td colspan="2" class="text-right uppercase">Grand Total :</td>
                <td class="text-center">{{ $grandUnit }}</td>
                <td class="text-right">{{ number_format($grandOtr, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandUm, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTj, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>