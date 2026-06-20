<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Subsidi Main Dealer</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0 0 3px 0; font-size: 13px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px 3px; font-size: 7.5px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; }
        tfoot tr td { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        .bg-light { background-color: #fafafa; }
        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Kontrol Subsidi Main Dealer</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="10">TERIMA (KONTROL HARGA)</th>
                <th colspan="3">TERIMA (KUITANSI)</th>
                <th rowspan="2">MD FEE</th>
                <th rowspan="2">SETOR</th>
                <th rowspan="2">TAMBAH</th>
                <th rowspan="2">LEASING</th>
                <th rowspan="2">AHM</th>
                <th colspan="4">SUBSIDI</th>
                <th colspan="2">TANGGAL</th>
                <th colspan="2">NAMA</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Nama Pemohon</th>
                <th>Tipe Motor</th>
                <th>No. Kunci</th>
                <th>Harga Cash</th>
                <th>DP</th>
                <th>Discount</th>
                <th>DP Murni</th>
                <th>Sisa</th>
                <th>Refund</th>
                <th>Kontan</th>
                <th>Transfer</th>
                <th>Bank</th>
                <th>Main Dealer</th>
                <th>Leasing</th>
                <th>DLL</th>
                <th>Dealer</th>
                <th>SPK</th>
                <th>SJK</th>
                <th>Sales</th>
                <th>Mediator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $row)
                @php
                    $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                    $hargaCash = $isCash ? $row->harga_otr : 0;
                    $dpKredit = !$isCash ? ($row->uang_muka + $row->tanda_jadi) : 0;
                    $discount = $row->kontrolHarga->discount ?? 0;
                    $dpMurni = $isCash ? ($hargaCash - $discount) : ($dpKredit - $discount);
                    $kontan = $row->kuitansiKonsumens->sum('bayar_kontan');
                    $transfer = $row->kuitansiKonsumens->sum('bayar_transfer');
                    $sisaTagihan = $dpMurni - ($kontan + $transfer);
                    $bankNames = $row->kuitansiKonsumens->where('bayar_transfer', '>', 0)->map(function($k) {
                        return $k->rekening ? $k->rekening->nama_rekening : '';
                    })->filter()->unique()->implode(', ');
                    $subsidiLeasing = ($row->kontrolHarga->subsidi_leasing_1 ?? 0) + ($row->kontrolHarga->subsidi_leasing_2 ?? 0);
                    $subsidiDll = ($row->kontrolHarga->dll_1 ?? 0) + ($row->kontrolHarga->dll_2 ?? 0);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold uppercase">{{ substr($row->nama_pemohon, 0, 15) }}</td>
                    <td>{{ substr($row->motorUnit->type->nama_type ?? '-', 0, 15) }}</td>
                    <td class="text-center font-mono">{{ $row->motorUnit->no_kunci ?? '-' }}</td>
                    <td class="text-right">{{ $hargaCash > 0 ? number_format($hargaCash, 0, '', '.') : '0' }}</td>
                    <td class="text-right">{{ $dpKredit > 0 ? number_format($dpKredit, 0, '', '.') : '0' }}</td>
                    <td class="text-right" style="color: red;">{{ $discount > 0 ? number_format($discount, 0, '', '.') : '0' }}</td>
                    <td class="text-right font-bold bg-light">{{ number_format($dpMurni, 0, '', '.') }}</td>
                    <td class="text-right">{{ $sisaTagihan > 0 ? number_format($sisaTagihan, 0, '', '.') : '0' }}</td>
                    <td class="text-right">{{ ($row->kontrolHarga->refund_transfer ?? 0) > 0 ? number_format($row->kontrolHarga->refund_transfer, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ $kontan > 0 ? number_format($kontan, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ $transfer > 0 ? number_format($transfer, 0, '', '.') : '0' }}</td>
                    <td class="font-mono" style="font-size: 6.5px;">{{ substr($bankNames, 0, 12) }}</td>
                    <td class="text-right">{{ ($row->kontrolHarga->mediator_fee ?? 0) > 0 ? number_format($row->kontrolHarga->mediator_fee, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ $kontan > 0 ? number_format($kontan, 0, '', '.') : '0' }}</td>
                    <td class="text-right">{{ ($row->kontrolHarga->tambahan ?? 0) > 0 ? number_format($row->kontrolHarga->tambahan, 0, '', '.') : '0' }}</td>
                    <td class="text-center uppercase" style="font-size:6.5px;">{{ $isCash ? 'Cash' : substr($row->leasing->nama_leasing ?? 'Kredit', 0, 8) }}</td>
                    <td class="text-right">{{ ($row->kontrolHarga->subsidi_ahm ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_ahm, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ ($row->kontrolHarga->subsidi_main_dealer ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_main_dealer, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ $subsidiLeasing > 0 ? number_format($subsidiLeasing, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ $subsidiDll > 0 ? number_format($subsidiDll, 0, '', '.') : '0' }}</td>
                    <td class="text-right bg-light">{{ ($row->kontrolHarga->subsidi_dealer ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_dealer, 0, '', '.') : '0' }}</td>
                    <td class="text-center font-mono">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/y') }}</td>
                    <td class="text-center font-mono">{{ $row->suratJalan ? \Carbon\Carbon::parse($row->suratJalan->tanggal)->format('d/m/y') : '-' }}</td>
                    <td class="uppercase" style="font-size: 6.5px;">{{ substr($row->sales->nama_sales ?? '-', 0, 10) }}</td>
                    <td class="uppercase" style="font-size: 6.5px;">{{ substr($row->kontrolHarga->nama_mediator ?? '-', 0, 10) }}</td>
                </tr>
            @empty
                <tr><td colspan="26" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        @if($reports->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($grandTotals['harga_cash'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['dp'], 0, '', '.') }}</td>
                <td class="text-right" style="color:red;">{{ number_format($grandTotals['discount'], 0, '', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($grandTotals['dp_murni'], 0, '', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-right">{{ number_format($grandTotals['kontan'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['transfer'], 0, '', '.') }}</td>
                <td></td>
                <td class="text-right">{{ number_format($grandTotals['md_fee'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['setor'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['tambah'], 0, '', '.') }}</td>
                <td></td>
                <td class="text-right">{{ number_format($grandTotals['ahm'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['mdealer'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['leasing'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['dll'], 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotals['dealer'], 0, '', '.') }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
