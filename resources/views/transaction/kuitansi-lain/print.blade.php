<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI LAIN-LAIN - {{ $kuitansi->no_bukti }}</title>
    <style>
        @page { size: 215mm 165mm landscape; margin: 5mm 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.4; }

        .header-logo { width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; text-align: center; }
        .header-logo img { max-height: 60px; width: auto; }

        .title-container { width: 100%; margin-bottom: 15px; text-align: center; }
        .title-main { font-size: 12px; font-weight: bold; text-decoration: underline; letter-spacing: 2px; text-transform: uppercase; }

        .content-table { width: 100%; margin-bottom: 10px; font-size: 11px; border-collapse: collapse; }
        .content-table td { padding: 4px 6px; border: none; vertical-align: top; }
        .content-table td.label-col { width: 150px; }
        .content-table td.colon-col { width: 10px; }

        .uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }

        .amount-text { font-size: 12px; font-weight: bold; }

        .signature-container { width: 100%; margin-top: 20px; display: table; text-align: center; font-size: 11px; font-weight: bold; }
        .signature-row { display: table-row; }
        .signature-cell { display: table-cell; width: 50%; vertical-align: top; }
        .signature-space { height: 60px; }
    </style>
</head>
<body onload="window.print()">

    @php
        // Format Alamat Memanjang Konsisten
        $alamatLengkap = $kuitansi->alamat .
                         ($kuitansi->rt_rw ? ' RT/RW ' . $kuitansi->rt_rw : '') .
                         ($kuitansi->desa ? ', DESA ' . $kuitansi->desa : '') .
                         ($kuitansi->kecamatan ? ', KEC. ' . $kuitansi->kecamatan : '') .
                         ($kuitansi->kabupaten_kota ? ', KAB. ' . $kuitansi->kabupaten_kota : '');

        // Format Tanggal Indonesia
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tgl_cetak = \Carbon\Carbon::parse($kuitansi->tanggal);
        $tanggalIndo = $tgl_cetak->format('d') . ' ' . $bulanIndo[$tgl_cetak->month] . ' ' . $tgl_cetak->format('Y');
    @endphp

    <div class="header-logo">
        <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
    </div>

    <div class="title-container">
        <div class="title-main">KUITANSI LAIN-LAIN</div>
    </div>

    <table class="content-table">
        <tr>
            <td class="label-col">No. Kuitansi</td>
            <td class="colon-col">:</td>
            <td class="font-bold">{{ $kuitansi->no_bukti }}</td>
        </tr>
        <tr>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td class="uppercase font-bold">{{ $kuitansi->nama }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Lengkap</td>
            <td class="colon-col">:</td>
            <td class="uppercase">{{ $alamatLengkap }}</td>
        </tr>
        <tr>
            <td class="label-col">No. Telepon</td>
            <td class="colon-col">:</td>
            <td>{{ $kuitansi->no_telepon ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Keterangan</td>
            <td class="colon-col">:</td>
            <td class="uppercase font-bold">{{ $kuitansi->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tipe Motor</td>
            <td class="colon-col">:</td>
            <td class="uppercase font-bold">{{ $kuitansi->tipe_motor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Uang Sejumlah</td>
            <td class="colon-col">:</td>
            <td>
                <span class="amount-text">Rp {{ number_format($kuitansi->nilai, 0, ',', '.') }},-</span>
            </td>
        </tr>
        <tr>
            <td class="label-col">Terbilang</td>
            <td class="colon-col">:</td>
            <td class="font-bold" style="font-style: italic; background-color: #f9f9f9; padding: 6px;">
                {{ $terbilang }}
            </td>
        </tr>
    </table>

    <div class="signature-container">
        <div class="signature-row">
            <div class="signature-cell">
                Mengetahui,<br>
                <div class="signature-space"></div>
                ( ............................................................ )
            </div>
            <div class="signature-cell">
                Garut, {{ $tanggalIndo }}<br>
                Yang Menerima,<br>
                <div class="signature-space"></div>
                ( ............................................................ )
            </div>
        </div>
    </div>

</body>
</html>
