<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan Sales POP Terperinci</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 14px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; border: 1px solid #000; }
        th, td { border: none; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 7.5px; padding: 6px 4px; border-bottom: 1px solid #000; }
        td { padding: 4px 4px; font-size: 8px; }
        tr.sales-row td { font-weight: bold; text-transform: uppercase; font-size: 9px; padding-top: 12px; padding-bottom: 6px; color: #222; }
        tr.sub-total td { border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #f9f9f9; font-weight: bold; padding: 6px 4px; }
        tr.grand-total td { border-top: 2px solid #000; background-color: #e2e2e2; font-weight: bold; padding: 8px 4px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Penjualan Sales / POP - Terperinci</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Tipe</th>
                <th>Nama Tipe Motor</th>
                <th>No. Bukti SJK</th>
                <th>Tgl Bukti</th>
                <th>Nama Pemohon</th>
                <th>Leasing</th>
                <th>Mediator</th>
                <th>MD Fee</th>
                <th>Discount</th>
                <th>Unit</th>
                <th>Total OTR</th>
                <th>Uang Muka</th>
                <th>Tanda Jadi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $gUnit = 0; $gOtr = 0; $gUm = 0; $gTj = 0; $gMd = 0; $gDisc = 0;
            @endphp
            @forelse($reports as $salesName => $items)
                <tr class="sales-row"><td colspan="13">{{ $salesName }}</td></tr>
                @php 
                    $sUnit = 0; $sOtr = 0; $sUm = 0; $sTj = 0; $sMd = 0; $sDisc = 0;
                @endphp
                @foreach($items as $row)
                    @php
                        $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                        $umTj = $isCash ? $row->harga_otr : $row->uang_muka;
                        $mdFee = $row->kontrolHarga->mediator_fee ?? 0;
                        $discount = $row->kontrolHarga->discount ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center font-bold">{{ $row->motorUnit->type->kode_tipe ?? '-' }}</td>
                        <td>{{ substr($row->motorUnit->type->nama_type ?? '-', 0, 15) }}</td>
                        <td class="text-center">{{ $row->suratJalan->no_bukti ?? '-' }}</td>
                        <td class="text-center">{{ $row->suratJalan ? \Carbon\Carbon::parse($row->suratJalan->tanggal)->format('d/m/y') : '-' }}</td>
                        <td class="font-bold uppercase">{{ substr($row->nama_pemohon, 0, 15) }}</td>
                        <td class="text-center">{{ $isCash ? 'CASH' : substr($row->leasing->nama_leasing ?? '-', 0, 10) }}</td>
                        <td>{{ substr($row->kontrolHarga->nama_mediator ?? '-', 0, 10) }}</td>
                        <td class="text-right">{{ number_format($mdFee, 0, '', '.') }}</td>
                        <td class="text-right" style="color:red;">{{ number_format($discount, 0, '', '.') }}</td>
                        <td class="text-center font-bold">1</td>
                        <td class="text-right">{{ number_format($row->harga_otr, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($umTj, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($umTj, 0, '', '.') }}</td>
                    </tr>
                    @php
                        $sUnit += 1; $sOtr += $row->harga_otr; $sUm += $umTj; $sTj += $umTj; $sMd += $mdFee; $sDisc += $discount;
                    @endphp
                @endforeach
                <tr class="sub-total">
                    <td colspan="7" class="text-right uppercase">Sub Total {{ $salesName }} :</td>
                    <td class="text-right">{{ number_format($sMd, 0, '', '.') }}</td>
                    <td class="text-right" style="color:red;">{{ number_format($sDisc, 0, '', '.') }}</td>
                    <td class="text-center">{{ $sUnit }}</td>
                    <td class="text-right">{{ number_format($sOtr, 0, '', '.') }}</td>
                    <td class="text-right">{{ number_format($sUm, 0, '', '.') }}</td>
                    <td class="text-right">{{ number_format($sTj, 0, '', '.') }}</td>
                </tr>
                @php
                    $gUnit += $sUnit; $gOtr += $sOtr; $gUm += $sUm; $gTj += $sTj; $gMd += $sMd; $gDisc += $sDisc;
                @endphp
            @empty
                <tr><td colspan="13" class="text-center">Tidak ada data penjualan pada rentang periode ini.</td></tr>
            @endforelse
        </tbody>
        @if($reports->count() > 0)
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="text-right uppercase">Grand Total :</td>
                <td class="text-right">{{ number_format($gMd, 0, '', '.') }}</td>
                <td class="text-right" style="color:red;">{{ number_format($gDisc, 0, '', '.') }}</td>
                <td class="text-center">{{ $gUnit }}</td>
                <td class="text-right">{{ number_format($gOtr, 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($gUm, 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($gTj, 0, '', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>