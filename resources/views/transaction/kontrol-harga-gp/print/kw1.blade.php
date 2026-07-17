<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI SUBSIDI - {{ $kontrol->no_kwitansi_kw1 }}</title>
    <style>
        @page { size: 215mm 165mm landscape; margin: 5mm 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.4; }

        .header-table { width: 100%; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header-logo { width: 75%; vertical-align: middle; }
        .header-logo img { max-height: 60px; width: auto; }
        
        .header-meta { width: 25%; vertical-align: top; }
        .header-meta table { width: 100%; font-size: 11px; border-collapse: collapse; }
        .header-meta td { padding: 1px 0; border: none; vertical-align: top; }

        .title-container { width: 100%; margin-bottom: 15px; }
        .title-main { font-size: 11px; font-weight: bold; text-align: center; text-decoration: underline; letter-spacing: 2px; }

        .content-table { width: 100%; margin-bottom: 10px; font-size: 11px; border-collapse: collapse; }
        .content-table td { padding: 4px 6px; border: none; vertical-align: top; }
        
        .uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        
        .amount-text { font-size: 12px; font-weight: bold; }
        
        .signature-area { width: 100%; margin-top: 25px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; width: 250px; font-weight: bold; }
        .signature-space { height: 60px; }
    </style>
</head>
<body onload="window.print()">

    @php
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tgl_kw1 = \Carbon\Carbon::parse($kontrol->tgl_kwitansi_kw1);
        $tanggalIndo = $tgl_kw1->format('d') . ' ' . $bulanIndo[$tgl_kw1->month] . ' ' . $tgl_kw1->format('Y');

        $nama_pemohon = trim(strtoupper($spk->nama_pemohon));
        $nama_stnk = trim(strtoupper($spk->nama_stnk));
        $display_name = $nama_pemohon === $nama_stnk ? $nama_pemohon : $nama_pemohon . ' QQ ' . $nama_stnk;
        
        $keterangan = !empty($spk->leasing->keterangan_penagihan_1) ? $spk->leasing->keterangan_penagihan_1 : 'PEMBAYARAN SUBSIDI LEASING';
    @endphp

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
            </td>
            <td class="header-meta">
                <table>
                    <tr>
                        <td style="width: 70px;">No. Kuitansi</td>
                        <td style="width: 10px;">:</td>
                        <td><b>{{ $kontrol->no_kwitansi_kw1 }}</b></td>
                    </tr>
                    <tr>
                        <td>No. SJ</td>
                        <td>:</td>
                        <td>{{ $suratJalan->no_bukti }}</td>
                    </tr>
                    <tr>
                        <td>No. GPK</td>
                        <td>:</td>
                        <td>{{ $spk->no_spk }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ $tgl_kw1->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title-container">
        <div class="title-main">KUITANSI</div>
    </div>

    <table class="content-table">
        <tr>
            <td style="width: 130px;">Telah Diterima Dari</td>
            <td style="width: 10px;">:</td>
            <td class="uppercase font-bold">{{ $spk->leasing->nama_leasing ?? '-' }}</td>
        </tr>
        <tr>
            <td>Penjualan Atas Nama</td>
            <td>:</td>
            <td class="uppercase font-bold">{{ $display_name }}</td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td>:</td>
            <td>
                <span class="amount-text">Rp {{ number_format($kontrol->dll_1, 0, ',', '.') }},-</span>
            </td>
        </tr>
        <tr>
            <td>Terbilang</td>
            <td>:</td>
            <td class="font-bold" style="font-style: italic; background-color: #f5f5f5; padding: 6px;">
                {{ ucwords(\Terbilang::make($kontrol->dll_1)) }} Rupiah
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td class="font-bold uppercase">{{ $keterangan }}</td>
        </tr>
        <tr>
            <td>Tipe</td>
            <td>:</td>
            <td class="uppercase font-bold">{{ $spk->motorUnit->type->kode_motor ?? '-' }} ({{ $spk->motorUnit->type->nama_type ?? '-' }}) Tahun: {{ $spk->motorUnit->tahun_pembuatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Warna</td>
            <td>:</td>
            <td class="uppercase">{{ $spk->motorUnit->color->warna ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Mesin</td>
            <td>:</td>
            <td class="uppercase font-bold" style="font-family: monospace;">{{ $spk->motorUnit->no_mesin ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Rangka</td>
            <td>:</td>
            <td class="uppercase" style="font-family: monospace;">{{ $spk->motorUnit->no_rangka ?? '-' }}</td>
        </tr>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            Garut, {{ $tanggalIndo }}<br>
            Hormat Kami,<br>
            <div class="signature-space"></div>
            ( ............................................................ )
        </div>
    </div>

</body>
</html>