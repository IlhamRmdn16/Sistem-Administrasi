<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penagihan Leasing - {{ $penagihan->no_bukti }}</title>
    <style>
        /* Ukuran 1/2 F4 Landscape */
        @page { size: 215mm 165mm landscape; margin: 10mm 15mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.4; }

        .title-main { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; text-align: left; }

        .meta-table { font-size: 11px; margin-bottom: 15px; border-collapse: collapse; }
        .meta-table td { padding: 2px 10px 2px 0; vertical-align: top; }

        /* Tabel Data: Border luar saja, tanpa garis pembatas vertikal antar nama kolom */
        .data-table { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px; }

        /* Garis bawah pada judul kolom */
        .data-table th {
            border-bottom: 1px solid #000;
            padding: 6px 5px;
            font-weight: bold;
        }

        /* Isi tabel tanpa border sama sekali */
        .data-table td {
            padding: 5px;
            border: none;
            vertical-align: top;
        }

        /* Garis atas pada footer tabel untuk memisahkan total */
        .data-table tfoot th {
            border-top: 1px solid #000;
            padding: 6px 5px;
            font-weight: bold;
        }

        /* Pengunci Aligntment agar tidak berubah */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }

        .uppercase { text-transform: uppercase; }

    </style>
</head>
<body onload="window.print()">

    <div class="title-main">Penagihan Leasing</div>

    <table class="meta-table">
        <tr>
            <td>No. Penagihan</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $penagihan->no_bukti }}</td>
        </tr>
        <tr>
            <td>Tgl. Penagihan</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($penagihan->tanggal)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Leasing</td>
            <td>:</td>
            <td class="uppercase" style="font-weight: bold;">{{ $penagihan->leasing->nama_leasing ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 12%;" class="text-left">No. Kunci</th>
                <th style="width: 10%;" class="text-left">Tanggal</th>
                <th style="width: 25%;" class="text-left">Nama STNK</th>
                <th style="width: 18%;" class="text-left">Tipe Motor</th>
                <th style="width: 15%;" class="text-right">DP PO</th>
                <th style="width: 15%;" class="text-right">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDp = 0;
                $totalSisa = 0;
            @endphp

            @foreach($penagihan->details as $index => $detail)
                @php
                    $totalDp += $detail->dp_po;
                    $totalSisa += $detail->sisa;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left" style="font-weight: bold;">{{ $detail->suratJalan->motorUnit->no_kunci ?? '-' }}</td>
                    <td class="text-left">{{ \Carbon\Carbon::parse($detail->suratJalan->tanggal ?? now())->format('d/m/Y') }}</td>
                    <td class="text-left uppercase" style="font-weight: bold;">{{ $detail->suratJalan->spk->nama_stnk ?? '-' }}</td>
                    <td class="text-left uppercase">{{ $detail->suratJalan->motorUnit->type->nama_type ?? '-' }}</td>
                    <td class="text-right">{{ number_format($detail->dp_po, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($detail->sisa, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right" style="padding-right: 15px;">TOTAL:</th>
                <th class="text-right">{{ number_format($totalDp, 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
