<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SJK - {{ $sj->no_bukti }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
                padding: 4mm 10mm 4mm 10mm;
                box-shadow: none;
                border: none;
            }
        }

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
            padding: 4mm 10mm 4mm 10mm;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 13px;
            line-height: 1.25;
            font-weight: 500;
            color: #111;
        }
    </style>
</head>
<body>

        <div class="screen-wrapper">
            <div class="max-w-[215mm] mx-auto mb-4 text-right no-print">
                <button onclick="window.print()" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-red-700 flex items-center gap-2 ml-auto text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Surat Jalan
                </button>
            </div>

            <div class="page-container">
                <div class="border-b-2 border-gray-900 pb-1 mb-1 shrink-0 flex justify-center w-full">
                    <img src="{{ asset('images/spk/logo.jpeg') }}" alt="Kop Surat" class="w-auto h-[60px] object-contain">
                </div>

                <div class="text-center mb-1 shrink-0">
                    <h2 class="text-[15px] font-bold underline uppercase tracking-wider">Surat Jalan Konsumen</h2>
                </div>

                <div class="mb-2 shrink-0">
                    <table>
                        <colgroup>
                            <col style="width: 110px;">
                            <col style="width: 15px;">
                            <col style="width: auto;">
                            <col style="width: 100px;">
                            <col style="width: 15px;">
                            <col style="width: 130px;">
                        </colgroup>

                        <tr>
                            <td>No. Surat Jalan</td>
                            <td class="text-center">:</td>
                            <td class="uppercase">{{ $sj->no_bukti }}</td>
                            <td>Garut,</td>
                            <td colspan="2">{{ \Carbon\Carbon::parse($sj->tanggal)->format('d/m/y') }}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td class="text-center">:</td>
                            <td class="uppercase font-bold text-gray-900 text-[13.5px]">{{ $sj->spk->nama_pemohon }}</td>
                            <td>No. Kunci</td>
                            <td class="text-center">:</td>
                            <td class="font-mono">{{ $sj->motorUnit->no_kunci ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->spk->alamat }}</td>
                        </tr>
                        <tr>
                            <td>RT/RW</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->spk->rt_rw }}</td>
                        </tr>
                        <tr>
                            <td>Desa/Kel</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->spk->desa_kelurahan }}</td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->spk->kecamatan }}</td>
                        </tr>
                        <tr>
                            <td>Kab/Kota</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->spk->kota_kabupaten }}</td>
                        </tr>

                        <tr><td colspan="6" class="h-1.5"></td></tr>

                        <tr>
                            <td>Tipe Motor</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">
                                {{ $sj->spk->motorType->nama_type }} / {{ $sj->spk->motorType->kode_motor ?? '-' }} / Warna: {{ $sj->spk->motorColor->warna }}
                                <span style="float: right; margin-right: 15px;">Tahun : {{ $sj->spk->motorType->tahun_pembuatan ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>No. Mesin</td>
                            <td class="text-center">:</td>
                            <td class="uppercase">{{ $sj->motorUnit->no_mesin ?? '-' }}</td>
                            <td>No. Rangka</td>
                            <td class="text-center">:</td>
                            <td class="uppercase">{{ $sj->motorUnit->no_rangka ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Kondisi</td>
                            <td class="text-center">:</td>
                            <td colspan="4">100% Baru dan Lengkap</td>
                        </tr>
                        <tr>
                            <td>PDI Man</td>
                            <td class="text-center">:</td>
                            <td colspan="4" class="uppercase">{{ $sj->pdiMan->nama_pdi_man ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="flex justify-between mt-2 text-[13px] font-medium shrink-0">
                    <div class="w-[22%]">
                        <p class="mb-10 text-left">Pembeli / Konsumen</p>
                        <p class="text-left tracking-widest">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                    </div>
                    <div class="w-[22%] text-center">
                        <p class="mb-10">Pengirim</p>
                        <p class="tracking-widest">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                    </div>
                    <div class="w-[22%] text-center">
                        <p class="mb-10">Mengetahui</p>
                        <p class="tracking-widest">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                    </div>
                    <div class="w-[34%] pl-4">
                        <div class="flex mb-3">
                            <span class="w-[110px]">Jam Berangkat</span>
                            <span>:</span>
                        </div>
                        <div class="flex">
                            <span class="w-[110px]">Jam Diterima</span>
                            <span>:</span>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] mt-3 border-t border-gray-400 pt-1.5 font-medium shrink-0">
                    <p class="font-bold">Keterangan:</p>
                    @if(Str::lower($sj->spk->jenis_pembayaran) == 'kredit')
                        <p class="pl-4 mt-0.5">Proses STNK, Plat nomor, dan buku service 2-3 Minggu</p>
                    @else
                        <ol class="list-decimal pl-4 space-y-0.5">
                            <li>Proses STNK, Plat nomor, dan buku service 2-3 Minggu</li>
                            <li>Proses BPKB 2-3 Bulan</li>
                        </ol>
                    @endif
                </div>

            </div>
        </div>

        <script>
            // Buka jendela print saat halaman dimuat
            window.onload = function() {
                window.print();
            };

            // Logika cerdas setelah jendela print ditutup atau dicancel
            window.onafterprint = function() {
                if (window.opener) {
                    // Jika dibuka di tab baru (dari tabel), tutup otomatis
                    setTimeout(window.close, 500);
                } else {
                    // Jika dibuka di tab yang sama, kembalikan ke halaman index
                    setTimeout(function() {
                        window.location.href = "{{ route('suratjalan.index') }}";
                    }, 500);
                }
            };
        </script>
</body>
</html>
