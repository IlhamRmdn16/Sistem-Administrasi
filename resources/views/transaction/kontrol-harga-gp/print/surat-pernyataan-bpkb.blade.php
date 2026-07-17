<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan BPKB - {{ $spk->nama_stnk }}</title>
    <style>
        @page { size: 215mm 165mm landscape; margin: 6mm 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        .title { text-align: center; text-decoration: underline; font-weight: bold; font-size: 13px; margin-bottom: 12px; text-transform: uppercase; }

        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .content-table td { padding: 2px 5px; vertical-align: top; border: none; }
        .content-table td.label-col { width: 150px; }
        .content-table td.colon-col { width: 10px; }

        .uppercase { text-transform: uppercase; }

        .paragraph { margin-bottom: 6px; text-align: justify; }

        .signature-area { margin-top: 20px; text-align: right; width: 100%; }
        .signature-box { display: inline-block; text-align: center; width: 220px; }
        .signature-space { height: 50px; }
    </style>
</head>
<body onload="window.print()">

    @php
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $now = \Carbon\Carbon::now();
        $tanggalIndo = $now->format('d') . ' ' . $bulanIndo[$now->month] . ' ' . $now->format('Y');
    @endphp

    <div class="title">SURAT PERNYATAAN BPKB</div>

    <div class="paragraph">Yang bertanda tangan di bawah ini:</div>

    <table class="content-table" style="margin-left: 15px;">
        <tr>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td>TJENDAKA SURYAWIDJAYA</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="colon-col">:</td>
            <td>DIREKTUR CV. SURYA WIJAYA SEJAHTERA</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td>JL. PAPANDAYAN NO 112 GARUT</td>
        </tr>
        <tr>
            <td class="label-col">Telepon</td>
            <td class="colon-col">:</td>
            <td>(0262) 231236 - 231370</td>
        </tr>
    </table>

    <div class="paragraph" style="margin-top: 4px;">Menyatakan Kendaraan dengan data di bawah ini:</div>

    <table class="content-table" style="margin-left: 15px;">
        <tr>
            <td class="label-col">Atas nama</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->nama_stnk }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->alamat }}</td>
        </tr>
        <tr>
            <td class="label-col">RT / RW</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->rt_rw ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Desa / Kelurahan</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->desa_kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kecamatan</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kabupaten / Kota</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->kota_kabupaten ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Merk / Type</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->motorUnit->type->kode_motor ?? '-' }} ({{ $spk->motorUnit->type->nama_type ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label-col">No. Rangka</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->motorUnit->no_rangka ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">No. Mesin</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->motorUnit->no_mesin ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Warna</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->motorUnit->color->warna ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tahun</td>
            <td class="colon-col">:</td>
            <td>{{ $spk->motorUnit->tahun_pembuatan ?? '-' }}</td>
        </tr>
    </table>

    <div class="paragraph" style="margin-top: 6px;">
        Akan kami serahkan BPKB beserta Faktur Kendaraan tersebut diatas selambat-lambatnya 90 (sembilan puluh) hari dari tanggal STNK kendaraan tersebut kepada : &nbsp; <b>{{ strtoupper($spk->leasing->nama_leasing ?? '-') }}</b>
    </div>

    <div class="paragraph">
        Demikian Surat Pernyataan ini kami buat untuk dipergunakan seperlunya.
    </div>

    <div class="signature-area">
        <div class="signature-box">
            Garut, {{ $tanggalIndo }}<br>
            <div class="signature-space"></div>
            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
        </div>
    </div>
</body>
</html>