<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan Leasing Terperinci</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; border: 1px solid #000; }
        th, td { border: none; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; padding: 8px 6px; border-bottom: 1px solid #000; }
        td { padding: 5px 6px; }
        tr.leasing-row td { font-weight: bold; text-transform: uppercase; font-size: 11px; padding-top: 14px; padding-bottom: 8px; color: #222; text-align: left; }
        tr.sub-total td { border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #f9f9f9; font-weight: bold; padding: 8px 6px; }
        tr.grand-total td { border-top: 2px solid #000; background-color: #e2e2e2; font-weight: bold; padding: 10px 6px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Penjualan Leasing - Terperinci</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tgl. SPK</th>
                <th style="width: 20%;">Nama Pemohon</th>
                <th style="width: 20%;">Nama STNK</th>
                <th style="width: 10%;">No. Kunci</th>
                <th>Nama Tipe Motor</th>
                <th style="width: 15%;">Uang Muka</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandUm = 0;
                $grandUnit = 0;
            @endphp
            @forelse($reports as $leasingName => $items)
                <tr class="leasing-row">
                    <td colspan="6">{{ $leasingName }}</td>
                </tr>
                @php 
                    $subUm = 0;
                    $subUnit = 0;
                @endphp
                @foreach($items as $row)
                    @php
                        $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                        $nominalUm = $isCash ? $row->harga_otr : $row->uang_muka;
                    @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td class="text-center font-bold uppercase">{{ $row->nama_pemohon }}</td>
                        <td class="text-center uppercase">{{ $row->nama_stnk }}</td>
                        <td class="text-center font-bold">{{ $row->motorUnit->no_kunci ?? '-' }}</td>
                        <td class="text-center">{{ $row->motorUnit->type->nama_type ?? '-' }}</td>
                        <td class="text-center">{{ number_format($nominalUm, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $subUm += $nominalUm;
                        $subUnit += 1;
                    @endphp
                @endforeach
                <tr class="sub-total">
                    <td colspan="3" class="text-left uppercase">Total : {{ $subUnit }} Unit</td>
                    <td colspan="2" class="text-right uppercase">Sub Total Uang Muka {{ $leasingName }} :</td>
                    <td class="text-right">{{ number_format($subUm, 0, ',', '.') }}</td>
                </tr>
                @php
                    $grandUm += $subUm;
                    $grandUnit += $subUnit;
                @endphp
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        @if($reports->count() > 0)
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-left uppercase">Total Keseluruhan : {{ $grandUnit }} Unit</td>
                <td colspan="2" class="text-right uppercase">Grand Total :</td>
                <td class="text-right">{{ number_format($grandUm, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>