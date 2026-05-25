<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima - {{ $sjk->no_bukti }}</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @page { size: 215mm 165mm; margin: 5mm 8mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        .print-controls { background: #f1f5f9; padding: 5px 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        @media print { .no-print { display: none !important; } }

        .header-logo { width: 100%; border-bottom: 0.5px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header-logo img { width: 100%; height: auto; }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        .section-title { font-weight: bold; margin: 8px 0 4px 0; text-transform: uppercase; font-size: 11px; }
        .warning-text { font-size: 10px; margin-top: 5px; text-transform: uppercase; font-weight: bold; }
        .invisible-block { visibility: hidden; }
    </style>
</head>

@php
    $isKredit = $sjk->spk->leasing_id ? true : false;
    $penyerahan = $sjk->penyerahanStnkBpkb;

    $alamat_parts = [];
    if (!empty($sjk->spk->alamat)) $alamat_parts[] = $sjk->spk->alamat;
    if (!empty($sjk->spk->rt_rw)) $alamat_parts[] = 'RT/RW ' . $sjk->spk->rt_rw;
    if (!empty($sjk->spk->desa_kelurahan)) $alamat_parts[] = 'Kel/Desa ' . $sjk->spk->desa_kelurahan;
    if (!empty($sjk->spk->kecamatan)) $alamat_parts[] = 'Kec. ' . $sjk->spk->kecamatan;
    if (!empty($sjk->spk->kota_kabupaten)) $alamat_parts[] = 'Kab. ' . $sjk->spk->kota_kabupaten;
    $alamat = implode(', ', $alamat_parts);
@endphp

<body x-data="{ printTop: true, printBpkb: true }">

    <div class="print-controls no-print">
        <div style="display: flex; gap: 15px; font-size: 12px;">
            <label><input type="checkbox" x-model="printTop"> Cetak STNK</label>
            @if(!$isKredit)
            <label><input type="checkbox" x-model="printBpkb"> Cetak BPKB</label>
            @endif
        </div>
        <button onclick="window.print()" style="padding: 4px 12px; cursor: pointer;">Print Dokumen</button>
    </div>

    <div class="header-logo">
        <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Logo Dealer">
    </div>

    <div :class="!printTop ? 'invisible-block' : ''">
        <div style="margin-bottom: 5px;">SUDAH TERIMA DARI: CV. SURYA WIJAYA SEJAHTERA, SURAT KENDARAAN SEPEDA MOTOR HONDA</div>

        <table>
            <tr>
                <td style="width: 12%;">No. Polisi</td>
                <td style="width: 2%;">:</td>
                <td colspan="7">{{ strtoupper($sjk->samsat->no_polisi ?? '-') }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td colspan="7">{{ strtoupper($sjk->spk->nama_stnk ?? '-') }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td colspan="7">{{ strtoupper($alamat) }}</td>
            </tr>
            <tr>
                <td>Tipe Motor</td>
                <td>:</td>
                <td style="width: 35%;">{{ strtoupper($sjk->motorUnit->type->nama_type ?? '-') }} / {{ strtoupper($sjk->motorUnit->type->kode_motor ?? '-') }}</td>
                <td style="width: 10%;">Warna</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ strtoupper($sjk->motorUnit->color->warna ?? '-') }}</td>
                <td style="width: 8%;">Tahun</td>
                <td style="width: 2%;">:</td>
                <td>{{ strtoupper($sjk->motorUnit->tahun_pembuatan ?? '-') }}</td>
            </tr>
            <tr>
                <td>No. Mesin</td>
                <td>:</td>
                <td>{{ strtoupper($sjk->motorUnit->no_mesin ?? '-') }}</td>
                <td>No. Rangka</td>
                <td>:</td>
                <td colspan="4">{{ strtoupper($sjk->motorUnit->no_rangka ?? '-') }}</td>
            </tr>
        </table>

        <div class="section-title">STNK (SURAT TANDA NOMOR KENDARAAN)</div>
        <table>
            <tr>
                <td style="width: 15%;">Diterima Oleh</td>
                <td style="width: 10%;">Nama</td><td style="width: 2%;">:</td><td style="width: 38%;">{{ $penyerahan && $penyerahan->tgl_serah_stnk ? strtoupper($penyerahan->penerima_stnk) : '........................................' }}</td>
                <td style="width: 35%; text-align: center;" rowspan="3">Penerima<br><br><br>(...........................................)</td>
            </tr>
            <tr><td></td><td>Alamat</td><td>:</td><td>{{ $penyerahan && $penyerahan->tgl_serah_stnk ? strtoupper($penyerahan->alamat_penerima_stnk) : '........................................' }}</td></tr>
            <tr><td></td><td>Tanggal</td><td>:</td><td>{{ $penyerahan && $penyerahan->tgl_serah_stnk ? \Carbon\Carbon::parse($penyerahan->tgl_serah_stnk)->format('d/m/Y') : '......./......./............' }}</td></tr>
        </table>

        @if(!$isKredit)
            <div class="warning-text">PADA WAKTU MENGAMBIL BPKB, HARAP MEMBAWA TANDA TERIMA INI, STNK, KTP, DAN TIDAK DIWAKILKAN.</div>
        @endif
    </div>

    @if(!$isKredit)
    <div :class="!printBpkb ? 'invisible-block' : ''" style="margin-top: 10px;">
        <div class="section-title">BPKB (BUKTI PEMILIK KENDARAAN BERMOTOR)</div>
        <table>
            <tr>
                <td style="width: 15%;">Diterima Oleh</td>
                <td style="width: 10%;">Nama</td><td style="width: 2%;">:</td><td style="width: 38%;">{{ $penyerahan && $penyerahan->tgl_serah_bpkb ? strtoupper($penyerahan->penerima_bpkb) : '........................................' }}</td>
                <td style="width: 35%; text-align: center;" rowspan="3">Penerima<br><br><br>(...........................................)</td>
            </tr>
            <tr><td></td><td>Alamat</td><td>:</td><td>{{ $penyerahan && $penyerahan->tgl_serah_bpkb ? strtoupper($penyerahan->alamat_penerima_bpkb) : '........................................' }}</td></tr>
            <tr><td></td><td>Tanggal</td><td>:</td><td>{{ $penyerahan && $penyerahan->tgl_serah_bpkb ? \Carbon\Carbon::parse($penyerahan->tgl_serah_bpkb)->format('d/m/Y') : '......./......./............' }}</td></tr>
        </table>
    </div>
    @endif

</body>
</html>
