<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI - {{ $kuitansi->no_kuitansi }}</title>
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
    </style>
</head>
<body onload="window.print()">

    @php
        $nama_pemohon = trim(strtoupper($spk->nama_pemohon));
        $nama_stnk = trim(strtoupper($spk->nama_stnk));
        $display_name = $nama_pemohon === $nama_stnk ? $nama_pemohon : $nama_pemohon . ' QQ ' . $nama_stnk;

        $totalBayarIni = $kuitansi->bayar_kontan + $kuitansi->bayar_transfer;
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
                        <td><b>{{ $kuitansi->no_kuitansi }}</b></td>
                    </tr>
                    <tr>
                        <td>No. SJ</td>
                        <td>:</td>
                        <td>{{ $suratJalan->no_bukti ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. SPK</td>
                        <td>:</td>
                        <td>{{ $spk->no_spk }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($kuitansi->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title-container">
        <div class="title-main">KUITANSI PEMBAYARAN KONSUMEN</div>
    </div>

    <table class="content-table">
        <tr>
            <td style="width: 130px;">Telah Diterima Dari</td>
            <td style="width: 10px;">:</td>
            <td class="uppercase font-bold">{{ $nama_pemohon }}</td>
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
                <span class="amount-text">Rp {{ number_format($totalBayarIni, 0, ',', '.') }},-</span>
            </td>
        </tr>
        <tr>
            <td>Terbilang</td>
            <td>:</td>
            <td class="font-bold" style="font-style: italic; background-color: #f5f5f5; padding: 6px;">
                {{ ucwords(\Terbilang::make($totalBayarIni)) }} Rupiah
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td class="font-bold uppercase">PEMBAYARAN KENDARAAN BERMOTOR HONDA {{ $kuitansi->keterangan ? '('.$kuitansi->keterangan.')' : '' }}</td>
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

        <tr>
            <td colspan="3" style="padding-top: 15px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 60%; vertical-align: top;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 130px; padding: 2px 6px;">Tagihan (Nett)</td>
                                    <td style="width: 10px; padding: 2px 0;">:</td>
                                    <td class="font-bold">Rp {{ number_format($targetTagihan, 0, ',', '.') }},-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 6px;">Dibayar (Kuitansi Ini)</td>
                                    <td style="padding: 2px 0;">:</td>
                                    <td class="font-bold">Rp {{ number_format($totalBayarIni, 0, ',', '.') }},-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 6px;">Status Sisa / Kurang</td>
                                    <td style="padding: 2px 0;">:</td>
                                    <td class="font-bold text-red-600">
                                        @if($sisa > 0)
                                            Rp {{ number_format($sisa, 0, ',', '.') }},-
                                        @else
                                            LUNAS
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 40%; vertical-align: top; text-align: center; font-weight: bold; font-size: 11px;">
                            Garut, {{ \Carbon\Carbon::parse($kuitansi->tanggal)->translatedFormat('d F Y') }}<br>
                            Hormat Kami,<br>
                            <div style="height: 50px;"></div>
                            ( ............................................................ )
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
