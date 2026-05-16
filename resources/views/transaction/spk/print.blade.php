<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $spk->no_spk }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: A4 portrait; margin: 10mm; }
        }
        body { font-family: 'Arial', sans-serif; background-color: #f3f4f6; }
        .page { width: 210mm; min-height: 297mm; background: white; margin: 0 auto; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .table-print th, .table-print td { padding: 6px 10px; border: 1px solid #000; font-size: 13px; }
        .table-print th { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body class="py-8">

    <div class="max-w-[210mm] mx-auto mb-4 text-right no-print">
        <button onclick="window.print()" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-red-700">
            Cetak Dokumen
        </button>
    </div>

    <div class="page text-gray-900">
        <div class="border-b-2 border-black pb-4 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-wider text-red-600">SURYA WIJAYA SEJAHTERA</h1>
                <p class="text-sm font-semibold mt-1">Authorized Honda Dealer & AHASS</p>
                <p class="text-xs text-gray-600 mt-0.5">Garut, Jawa Barat</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold uppercase border-2 border-gray-900 p-2 inline-block rounded">SURAT PESANAN</h2>
                <div class="mt-2 text-sm">
                    <div><span class="font-bold">No. SPK :</span> {{ $spk->no_spk }}</div>
                    <div><span class="font-bold">Tanggal :</span> {{ \Carbon\Carbon::parse($spk->tanggal)->format('d F Y') }}</div>
                    <div><span class="font-bold">Sales :</span> {{ $spk->sales->nama_sales ?? '-' }}</div>
                </div>
            </div>
        </div>

        <h3 class="text-sm font-bold uppercase mb-2 bg-gray-200 p-1.5 border border-black">I. Biodata Pemesan & STNK</h3>
        <table class="w-full text-sm mb-6 table-print">
            <tr>
                <td class="w-1/4 font-bold">Nama Pemesan</td>
                <td class="w-3/4">{{ $spk->nama_pemohon }}</td>
            </tr>
            <tr>
                <td class="font-bold">Nama di STNK</td>
                <td>{{ $spk->nama_stnk }}</td>
            </tr>
            <tr>
                <td class="font-bold">No. Identitas (NIK)</td>
                <td>{{ $spk->nik }}</td>
            </tr>
            <tr>
                <td class="font-bold">Alamat Lengkap</td>
                <td>{{ $spk->alamat }}, RT/RW: {{ $spk->rt_rw }}, Kel/Desa: {{ $spk->desa_kelurahan }}, Kec. {{ $spk->kecamatan }}, {{ $spk->kota_kabupaten }}</td>
            </tr>
            <tr>
                <td class="font-bold">No. Telepon / WA</td>
                <td>{{ $spk->telepon }}</td>
            </tr>
        </table>

        <h3 class="text-sm font-bold uppercase mb-2 bg-gray-200 p-1.5 border border-black">II. Detail Kendaraan</h3>
        <table class="w-full text-sm mb-6 table-print">
            <tr>
                <th class="w-1/3">Tipe / Model Motor</th>
                <th class="w-1/3">Warna</th>
                <th class="w-1/3">Tahun Pembuatan</th>
            </tr>
            <tr class="text-center">
                <td>{{ $spk->motorType->nama_type ?? '-' }}</td>
                <td>{{ $spk->motorColor->warna ?? '-' }}</td>
                <td>{{ $spk->motorType->tahun_pembuatan ?? '-' }}</td>
            </tr>
        </table>

        <h3 class="text-sm font-bold uppercase mb-2 bg-gray-200 p-1.5 border border-black">III. Rincian Pembayaran</h3>
        <table class="w-full text-sm mb-6 table-print">
            <tr>
                <td class="w-1/3 font-bold">Jenis Transaksi</td>
                <td class="w-2/3 font-bold uppercase text-lg">{{ $spk->jenis_pembayaran }}</td>
            </tr>
            <tr>
                <td class="font-bold">Harga OTR</td>
                <td>Rp {{ number_format($spk->harga_otr, 0, ',', '.') }}</td>
            </tr>

            @if($spk->jenis_pembayaran == 'Kredit')
            <tr>
                <td class="font-bold">Leasing / Pembiayaan</td>
                <td>{{ $spk->leasing->nama_leasing ?? '-' }}</td>
            </tr>
            <tr>
                <td class="font-bold">Uang Muka (DP)</td>
                <td>Rp {{ number_format($spk->uang_muka, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Tanda Jadi (Titipan)</td>
                <td>Rp {{ number_format($spk->tanda_jadi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Skema Angsuran</td>
                <td>{{ $spk->tenor_bulan }} Bulan x Rp {{ number_format($spk->cicilan, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <div class="text-xs text-justify mb-8 text-gray-700 border border-black p-3">
            <p class="font-bold mb-1">Ketentuan:</p>
            <ol class="list-decimal pl-4 space-y-1">
                <li>SPK ini sah apabila tanda jadi telah disetorkan dan diterima oleh Kasir dealer.</li>
                <li>Harga tidak mengikat dan dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu (Mengikuti harga OTR saat unit diserahkan/faktur turun).</li>
                <li>Bila transaksi kredit batal karena ditolak oleh pihak Leasing, maka tanda jadi akan dikembalikan sepenuhnya.</li>
                <li>Bila pembatalan dilakukan sepihak oleh pemesan, maka tanda jadi dinyatakan hangus.</li>
            </ol>
        </div>

        <div class="flex justify-between text-center mt-12 text-sm">
            <div class="w-1/3">
                <p class="mb-16">Pemesan,</p>
                <p class="font-bold underline">{{ $spk->nama_pemohon }}</p>
            </div>
            <div class="w-1/3">
                <p class="mb-16">Sales Person,</p>
                <p class="font-bold underline">{{ $spk->sales->nama_sales ?? '-' }}</p>
            </div>
            <div class="w-1/3">
                <p class="mb-16">Mengetahui (Kacab / SPV),</p>
                <p class="font-bold underline">_________________________</p>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
