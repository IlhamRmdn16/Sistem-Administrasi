<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Pengajuan STNK - {{ $pengajuan->no_bukti }}</title>

    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        margin: 5mm;
        padding: 0;
        color: #000;
    }

    /* Layout Header */
    .header-section { width: 100%; margin-bottom: 20px; }
    .logo-side { width: auto; }
    .text-side { text-align: right; vertical-align: middle; padding-left: 20px; }

    /* Tabel Styling */
    table.data-table { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px; }
    table.data-table th { border-bottom: 1px solid #000; padding: 6px; text-align: center; font-size: 11px; }
    table.data-table td { border: none; padding: 6px; }
    .total-row { border-top: 1px solid #000; font-weight: bold; }

    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }
    .font-bold { font-weight: bold; }

    /* Signature */
    .signature-wrapper { width: 100%; margin-top: 40px; text-align: right; }
    .sig-box { height: 70px; }

    @media print {
        @page { size: A4 portrait; margin: 10mm; }
    }
    </style>
</head>

<body onload="window.print()">

    <table class="header-section">
        <tr>
            <td class="logo-side">
                <img src="{{ asset('images/spk/logo.jpeg') }}" style="max-height: 80px; width: auto;">
            </td>
            <td class="text-side">
                <div style="font-size: 16px; font-weight: bold;">SURAT PENGANTAR PENGAJUAN STNK</div>
                <div style="margin-top: 5px;">No. Pengajuan : {{ $pengajuan->no_bukti }}</div>
                <div>Tgl Pengajuan : {{ \Carbon\Carbon::parse($pengajuan->tanggal)->format('d/m/Y') }}</div>
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
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold uppercase">{{ $d->suratJalan->spk->nama_stnk }}</td>
                    <td>{{ $d->suratJalan->spk->alamat }}</td>
                    <td>{{ $d->suratJalan->spk->motorType->nama_type ?? '-' }}</td>
                    <td>{{ $d->suratJalan->motorUnit->no_mesin ?? '-' }}</td>
                    <td class="text-center">{{ number_format($d->notice_pajak, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($d->adm, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ number_format($d->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Notice Pajak dan ADM :</td>
                <td class="text-center">{{ number_format($pengajuan->total_pajak, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($pengajuan->total_adm, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($pengajuan->total_pajak + $pengajuan->total_adm, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($pengajuan->tambahans->count() > 0)
        <table class="data-table" style="width: 50%; margin-left: auto;">
            <thead>
                <tr>
                    <th colspan="2">Biaya Tambahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengajuan->tambahans as $t)
                    <tr>
                        <td>{{ $t->keterangan }} ({{ number_format($t->nominal, 0, ',', '.') }} x {{ $pengajuan->details->count() }})</td>
                        <td class="text-right">{{ number_format($t->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-right">Total Tambahan :</td>
                    <td class="text-right">{{ number_format($pengajuan->total_tambahan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <table class="data-table" style="width: 50%; margin-left: auto;">
            <tr>
                <td class="font-bold">Grand Total</td>
                <td class="text-right font-bold">Rp {{ number_format($pengajuan->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
    @endif

    <div class="signature-wrapper">
        <div style="display: inline-block; text-align: center;">
            Penerima,<br>
            <div class="sig-box"></div>
            (_________________)
        </div>
    </div>

</body>
</html>
