<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Pengajuan STNK - {{ $pengajuan->no_bukti }}</title>

    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 10px;
        color: #000;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    .header-table {
        margin-bottom: 18px;
    }

    .header-table td {
        vertical-align: top;
    }

    .data-table {
        border: 1px solid #000;
        margin-bottom: 16px;
    }

    .data-table th {
        border-bottom: 1px solid #000;
        padding: 6px 5px;
        text-align: center;
        font-size: 12px;
        font-weight: normal;
    }

    .data-table td {
        padding: 5px;
        font-size: 12px;
        vertical-align: middle;
    }

    .total-row td {
        border-top: 1px solid #000;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .signature-wrapper {
        width: 100%;
        margin-top: 40px;
    }

    .signature-box {
        width: 220px;
        margin-left: auto;
        text-align: center;
        font-size: 12px;
    }

    .sig-space {
        height: 70px;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm;
        }

        body {
            padding: 0;
        }
    }
</style>
</head>

<body onload="window.print()">

    <table class="header-table" style="width: 100%;">
    <tr>
        <td style="width: 65%; vertical-align: top;">
            <img 
                src="{{ asset('images/spk/logo.jpeg') }}"
                style="width: 100%; height: auto; display: block;"
            >
        </td>

        <td style="width: 35%; vertical-align: top; padding-left: 14px;">
            <div style="font-size: 12px; margin-bottom: 8px; line-height: 1.4;">
                Surat Pengantar Pengajuan STNK
            </div>

            <table style="width: 100%; font-size: 12px;">
                <tr>
                    <td style="width: 95px; padding: 1px 0;">
                        No. Pengajuan
                    </td>

                    <td style="width: 10px; padding: 1px 4px;">
                        :
                    </td>

                    <td style="padding: 1px 0;">
                        {{ $pengajuan->no_bukti }}
                    </td>
                </tr>

                <tr>
                    <td style="padding: 1px 0;">
                        Tgl Pengajuan
                    </td>

                    <td style="padding: 1px 4px;">
                        :
                    </td>

                    <td style="padding: 1px 0;">
                        {{ \Carbon\Carbon::parse($pengajuan->tanggal)->format('d/m/Y') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

    <table class="data-table">
    <thead>
        <tr>
            <th width="30">No</th>
            <th>Nama STNK</th>
            <th>Alamat</th>
            <th width="110">Tipe Motor</th>
            <th width="100">No. Mesin</th>
            <th width="90">Notice Pajak</th>
            <th width="75">ADM</th>
            <th width="90">Sub Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($pengajuan->details as $index => $d)
            <tr>
                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td class="text-center">
                    {{ strtoupper($d->samsat->suratJalan->spk->nama_stnk) }}
                </td>

                <td class="text-center">
                    {{ $d->samsat->suratJalan->spk->alamat }}
                </td>

                <td class="text-center">
                    {{ $d->samsat->suratJalan->spk->motorType->nama_type ?? '-' }}
                </td>

                <td class="text-center">
                    {{ $d->samsat->suratJalan->motorUnit->no_mesin ?? '-' }}
                </td>

                <td class="text-center">
                    {{ number_format($d->notice_pajak, 0, ',', '.') }}
                </td>

                <td class="text-center">
                    {{ number_format($d->adm, 0, ',', '.') }}
                </td>

                <td class="text-center">
                    {{ number_format($d->sub_total, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="5" class="text-right" style="padding-right: 10px;">
    Total Notice Pajak dan ADM :
</td>

            <td class="text-center">
                {{ number_format($pengajuan->total_pajak, 0, ',', '.') }}
            </td>

            <td class="text-center">
                {{ number_format($pengajuan->total_adm, 0, ',', '.') }}
            </td>

            <td class="text-center">
                {{ number_format($pengajuan->total_pajak + $pengajuan->total_adm, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>

    @if($pengajuan->tambahans->count() > 0)

        <table class="data-table" style="width: 60%; margin-left: auto;">
            <thead>
                <tr>
                    <th colspan="3">
                        Biaya Tambahan
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($pengajuan->tambahans as $t)
                    <tr>
                        <td>
                            {{ $t->keterangan }}
                        </td>

                        <td class="text-center">
                            {{ number_format($t->nominal, 0, ',', '.') }} x {{ $pengajuan->details->count() }} unit
                        </td>

                        <td class="text-right">
                            {{ number_format($t->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="2" class="text-right">
                        Total Tambahan
                    </td>

                    <td class="text-right">
                        {{ number_format($pengajuan->total_tambahan, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="data-table" style="width: 45%; margin-left: auto;">
            <tr>
                <td width="50%">
                    Grand Total
                </td>

                <td class="text-right">
                    Rp {{ number_format($pengajuan->grand_total, 0, ',', '.') }}
                </td>
            </tr>
        </table>

    @endif

    <div class="signature-wrapper">
        <div class="signature-box">
            Penerima,

            <div class="sig-space"></div>

            (____________________)
        </div>
    </div>

</body>
</html>