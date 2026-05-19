<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Pengajuan STNK - {{ $pengajuan->no_bukti }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .meta-data { width: 100%; margin-bottom: 20px; }
        .meta-data td { padding: 3px 0; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        table.data-table th { background-color: #f0f0f0; text-align: center; font-size: 11px;}
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold; }
        .signature { width: 100%; margin-top: 50px; }
        .signature td { width: 33%; text-align: center; }
        .sig-box { height: 80px; }
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div class="title">DAFTAR PENGAJUAN STNK & BPKB</div>
        <div>DEALER AHASS SURYA WIJAYA</div>
    </div>

    <table class="meta-data">
        <tr>
            <td width="120" class="font-bold">NO. BUKTI</td>
            <td width="10">:</td>
            <td>{{ $pengajuan->no_bukti }}</td>
            <td width="120" class="font-bold text-right">TANGGAL</td>
            <td width="10">:</td>
            <td width="100">{{ \Carbon\Carbon::parse($pengajuan->tanggal)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA STNK</th>
                <th>ALAMAT</th>
                <th>TIPE / MESIN</th>
                <th width="80">PAJAK</th>
                <th width="80">ADM</th>
                <th width="80">SUB TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuan->details as $index => $d)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold uppercase">{{ $d->samsat->suratJalan->spk->nama_stnk }}</td>
                    <td>
                        {{ $d->samsat->suratJalan->spk->alamat }}
                        @if($d->samsat->suratJalan->spk->rt_rw) RT/RW {{ $d->samsat->suratJalan->spk->rt_rw }} @endif
                    </td>
                    <td>
                        {{ $d->samsat->suratJalan->spk->motorType->nama_type ?? '-' }}<br>
                        {{ $d->samsat->suratJalan->motorUnit->no_mesin ?? '-' }}
                    </td>
                    <td class="text-right">{{ number_format($d->notice_pajak, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($d->adm, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($d->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-right font-bold">TOTAL DOKUMEN :</td>
                <td class="text-right font-bold">{{ number_format($pengajuan->total_pajak, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($pengajuan->total_adm, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($pengajuan->total_pajak + $pengajuan->total_adm, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($pengajuan->tambahans->count() > 0)
        <table class="data-table" style="width: 60%; margin-left: auto;">
            <thead>
                <tr>
                    <th colspan="3">BIAYA TAMBAHAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengajuan->tambahans as $t)
                    <tr>
                        <td>{{ $t->keterangan }}</td>
                        <td class="text-center">{{ number_format($t->nominal, 0, ',', '.') }} x {{ $pengajuan->details->count() }} unit</td>
                        <td class="text-right">{{ number_format($t->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-right font-bold">TOTAL TAMBAHAN :</td>
                    <td class="text-right font-bold">{{ number_format($pengajuan->total_tambahan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <table class="data-table" style="width: 50%; margin-left: auto;">
        <tr>
            <td class="font-bold" style="font-size: 14px;">GRAND TOTAL</td>
            <td class="text-right font-bold" style="font-size: 14px;">Rp {{ number_format($pengajuan->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="signature">
        <tr>
            <td>Disiapkan Oleh,<br><div class="sig-box"></div>(_________________)</td>
            <td>Biro Jasa / SAMSAT,<br><div class="sig-box"></div>(_________________)</td>
            <td>Diketahui Oleh,<br><div class="sig-box"></div>(_________________)</td>
        </tr>
    </table>

</body>
</html>
