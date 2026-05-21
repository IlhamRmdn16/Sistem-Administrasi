<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Dokumen - {{ $sjk->no_bukti }}</title>
    <style>
        /* Ukuran 1/2 F4 Landscape: 21.5cm Lebar x 16.5cm Tinggi */
        @page {
            size: 21.5cm 16.5cm;
            margin: 5mm 8mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .header h3 { margin: 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 1px 0 0 0; font-size: 9px; color: #333; }

        /* Pembagian Area Vertikal Atas & Bawah */
        .section-stnk {
            height: 58mm; /* Batas tinggi ketat agar pas setengah halaman atas */
            box-sizing: border-box;
            padding-bottom: 2mm;
        }

        .section-bpkb {
            height: 58mm; /* Batas tinggi ketat agar pas setengah halaman bawah */
            box-sizing: border-box;
            padding-top: 2mm;
        }

        .box-title {
            font-weight: bold;
            font-size: 10px;
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 3px 6px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Layout Fleksibel untuk Info Tabel & TTD */
        .grid-container {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .col-info {
            display: table-cell;
            vertical-align: top;
            width: 65%;
        }
        .col-signature {
            display: table-cell;
            vertical-align: bottom;
            width: 35%;
            text-align: right;
        }

        table.info-table { width: 100%; border-collapse: collapse; }
        table.info-table td { padding: 2px 0; vertical-align: top; }

        .ttd-box {
            display: inline-block;
            text-align: center;
            width: 160px;
            font-size: 11px;
        }
        .ttd-space { height: 38px; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h3>TANDA TERIMA DOKUMEN KENDARAAN</h3>
        <p>DEALER AHASS SURYA WIJAYA - GARUT</p>
    </div>

    <div class="section-stnk">
        <div class="box-title">BUKTI PENYERAHAN STNK & PLAT NOMOR</div>

        @if($sjk->penyerahanStnkBpkb && $sjk->penyerahanStnkBpkb->tgl_serah_stnk)
            <div class="grid-container">
                <div class="col-info">
                    <table class="info-table">
                        <tr><td width="90">No. Polisi / Plat</td><td width="10">:</td><td style="font-weight:bold;">{{ $sjk->samsat->no_polisi ?? '-' }}</td></tr>
                        <tr><td>No. Mesin</td><td>:</td><td>{{ $sjk->motorUnit->no_mesin ?? '-' }}</td></tr>
                        <tr><td>Nama di STNK</td><td>:</td><td style="font-weight:bold;">{{ strtoupper($sjk->spk->nama_stnk ?? '-') }}</td></tr>
                        <tr><td>Tgl. Serah Terima</td><td>:</td><td>{{ \Carbon\Carbon::parse($sjk->penyerahanStnkBpkb->tgl_serah_stnk)->format('d/m/Y') }}</td></tr>
                        <tr><td>Nama Penerima</td><td>:</td><td>{{ strtoupper($sjk->penyerahanStnkBpkb->penerima_stnk) }}</td></tr>
                        <tr><td>Alamat Penerima</td><td>:</td><td>{{ $sjk->penyerahanStnkBpkb->alamat_penerima_stnk ?? '-' }}</td></tr>
                        <tr><td>Keterangan / Hub.</td><td>:</td><td>{{ $sjk->penyerahanStnkBpkb->keterangan_stnk ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col-signature">
                    <div class="ttd-box">
                        Penerima STNK,<br>
                        <div class="ttd-space"></div>
                        ( {{ strtoupper($sjk->penyerahanStnkBpkb->penerima_stnk) }} )
                    </div>
                </div>
            </div>
        @else
            <div style="color: #999; font-style: italic; padding: 10px 0;">[ Belum diserahkan / Belum diinput ]</div>
        @endif
    </div>

    <div style="border-top: 1px dashed #bbb; margin: 2px 0;"></div>

    <div class="section-bpkb">
        <div class="box-title">BUKTI PENYERAHAN BPKB</div>

        @if($sjk->penyerahanStnkBpkb && $sjk->penyerahanStnkBpkb->tgl_serah_bpkb)
            <div class="grid-container">
                <div class="col-info">
                    <table class="info-table">
                        <tr><td width="90">No. BPKB</td><td width="10">:</td><td style="font-weight:bold;">{{ $sjk->samsat->no_bpkb ?? '-' }}</td></tr>
                        <tr><td>No. Rangka</td><td>:</td><td>{{ $sjk->motorUnit->no_rangka ?? '-' }}</td></tr>
                        <tr><td>Nama di STNK</td><td>:</td><td style="font-weight:bold;">{{ strtoupper($sjk->spk->nama_stnk ?? '-') }}</td></tr>
                        <tr><td>Tgl. Serah Terima</td><td>:</td><td>{{ \Carbon\Carbon::parse($sjk->penyerahanStnkBpkb->tgl_serah_bpkb)->format('d/m/Y') }}</td></tr>
                        <tr><td>Nama Penerima</td><td>:</td><td>{{ strtoupper($sjk->penyerahanStnkBpkb->penerima_bpkb) }}</td></tr>
                        <tr><td>Alamat Penerima</td><td>:</td><td>{{ $sjk->penyerahanStnkBpkb->alamat_penerima_bpkb ?? '-' }}</td></tr>
                        <tr><td>Keterangan / Hub.</td><td>:</td><td>{{ $sjk->penyerahanStnkBpkb->keterangan_bpkb ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col-signature">
                    <div class="ttd-box">
                        Penerima BPKB,<br>
                        <div class="ttd-space"></div>
                        ( {{ strtoupper($sjk->penyerahanStnkBpkb->penerima_bpkb) }} )
                    </div>
                </div>
            </div>
        @else
            <div style="color: #999; font-style: italic; padding: 10px 0;">[ Belum diserahkan / Belum diinput ]</div>
        @endif
    </div>

</body>
</html>
