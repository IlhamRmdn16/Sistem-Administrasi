<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penyerahan BPKB - {{ $document->no_bukti }}</title>
    <style>
        @page { size: 215mm 165mm landscape; margin: 8mm 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        .header-table { width: 100%; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 15px; }
        .header-logo { width: 75%; vertical-align: middle; }
        .header-logo img { max-height: 70px; width: auto; }

        .header-meta { width: 25%; vertical-align: top; }
        .header-meta .title { text-transform: uppercase; text-decoration: underline; margin-bottom: 8px; }
        .header-meta table { width: 100%; border-collapse: collapse; }
        .header-meta td { padding: 1px 0; border: none; vertical-align: top; }

        .data-table { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 15px; }
        .data-table thead th { border: 1px solid #000; padding: 6px 4px; background-color: #f0f0f0; text-align: center; }

        .data-table tbody td { border: none; padding: 4px 6px; vertical-align: top; }
        .data-table tbody td.col-no { border-right: 1px solid #000; border-left: 1px solid #000; text-align: center; }
        .data-table tbody { border-bottom: 1px solid #000; }

        tr { page-break-inside: avoid; }

        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }

        .signature-area { width: 100%; margin-top: 30px; display: table; page-break-inside: avoid; }
        .signature-box { display: table-cell; width: 33.33%; text-align: center; vertical-align: bottom; }
        .signature-space { height: 70px; }
    </style>
</head>
<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
            </td>
            <td class="header-meta">
                <div class="title">Penyerahan BPKB Ke Leasing</div>
                <table>
                    <tr>
                        <td style="width: 70px;">No. Bukti</td>
                        <td style="width: 10px;">:</td>
                        <td>{{ $document->no_bukti }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($document->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Leasing</td>
                        <td>:</td>
                        <td class="uppercase">{{ $document->leasing->nama_leasing }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama STNK</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 15%;">Tipe Motor</th>
                <th style="width: 15%;">No. Mesin</th>
                <th style="width: 10%;">No. Polisi</th>
                <th style="width: 10%;">No. BPKB</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document->details as $index => $detail)
            @php
                $spk = $detail->suratJalan->spk;
                $samsat = $detail->suratJalan->samsat;
                $motor = $detail->suratJalan->motorUnit;
            @endphp
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="uppercase">{{ $spk->nama_stnk }}</td>
                <td>{{ $spk->alamat }}</td>
                <td>{{ $motor->type->nama_type ?? '-' }}</td>
                <td>{{ $motor->no_mesin ?? '-' }}</td>
                <td class="text-center">{{ $samsat->no_polisi ?? '-' }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $samsat->no_bpkb }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            Diterima Oleh,<br>
            <div class="signature-space"></div>
            (...........................................)
        </div>
        <div class="signature-box">
            Hormat Kami,<br>
            <div class="signature-space"></div>
            (...........................................)
        </div>
        <div class="signature-box">
            Surya Wijaya<br>
            <div class="signature-space"></div>
            (...........................................)
        </div>
    </div>

</body>
</html>
