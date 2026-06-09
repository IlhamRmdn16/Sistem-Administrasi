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
            font-size: 11pt;
            text-transform: uppercase;
            color: #000;
        }

        .kelompok-1 {
            position: absolute;
            top: 11cm;
            left: 7cm;
            right: 5cm; /* Paksa batas tulisan berhenti 5cm sebelum pinggir kanan kertas */
        }

        .kelompok-2 {
            position: absolute;
            /* Jika kelompok 1 isinya 4 baris (sekitar 2cm total tinggi),
               dan Anda ingin jarak 0.9cm setelahnya, maka top-nya sekitar 13.9cm.
               Silakan diubah sedikit jika kurang pas saat diprint. */
            top: 13.9cm;
            left: 7cm;
            right: 5cm; /* Paksa batas tulisan berhenti 5cm sebelum pinggir kanan kertas */
        }

        .baris {
            margin-bottom: 0.1cm;
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
                         ($spk->desa_kelurahan ? ', ' . $spk->desa_kelurahan : '') .
                         ($spk->kecamatan ? ', KEC. ' . $spk->kecamatan : '');

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
        <div class="baris">{{ $motor->type->nama_type ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{{ $motor->type->jenis ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{{ $motor->tahun_pembuatan ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{{ $motor->no_rangka ?? '-' }}</div>
        <div class="baris">{{ $motor->no_mesin ?? '-' }}</div>
        <div class="baris">{{ $motor->color->warna ?? '-' }}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">DUA</div>
        <div class="baris">{!! '&nbsp;' !!}</div>
        <div class="baris">{!! $samsat->no_bpkb ?? '&nbsp;' !!}</div>
    </div>

    <button class="no-print btn-print-floating" onclick="window.print()">
        🖨️ Cetak Sekarang
    </button>

</body>
</html>
