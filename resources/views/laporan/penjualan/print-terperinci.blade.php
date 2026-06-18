<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan Terperinci</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .info { margin-bottom: 15px; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
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
        <h2>Laporan Penjualan - Rincian Terperinci</h2>
        <div>Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</div>
        <small>Waktu Cetak: {{ date('d/m/Y H:i:s') }} WIB</small>
    </div>

    <div class="info">
        <b>Jenis Dokumen:</b> {{ $jenis_dokumen == 'all' ? 'Semua (SPK & GPK)' : strtoupper($jenis_dokumen) }}
        @if($search) | <b>Kata Kunci:</b> "{{ $search }}" @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 12%;">No. Dokumen</th>
                <th style="width: 8%;">Tgl SPK</th>
                <th style="width: 18%;">Nama Pemohon & STNK</th>
                <th style="width: 20%;">Tipe Motor & Kunci</th>
                <th style="width: 9%;">Pembayaran</th>
                <th style="width: 15%;">Harga OTR</th>
                <th style="width: 15%;">DP / Tanda Jadi</th>
            </tr>
        </thead>
        <tbody>
            @php $tOtr = 0; $tTunai = 0; $tDp = 0; @endphp
            @forelse($transactions as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold text-center">{{ $row->no_spk }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td class="uppercase">
                        <b>{{ $row->nama_pemohon }}</b><br>
                        <small style="color:#555;">STNK: {{ $row->nama_stnk }}</small>
                    </td>
                    <td>
                        <b>{{ $row->motorUnit->type->nama_type ?? '-' }}</b><br>
                        <small style="color:#555;" class="font-mono">KUNCI: {{ $row->motorUnit->no_kunci ?? '-' }}</small>
                    </td>
                    <td class="text-center font-bold uppercase">{{ $row->jenis_pembayaran }}</td>
                    <td class="text-right">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">
                        @if(in_array($row->jenis_pembayaran, ['Cash', 'Tunai']))
                            -
                        @else
                            {{ number_format($row->uang_muka + $row->tanda_jadi, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
                @php
                    $tOtr += $row->harga_otr;
                    if(in_array($row->jenis_pembayaran, ['Cash', 'Tunai'])) {
                        $tTunai += $row->harga_otr;
                    } else {
                        $tDp += ($row->uang_muka + $row->tanda_jadi);
                    }
                @endphp
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada baris transaksi penjualan pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot style="font-weight: bold; background-color: #f9f9f9;">
            <tr>
                <td colspan="6" class="text-right" style="text-transform: uppercase; background-color: #f2f2f2;">Total Nilai OTR (Semua Unit):</td>
                <td class="text-right" style="font-size: 11px;">{{ number_format($tOtr, 0, ',', '.') }}</td>
                <td style="background-color: #f2f2f2;"></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right" style="text-transform: uppercase; color: #006400;">Total Uang Masuk dari Cash:</td>
                <td class="text-right" style="color: #006400; font-size: 11px;">{{ number_format($tTunai, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right" style="text-transform: uppercase; color: #b25900;">Total Uang Masuk dari Kredit (DP):</td>
                <td class="text-center" style="color: #888; font-weight: normal; italic;">-</td>
                <td class="text-right" style="color: #b25900; font-size: 11px; background-color: #fff9e6;">{{ number_format($tDp, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
