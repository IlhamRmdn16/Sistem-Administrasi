<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Blanko Samsat - {{ $suratJalan->no_bukti }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8pt;
            text-transform: uppercase;
            color: #000;
        }

        .kelompok-1 {
            position: absolute;
            top: 11.3cm;
            left: 7cm;
            right: 5cm;
        }

        .kelompok-2 {
            position: absolute;
            top: 13.3cm;
            left: 7cm;
            right: 5cm;
        }

        .baris {
            margin-bottom: 0.08cm;
            line-height: 1;
        }

        @media print {
            .no-print { display: none !important; }
        }

        .btn-print-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #E60000;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            text-transform: none;
        }
    </style>
</head>
<body onload="window.print()">

    @php
        $spk = $suratJalan->spk;
        $motor = $suratJalan->motorUnit;
        $samsat = $suratJalan->samsat;

        // ALAMAT DIRINGKAS: Hanya Alamat Jalan, Desa, dan Kecamatan
        $alamatRingkas = $spk->alamat .
                         ($spk->kota_kabupaten ? ', ' . $spk->kota_kabupaten : '');

        $kontak = $spk->telepon;
        if($spk->email) {
            $kontak .= ' / ' . $spk->email;
        }
    @endphp

    <div class="kelompok-1">
        <div class="baris">{{ $spk->nama_stnk ?? '-' }}</div>
        <div class="baris">{{ $alamatRingkas }}</div>
        <div class="baris">{{ $spk->nik ?? '-' }}</div>
        <div class="baris">{!! $kontak ?: '&nbsp;' !!}</div>
    </div>

    <div class="kelompok-2">
        <div class="baris">HONDA</div>
        <div class="baris">{{ $motor->type->kode_motor ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{{ $motor->type->jenis ?? '-' }}</div>
        <div class="baris">SEPEDA MOTOR</div>
        <div class="baris">{{ $motor->tahun_pembuatan ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{{ $motor->no_rangka ?? '-' }}</div>
        <div class="baris">{{ $motor->no_mesin ?? '-' }}</div>
        <div class="baris">{{ $motor->color->warna ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
    </div>

    <button class="no-print btn-print-floating" onclick="window.print()">
        🖨️ Cetak Sekarang
    </button>

</body>
</html>
