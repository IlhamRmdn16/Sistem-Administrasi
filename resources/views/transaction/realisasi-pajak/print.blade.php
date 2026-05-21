<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Realisasi Pajak - {{ $pengajuan->no_bukti }}</title>
    <style>
        /* Ukuran 1/2 F4 Landscape */
        @page { size: 215mm 165mm; margin: 15mm 10mm; }

        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; }

        /* HEADER LAYOUT: Judul Kiri & No Realisasi Kanan */
        .header-container { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .title-left { font-weight: bold; font-size: 12px; }
        .header-top { width: 300px; font-size: 11px; }
        .header-top table { width: 100%; }
        .header-top td { padding: 3px 0; }

        /* TABEL UTAMA: Border Luar Saja */
        .main-table { width: 100%; border-collapse: collapse; margin-top: 5px; border: 1px solid #000; }

        /* Hilangkan border internal (dalam) */
        .main-table th, .main-table td { border: none; padding: 4px; }

        /* Posisi Isi Tabel Tengah */
        .main-table th { text-align: center; }
        .main-table td { vertical-align: middle; text-align: center; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }

        /* Garis pembatas untuk Header dan Footer */
        .main-table thead tr { border-bottom: 1px solid #000; }
        .row-total td { border-top: 1px solid #000; font-weight: bold; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-container">
        <div class="title-left">REALISASI PENGAJUAN NOTICE PAJAK PROGRESIF</div>

        <div class="header-top">
            <table>
                <tr>
                    <td style="width: 100px;">No. Realisasi</td>
                    <td style="width: 10px;">:</td>
                    <td>{{ strtoupper($pengajuan->no_bukti) }}</td>
                </tr>
                <tr>
                    <td>Tgl. Realisasi</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($tanggal_realisasi)->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th class="text-left">Tipe Motor</th>
                <th style="width: 50px;">Milik</th>
                <th style="width: 50px;">Unit</th>
                <th style="width: 20px;"></th> <th style="width: 100px;" class="text-right">Pajak Progresif</th>
                <th style="width: 20px;"></th> <th style="width: 100px;" class="text-right">Sub. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedData as $index => $group)
                @php
                    $rowSpan = count($group['names']) + 1;
                @endphp
                <tr>
                    <td rowspan="{{ $rowSpan }}">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $group['tipe_motor'] }}</td>

                    <td rowspan="{{ $rowSpan }}">{{ $group['milik'] }}</td>
                    <td rowspan="{{ $rowSpan }}">{{ $group['unit'] }}</td>

                    <td rowspan="{{ $rowSpan }}">x</td>
                    <td rowspan="{{ $rowSpan }}" class="text-right">{{ number_format($group['pajak_progresif'], 0, ',', '.') }}</td>
                    <td rowspan="{{ $rowSpan }}">=</td>
                    <td rowspan="{{ $rowSpan }}" class="text-right">{{ number_format($group['sub_total'], 0, ',', '.') }}</td>
                </tr>
                @foreach($group['names'] as $nama)
                <tr>
                    <td class="text-left" style="padding-left: 10px;">- {{ $nama }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="row-total">
                <td colspan="7" class="text-right" style="padding-right: 10px;">T O T A L :</td>
                <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
