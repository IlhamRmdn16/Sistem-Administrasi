<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Kwitansi - {{ $kwitansi->no_kwitansi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya kertas Kwitansi (1/2 F4) di layar */
        .sheet {
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            width: 210mm; /* Mendekati F4 */
            min-height: 148mm;
            padding: 10mm;
            position: relative;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        /* Navigasi atas (hanya muncul di layar PC) */
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .sheet { 
                margin: 0; 
                box-shadow: none; 
                width: 100%; 
                padding: 0;
            }
        }

        body { background: #e2e8f0; }
        
        /* Gaya Konten Kwitansi */
        .header-logo { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { width: 100px; margin-right: 15px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .nominal-box { background-color: #f3f4f6; padding: 10px; border: 1px dashed #000; display: inline-block; font-size: 15px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>

    <div class="no-print sticky top-0 bg-slate-800 text-white p-4 shadow-lg z-50">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('kwitansi-progresif.index') }}" class="bg-slate-600 hover:bg-slate-700 px-4 py-2 rounded font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                <span class="text-slate-400">|</span>
                <p class="text-xs font-medium">Preview Kwitansi: <span class="text-blue-400">{{ $kwitansi->no_kwitansi }}</span></p>
            </div>
            
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded font-bold text-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Sekarang
            </button>
        </div>
    </div>

    <div class="sheet">
        <div class="header-logo">
            <img src="{{ asset('images/spk/logo.jpeg') }}" class="logo" alt="Logo">
            <div>
                <h2 class="text-red-600 font-bold italic text-lg m-0">CV. SURYA WIJAYA SEJAHTERA</h2>
                <p class="text-[10px] m-0">JL. PAPANDAYAN NO. 112, GARUT - 44111</p>
                <p class="text-[10px] m-0 italic text-gray-500">Telp. (0262) 231236 | Email: suryahondagarut@gmail.com</p>
            </div>
        </div>

        <div class="title uppercase">Kwitansi Pajak Progresif</div>
        <div class="text-center font-bold mb-6 mt-[-5px]">No: {{ $kwitansi->no_kwitansi }}</div>

        @php
            $spk = $kwitansi->suratJalan->spk;
            $samsat = $kwitansi->suratJalan->samsat;
        @endphp

        <table class="w-full text-sm">
            <tr>
                <td class="w-1/4 font-bold py-1">Terima Dari</td>
                <td class="w-[2%]">:</td>
                <td class="font-bold uppercase text-lg">{{ $spk->nama_stnk }}</td>
            </tr>
            <tr>
                <td class="font-bold py-1">Uang Sejumlah</td>
                <td>:</td>
                <td class="italic bg-gray-50 border-l-4 border-slate-800 px-2 py-1">
                    # {{ ucwords(\Terbilang::make($samsat->pajak_progresif)) }} Rupiah #
                </td>
            </tr>
            <tr>
                <td class="font-bold py-1">Untuk Pembayaran</td>
                <td>:</td>
                <td>Pajak Progresif Kendaraan Bermotor a/n <b>{{ strtoupper($spk->nama_stnk) }}</b></td>
            </tr>
            <tr>
                <td class="font-bold py-1">Tipe Motor / No Pol</td>
                <td>:</td>
                <td>{{ strtoupper($spk->motorType->nama_type ?? '-') }} / <b>{{ strtoupper($samsat->no_polisi ?? '-') }}</b></td>
            </tr>
            <tr>
                <td class="font-bold py-1">No. Mesin / Rangka</td>
                <td>:</td>
                <td>{{ strtoupper($kwitansi->suratJalan->motorUnit->no_mesin ?? '-') }} / {{ strtoupper($kwitansi->suratJalan->motorUnit->no_rangka ?? '-') }}</td>
            </tr>
            <tr>
                <td class="font-bold py-1">Rincian Bayar</td>
                <td>:</td>
                <td>
                    @if($kwitansi->bayar_kontan > 0) Cash: Rp {{ number_format($kwitansi->bayar_kontan,0,',','.') }} &nbsp;&nbsp; @endif
                    @if($kwitansi->bayar_transfer > 0) Transfer: Rp {{ number_format($kwitansi->bayar_transfer,0,',','.') }} ({{ $kwitansi->rekening_tujuan }}) @endif
                </td>
            </tr>
            @if($kwitansi->no_po_leasing)
            <tr>
                <td class="font-bold py-1">No. PO Leasing</td>
                <td>:</td>
                <td>{{ strtoupper($kwitansi->no_po_leasing) }} ({{ strtoupper($spk->leasing->nama_leasing ?? '-') }})</td>
            </tr>
            @endif
        </table>

        <div class="nominal-box mt-8">
            Rp {{ number_format($samsat->pajak_progresif, 0, ',', '.') }},-
        </div>

        <div class="flex justify-end mt-12">
            <div class="text-center w-64">
                <p>Garut, {{ \Carbon\Carbon::parse($kwitansi->tanggal)->format('d F Y') }}</p>
                <p class="mb-16">Penerima Kasir,</p>
                <p class="font-bold underline text-sm">( {{ strtoupper(auth()->user()->name ?? 'Admin Kasir') }} )</p>
            </div>
        </div>
    </div>

</body>
</html>