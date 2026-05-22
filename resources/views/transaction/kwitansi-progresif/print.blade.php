<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Progresif - {{ $kwitansi->no_kwitansi }}</title>
    <style>
        /* Pengaturan Kertas 1/2 F4 */
        @page { size: 215mm 165mm; margin: 10mm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.4;
            background-color: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-area {
            width: 100%;
        }

        .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { width: 120px; margin-right: 15px; }
        .header-text h2 { margin: 0; font-size: 18px; color: #dc2626; font-style: italic; }
        .header-text p { margin: 2px 0 0 0; font-size: 10px; }

        .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .kwitansi-no { text-align: center; font-size: 12px; margin-bottom: 20px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 4px 0; }
        .col-label { width: 25%; font-weight: bold; }
        .col-colon { width: 2%; text-align: center; }

        .nominal-box { background-color: #f3f4f6; padding: 10px; border: 1px dashed #000; display: inline-block; font-size: 16px; font-weight: bold; margin-top: 15px; }

        .ttd-box { width: 100%; margin-top: 30px; text-align: right; }
        .ttd-space { height: 70px; }
    </style>
</head>
<body onload="window.print();">

    <div class="print-area">
        <div class="header">
            <img src="{{ asset('images/spk/logo.jpeg') }}" class="logo" alt="Logo">
            <div class="header-text">
                <h2>CV. SURYA WIJAYA SEJAHTERA</h2>
                <p>JL. PAPANDAYAN NO. 112, GARUT - 44111</p>
                <p>TELP. (0262) 231236 - 231370 | Email: suryahondagarut@gmail.com</p>
            </div>
        </div>

        <div class="title">Kwitansi Pajak Progresif</div>
        <div class="kwitansi-no">No: {{ $kwitansi->no_kwitansi }}</div>

        @php
            $spk = $kwitansi->suratJalan->spk;
            $samsat = $kwitansi->suratJalan->samsat;
        @endphp

        <table>
            <tr>
                <td class="col-label">Terima Dari</td>
                <td class="col-colon">:</td>
                <td style="font-weight: bold; font-size: 14px; text-transform: uppercase;">{{ $spk->nama_stnk }}</td>
            </tr>
            <tr>
                <td class="col-label">Uang Sejumlah</td>
                <td class="col-colon">:</td>
                <td style="font-style: italic; background-color: #f9fafb; padding: 2px 5px; border-left: 3px solid #000;">
                    # {{ ucwords(\Terbilang::make($samsat->pajak_progresif)) }} Rupiah #
                </td>
            </tr>
            <tr>
                <td class="col-label">Untuk Pembayaran</td>
                <td class="col-colon">:</td>
                <td>Pajak Progresif Kendaraan Bermotor a/n <b>{{ strtoupper($spk->nama_stnk) }}</b></td>
            </tr>
            <tr>
                <td class="col-label">Tipe Motor / No Pol</td>
                <td class="col-colon">:</td>
                <td>{{ strtoupper($spk->motorType->nama_type ?? '-') }} / <b>{{ strtoupper($samsat->no_polisi ?? '-') }}</b></td>
            </tr>
            <tr>
                <td class="col-label">No. Mesin / Rangka</td>
                <td class="col-colon">:</td>
                <td>{{ strtoupper($kwitansi->suratJalan->motorUnit->no_mesin ?? '-') }} / {{ strtoupper($kwitansi->suratJalan->motorUnit->no_rangka ?? '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">Rincian Pembayaran</td>
                <td class="col-colon">:</td>
                <td>
                    @if($kwitansi->bayar_kontan > 0) Cash: Rp {{ number_format($kwitansi->bayar_kontan,0,',','.') }} &nbsp;&nbsp; @endif
                    @if($kwitansi->bayar_transfer > 0) Transfer: Rp {{ number_format($kwitansi->bayar_transfer,0,',','.') }} ({{ $kwitansi->rekening_tujuan }}) @endif
                </td>
            </tr>
            @if($kwitansi->no_po_leasing)
            <tr>
                <td class="col-label">No. PO Leasing</td>
                <td class="col-colon">:</td>
                <td>{{ strtoupper($kwitansi->no_po_leasing) }} ({{ strtoupper($spk->leasing->nama_leasing ?? '-') }})</td>
            </tr>
            @endif
        </table>

        <div class="nominal-box">
            Rp {{ number_format($samsat->pajak_progresif, 0, ',', '.') }},-
        </div>

        <table class="ttd-box">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%; text-align: center;">
                    Garut, {{ \Carbon\Carbon::parse($kwitansi->tanggal)->format('d F Y') }}<br>
                    Penerima Kasir,
                    <div class="ttd-space"></div>
                    <span style="font-weight: bold; text-decoration: underline;">( {{ strtoupper(auth()->user()->name ?? 'Admin Kasir') }} )</span>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.onafterprint = function() {
            // Jika tab ini memiliki 'opener' (dibuka via target="_blank" dari Print Ulang), tutup tab.
            // Jika dibuka langsung dari fungsi Simpan Form, kembali ke halaman Riwayat.
            if (window.opener) {
                setTimeout(window.close, 500);
            } else {
                setTimeout(function() {
                    window.location.href = "{{ route('kwitansi-progresif.index', ['tab' => 'riwayat']) }}";
                }, 500);
            }
        };
    </script>
</body>
</html>
