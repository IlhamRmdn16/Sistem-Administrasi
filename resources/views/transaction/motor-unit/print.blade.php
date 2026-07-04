<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Unit - {{ $unit->no_kunci }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            @page {
                size: 148mm 210mm;
                margin: 0;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: white !important;
                margin: 0;
                padding: 0;
            }
            .print-area {
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            line-height: 1.2;
            font-size: 12px;
            background-color: #fff;
        }

        .data-row {
            display: grid;
            grid-template-columns: 95px 10px auto;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .print-area {
            width: 148mm;
            padding: 8mm 8mm;
            margin: 0 auto;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">

    <div class="print-area">
        <div class="w-full">
            <div class="data-row">
                <div>No. Kunci</div>
                <div>:</div>
                <div class="font-bold">{{ $unit->no_kunci }}</div>
            </div>
            <div class="data-row">
                <div>Tipe Motor</div>
                <div>:</div>
                <div class="uppercase">{{ $unit->type->nama_type ?? '-' }}</div>
            </div>
            <div class="data-row">
                <div>Warna</div>
                <div>:</div>
                <div class="uppercase">{{ $unit->color->warna ?? '-' }}</div>
            </div>
            <div class="data-row">
                <div>Tahun</div>
                <div>:</div>
                <div>{{ $unit->tahun_pembuatan }}</div>
            </div>
            <div class="data-row">
                <div>No. Rangka</div>
                <div>:</div>
                <div class="uppercase">{{ $unit->no_rangka }}</div>
            </div>
            <div class="data-row">
                <div>No. Mesin</div>
                <div>:</div>
                <div class="uppercase">{{ $unit->no_mesin }}</div>
            </div>
            <div class="data-row">
                <div>No. Bukti</div>
                <div>:</div>
                <div class="font-bold">{{ $unit->penerimaanUnit->no_bukti ?? '-' }}</div>
            </div>
            <div class="data-row">
                <div>No. SJ / Tgl</div>
                <div>:</div>
                <div>{{ $unit->penerimaanUnit->no_sj ?? '-' }} / {{ \Carbon\Carbon::parse($unit->penerimaanUnit->tanggal)->format('d-m-Y') }}</div>
            </div>
        </div>

        <div class="border-t-[1.5px] border-black my-4"></div>

        <div class="w-full pl-1 mb-5">
            <div class="font-bold text-[11px] mb-1.5 leading-tight w-[85%]">
                KELENGKAPAN UNIT
            </div>
            <table class="w-[85%] border-collapse border border-black text-[11px]">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="border border-black px-1.5 py-1 text-left font-bold w-[75%]">PERLENGKAPAN</th>
                        <th class="border border-black px-1.5 py-1 text-center font-bold">KET.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['AKI', 'SPION', 'SAFETY TOOL', 'KARPET', 'HELM', 'KUNCI KONTAK', 'DISLOCK', 'DUDUKAN PLAT', 'TOOLKIT'] as $item)
                    <tr>
                        <td class="border border-black px-1.5 py-0.5">{{ $item }}</td>
                        <td class="border border-black px-1.5 py-0.5 text-center h-4"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-[9px] mt-1">*Karpet & Dislock Untuk Type Tertentu.</div>
        </div>

        <div class="w-full">
            <table class="w-full border-collapse border border-black text-[10px] text-center">
                <tr>
                    <td class="border border-black font-bold py-1 w-1/4">Head Admin</td>
                    <td class="border border-black font-bold py-1 w-1/4">Admin</td>
                    <td class="border border-black font-bold py-1 w-1/4">SPV</td>
                    <td class="border border-black font-bold py-1 w-1/4">Checker</td>
                </tr>
                <tr>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                </tr>
                <tr>
                    <td class="border border-black font-bold py-1">PDI Man</td>
                    <td class="border border-black font-bold py-1">Sales Force</td>
                    <td class="border border-black font-bold py-1">Delivery</td>
                    <td class="border border-black"></td>
                </tr>
                <tr>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                    <td class="border border-black h-14"></td>
                </tr>
                <tr>
                    <td colspan="4" class="border border-black h-8 bg-gray-50"></td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>