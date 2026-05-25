<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Progresif - {{ $kwitansi->no_kwitansi }}</title>
    <style>
        @page { size: 215mm 165mm; margin: 5mm 8mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        .header-logo { width: 100%; border-bottom: 0.5px solid #000; padding-bottom: 5px; margin-bottom: 10px; text-align: center; }
        .header-logo img { max-height: 70px; width: auto; }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }

        .section-title { font-weight: bold; margin: 8px 0 10px 0; text-transform: uppercase; font-size: 12px; text-align: center; text-decoration: underline; }
    </style>
</head>

@php
    $spk = $kwitansi->suratJalan->spk;
    $samsat = $kwitansi->suratJalan->samsat;

    $alamat_parts = [];
    if (!empty($spk->alamat)) $alamat_parts[] = $spk->alamat;
    if (!empty($spk->rt_rw)) $alamat_parts[] = 'RT/RW ' . $spk->rt_rw;
    if (!empty($spk->desa_kelurahan)) $alamat_parts[] = 'Kel/Desa ' . $spk->desa_kelurahan;
    if (!empty($spk->kecamatan)) $alamat_parts[] = 'Kec. ' . $spk->kecamatan;
    if (!empty($spk->kota_kabupaten)) $alamat_parts[] = 'Kab. ' . $spk->kota_kabupaten;
    $alamat = implode(', ', $alamat_parts);
@endphp

<body onload="window.print()">

    <div class="header-logo">
        <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
    </div>

    <div class="section-title">TANDA TERIMA PELUNASAN PAJAK PROGRESIF</div>

    <table>
        <tr>
            <td style="width: 20%;">No. Kwitansi</td>
            <td style="width: 2%;">:</td>
            <td style="width: 78%;">{{ $kwitansi->no_kwitansi }}</td>
        </tr>
        <tr>
            <td>No. SPK</td>
            <td>:</td>
            <td>{{ $spk->no_spk }}</td>
        </tr>
        <tr>
            <td>Sudah Terima Dari</td>
            <td>:</td>
            <td style="font-weight: bold; text-transform: uppercase;">{{ $spk->nama_stnk }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td style="text-transform: uppercase;">{{ $alamat }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td>Pelunasan Pajak Progresif</td>
        </tr>
        <tr>
            <td>Banyaknya Uang</td>
            <td>:</td>
            <td style="font-weight: bold; font-style: italic;">
                {{ ucwords(\Terbilang::make($samsat->pajak_progresif)) }} Rupiah
            </td>
        </tr>
        <tr>
            <td>Jumlah KONTAN</td>
            <td>:</td>
            <td>Rp. <span style="display: inline-block; width: 60px; text-align: right;">{{ number_format($kwitansi->bayar_kontan, 0, ',', '.') }}</span></td>
        </tr>
        <tr>
            <td>Jumlah TRANSFER</td>
            <td>:</td>
            <td>
                Rp. <span style="display: inline-block; width: 60px; text-align: right;">{{ number_format($kwitansi->bayar_transfer, 0, ',', '.') }}</span>
                @if($kwitansi->bayar_transfer > 0)
                    <span style="margin-left: 10px;">({{ $kwitansi->rekening_tujuan }})</span>
                @endif
            </td>
        </tr>
    </table>

    <table style="margin-top: 25px; text-align: center;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 25%;">Mengetahui</td>
            <td style="width: 25%;">
                Garut, {{ \Carbon\Carbon::parse($kwitansi->tanggal)->format('d/m/Y') }}<br>
                Yang Menerima
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 60px;"></td>
        </tr>
        <tr>
            <td></td>
            <td>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            <td>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        </tr>
    </table>

    <script>
        window.onafterprint = function() {
            if (window.opener) {
                window.close();
            } else {
                window.location.replace("{{ route('kwitansi-progresif.index', ['tab' => 'riwayat']) }}");
            }
        };
    </script>
</body>
</html>
