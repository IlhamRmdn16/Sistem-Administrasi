<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $spk->no_spk }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Mutlak 1/2 F4 (215mm x 165mm) */
        @media print {
            @page {
                size: 215mm 165mm;
                margin: 0;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white !important;
                margin: 0;
                padding: 0;
            }
            .no-print { display: none !important; }
            .screen-wrapper { padding: 0 !important; }

            .page-container {
                width: 215mm;
                height: 165mm;
                margin: 0;
                padding: 5mm 10mm 5mm 10mm;
                box-shadow: none;
                border: none;
            }
        }

        /* Pengaturan Layar Preview */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .screen-wrapper {
            padding: 2rem 0;
        }

        .page-container {
            width: 215mm;
            height: 165mm;
            background: white;
            margin: 0 auto;
            padding: 5mm 10mm 5mm 10mm;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Tabel Super Rapat & Sejajar */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 13px; /* Ukuran font disesuaikan agar pas dibaca */
            line-height: 1.25;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="screen-wrapper">
        <div class="max-w-[215mm] mx-auto mb-4 text-right no-print">
            <button onclick="window.print()" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-red-700 flex items-center gap-2 ml-auto text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak (1/2 F4)
            </button>
        </div>

        <div class="page-container">

            <div class="border-b-[1.5px] border-gray-800 pb-1.5 mb-1.5 shrink-0 flex justify-center w-full">
                <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Kop Surat" class="w-auto h-[65px] object-contain">
            </div>

            <div class="text-center mb-1 shrink-0">
                <h2 class="text-[14px] font-bold underline uppercase tracking-wider">Surat Pesanan Kendaraan</h2>
            </div>

            <div class="flex-grow">
                <table>
                    <colgroup>
                        <col style="width: 105px;"> <col style="width: 15px;">  <col style="width: auto;">  <col style="width: 65px;">  <col style="width: 15px;">  <col style="width: 110px;"> </colgroup>

                    <tr>
                        <td>No. SPK</td>
                        <td class="text-center">:</td>
                        <td class="uppercase font-bold">{{ $spk->no_spk }}</td>
                        <td>Tanggal</td>
                        <td class="text-center">:</td>
                        <td>{{ \Carbon\Carbon::parse($spk->tanggal)->format('d/m/y') }}</td>
                    </tr>
                    <tr>
                        <td>Nama Sales</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->sales->nama_sales ?? '-' }}</td>
                    </tr>

                    <tr><td colspan="6" class="h-2"></td></tr>

                    <tr>
                        <td>Nama Pemohon</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase font-bold">{{ $spk->nama_pemohon }}</td>
                    </tr>
                    <tr>
                        <td>Nama STNK</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase font-bold">{{ $spk->nama_stnk }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->alamat }}</td>
                    </tr>
                    <tr>
                        <td>RT/RW</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->rt_rw }}</td>
                    </tr>
                    <tr>
                        <td>Desa/Kel.</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->desa_kelurahan }}</td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->kecamatan }}</td>
                    </tr>
                    <tr>
                        <td>Kab/Kota</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->kota_kabupaten }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td class="text-center">:</td>
                        <td class="uppercase">{{ $spk->telepon }}</td>
                        <td>Email</td>
                        <td class="text-center">:</td>
                        <td class="lowercase">{{ $spk->email ?? '-' }}</td>
                    </tr>

                    <tr><td colspan="6" class="h-2"></td></tr>

                    <tr>
                        <td>Tipe Motor</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="uppercase">{{ $spk->motorType->nama_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Warna</td>
                        <td class="text-center">:</td>
                        <td class="uppercase">{{ $spk->motorColor->warna ?? '-' }}</td>
                        <td>Tahun</td>
                        <td class="text-center">:</td>
                        <td>{{ $spk->motorType->tahun_pembuatan ?? '-' }}</td>
                    </tr>

                    <tr><td colspan="6" class="h-2"></td></tr>

                    <tr>
                        <td>Harga OTR</td>
                        <td class="text-center">:</td>
                        <td colspan="4" class="font-bold">Rp {{ number_format($spk->harga_otr, 0, '.', '.') }}</td>
                    </tr>

                    @if($spk->jenis_pembayaran == 'Kredit')
                        <tr>
                            <td>Uang Muka</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="font-bold">Rp {{ number_format($spk->uang_muka, 0, '.', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Tanda Jadi</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="font-bold">Rp {{ number_format($spk->tanda_jadi, 0, '.', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Nama Leasing</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $spk->leasing->nama_leasing ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tenor</td>
                            <td class="text-center">:</td>
                            <td colspan="4">{{ $spk->tenor_bulan }} <span class="mx-1">x</span> Rp {{ number_format($spk->cicilan, 0, '.', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>Keterangan</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase font-bold tracking-widest text-sm">KONTAN</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="flex justify-between text-center text-[12px] pt-4 shrink-0">
                <div class="w-1/3">
                    <p class="mb-14">Konsumen</p>
                    <div class="flex justify-between px-8">
                        <span>(</span><span>)</span>
                    </div>
                </div>
                <div class="w-1/3">
                    <p class="mb-14">Sales Admin</p>
                    <div class="flex justify-between px-8">
                        <span>(</span><span>)</span>
                    </div>
                </div>
                <div class="w-1/3">
                    <p class="mb-14">Mengetahui</p>
                    <div class="flex justify-between px-8">
                        <span>(</span><span>)</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
