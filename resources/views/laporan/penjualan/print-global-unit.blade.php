<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan Global Unit</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .info { margin-bottom: 15px; font-size: 11px; }

        /* Style Tabel Summary Ringkasan Atas */
        .summary-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        .summary-box td { border: 1px solid #ccc; padding: 8px; text-align: center; background-color: #fafafa; }
        .summary-box .title { font-size: 9px; text-transform: uppercase; color: #555; font-weight: bold; margin-bottom: 3px; }
        .summary-box .value { font-size: 14px; font-weight: font-black; color: #000; }

        /* Style Tabel Utama */
        table.data-table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px; }
        table.data-table th { background-color: #e5e5e5; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }

        .bg-dark { background-color: #dbdbdb; font-weight: bold; }
        .bg-emerald { background-color: #ebf7ee; color: #006400; }
        .bg-amber { background-color: #fff9e6; color: #b25900; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Penjualan - Global Unit</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
        <small>Waktu Cetak: {{ date('d/m/Y H:i:s') }} WIB</small>
    </div>

    <div class="info">
        <b>Jenis Dokumen:</b> {{ $jenis_dokumen == 'all' ? 'Semua (SPK & GPK)' : strtoupper($jenis_dokumen) }}
        @if($search) | <b>Kata Kunci Tipe:</b> "{{ $search }}" @endif
    </div>

    @if($salesData->count() > 0)
    @php
        $tCash = $salesData->sum('unit_cash');
        $tKredit = $salesData->sum('unit_kredit');
        $tUnit = $salesData->sum('total_unit');
        $tOtr = $salesData->sum('total_otr');
        $tTunai = $salesData->sum('total_tunai');
        $tDp = $salesData->sum('total_dp_tanda_jadi');
    @endphp
    <table class="summary-box">
        <tr>
            <td>
                <div class="title">Total Volume Unit Jual</div>
                <div class="value">{{ $tUnit }} <span style="font-size:10px; font-weight:normal;">Unit</span></div>
                <small style="color:#666;">(Cash: {{ $tCash }} | Kredit: {{ $tKredit }})</small>
            </td>
            <td>
                <div class="title">Total Omzet Penjualan (OTR)</div>
                <div class="value">Rp {{ number_format($tOtr, 0, ',', '.') }}</div>
            </td>
            <td style="border-left: 3px solid #006400;">
                <div class="title" style="color:#006400;">Uang Masuk Tunai (Cash)</div>
                <div class="value" style="color:#006400;">Rp {{ number_format($tTunai, 0, ',', '.') }}</div>
            </td>
            <td style="border-left: 3px solid #b25900;">
                <div class="title" style="color:#b25900;">Uang Masuk DP (Kredit)</div>
                <div class="value" style="color:#b25900;">Rp {{ number_format($tDp, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align:center;" rowspan="2">No</th>
                <th style="width: 25%;" rowspan="2">Tipe Motor</th>
                <th colspan="3">Kuantitas Unit</th>
                <th colspan="3">Nominal Uang Masuk Penjualan</th>
            </tr>
            <tr>
                <th style="width: 7%;">Cash</th>
                <th style="width: 7%;">Kredit</th>
                <th style="width: 8%;" class="bg-dark">Total</th>
                <th style="width: 16%;">Total OTR</th>
                <th style="width: 16%;">Tunai (Cash)</th>
                <th style="width: 18%;">DP / Tanda Jadi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <b>{{ $row->nama_type }}</b><br>
                        <small style="color: #555;" class="font-mono">{{ $row->kode_tipe }}</small>
                    </td>
                    <td class="text-center font-bold">{{ $row->unit_cash }}</td>
                    <td class="text-center font-bold">{{ $row->unit_kredit }}</td>
                    <td class="text-center font-bold bg-dark">{{ $row->total_unit }}</td>
                    <td class="text-right">{{ number_format($row->total_otr, 0, ',', '.') }}</td>
                    <td class="text-right font-bold bg-emerald">{{ number_format($row->total_tunai, 0, ',', '.') }}</td>
                    <td class="text-right font-bold bg-amber">{{ number_format($row->total_dp_tanda_jadi, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data penjualan pada rentang periode ini.</td></tr>
            @endforelse
        </tbody>
        @if($salesData->count() > 0)
        <tfoot>
            <tr style="background-color: #dcdcdc; font-weight: bold; font-size: 11px;">
                <td colspan="2" class="text-right uppercase">Grand Total Keseluruhan:</td>
                <td class="text-center">{{ number_format($tCash, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($tKredit, 0, ',', '.') }}</td>
                <td class="text-center bg-dark">{{ number_format($tUnit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($tOtr, 0, ',', '.') }}</td>
                <td class="text-right bg-emerald">{{ number_format($tTunai, 0, ',', '.') }}</td>
                <td class="text-right bg-amber">{{ number_format($tDp, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
