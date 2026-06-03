<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penagihan Leasing - {{ $penagihan->no_bukti }}</title>
    <style>
        /* Menggunakan format kertas Landscape agar muat banyak kolom */
        @page { size: landscape; margin: 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 10px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header-logo { width: 30%; vertical-align: middle; }
        .header-logo img { max-height: 60px; width: auto; }

        .header-title { width: 40%; text-align: center; vertical-align: middle; }
        .header-title h1 { font-size: 16px; margin: 0; text-transform: uppercase; text-decoration: underline; letter-spacing: 1px; }

        .header-meta { width: 30%; vertical-align: top; }
        .header-meta table { width: 100%; font-size: 11px; border-collapse: collapse; }
        .header-meta td { padding: 2px 0; border: none; vertical-align: top; }

        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 5px 4px; vertical-align: middle; }
        .content-table th { background-color: #f0f0f0; text-align: center; font-size: 10px; font-weight: bold; text-transform: uppercase; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .signature-container { width: 100%; margin-top: 30px; display: table; text-align: center; font-size: 11px; }
        .signature-row { display: table-row; }
        .signature-cell { display: table-cell; width: 33.33%; vertical-align: top; }
        .signature-space { height: 70px; }
    </style>
</head>
<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
            </td>
            <td class="header-title">
                <h1>Tanda Terima Tagihan Leasing</h1>
            </td>
            <td class="header-meta">
                <table>
                    <tr>
                        <td style="width: 80px;">No. Bukti</td>
                        <td style="width: 10px;">:</td>
                        <td class="font-bold">{{ $penagihan->no_bukti }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($penagihan->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Kepada Leasing</td>
                        <td>:</td>
                        <td class="font-bold uppercase">{{ $penagihan->leasing->nama_leasing ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 5%;">Apprv</th>
                <th style="width: 8%;">No. SJ</th>
                <th style="width: 6%;">Tgl SJ</th>
                <th style="width: 6%;">Kunci</th>
                <th style="width: 12%;">Nama STNK</th>
                <th style="width: 16%;">Alamat</th>
                <th style="width: 10%;">Tipe Motor</th>
                <th style="width: 8%;">No. Mesin</th>
                <th style="width: 8%;">No. Rangka</th>
                <th style="width: 7%;">OTR</th>
                <th style="width: 7%;">DP PO</th>
                <th style="width: 8%;">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOtr = 0;
                $totalDp = 0;
                $totalSisa = 0;
            @endphp

            @foreach($penagihan->details as $index => $detail)
                @php
                    $totalOtr += $detail->otr;
                    $totalDp += $detail->dp_po;
                    $totalSisa += $detail->sisa;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold" style="font-size: 12px;">&#10003;</td>
                    <td class="text-center font-bold">{{ $detail->suratJalan->no_bukti ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($detail->suratJalan->tanggal ?? now())->format('d/m/y') }}</td>
                    <td class="text-center font-bold">{{ $detail->suratJalan->motorUnit->no_kunci ?? '-' }}</td>
                    <td class="uppercase font-bold">{{ $detail->suratJalan->spk->nama_stnk ?? '-' }}</td>
                    <td class="uppercase" style="font-size: 9px;">{{ $detail->suratJalan->spk->alamat ?? '-' }}</td>
                    <td class="uppercase">{{ $detail->suratJalan->motorUnit->type->nama_type ?? '-' }}</td>
                    <td class="uppercase">{{ $detail->suratJalan->motorUnit->no_mesin ?? '-' }}</td>
                    <td class="uppercase">{{ $detail->suratJalan->motorUnit->no_rangka ?? '-' }}</td>
                    <td class="text-right">{{ number_format($detail->otr, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->dp_po, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($detail->sisa, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right" style="padding-right: 15px; font-size: 11px;">TOTAL KESELURUHAN</th>
                <th class="text-right font-bold" style="font-size: 11px;">{{ number_format($totalOtr, 0, ',', '.') }}</th>
                <th class="text-right font-bold" style="font-size: 11px;">{{ number_format($totalDp, 0, ',', '.') }}</th>
                <th class="text-right font-bold" style="font-size: 11px;">{{ number_format($totalSisa, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="signature-container">
        <div class="signature-row">
            <div class="signature-cell">
                Dibuat Oleh,<br>
                <div class="signature-space"></div>
                ( ........................................ )
            </div>
            <div class="signature-cell">
                Diperiksa Oleh,<br>
                <div class="signature-space"></div>
                ( ........................................ )
            </div>
            <div class="signature-cell">
                Diterima Oleh (Pihak Leasing),<br>
                <div class="signature-space"></div>
                ( ........................................ )
            </div>
        </div>
    </div>

</body>
</html>
