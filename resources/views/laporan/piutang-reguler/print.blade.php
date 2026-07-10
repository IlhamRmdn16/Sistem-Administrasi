<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Piutang Konsumen Reguler</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0 0 4px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 0; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px 4px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">
    <div class="header">
        <h2>LAPORAN PIUTANG KONSUMEN REGULER - {{ $isAdminGp ? 'GUDANG POJOK (GPK)' : 'PUSAT (SPK)' }}</h2>
        <p>BERDASARKAN PERIODE TANGGAL SPK: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 110px;">NAMA KONSUMEN</th>
                <th>ALAMAT / RT-RW</th>
                <th style="width: 85px;">NO. SPK</th>
                <th style="width: 60px;">TGL SPK</th>
                <th style="width: 85px;">NO. SJK</th>
                <th style="width: 60px;">TGL SJK</th>
                <th style="width: 90px;">TIPE MOTOR</th>
                <th style="width: 75px;">SALES</th>
                <th style="width: 75px;">LEASING</th>
                <th style="width: 90px;">UM / OTR NETTO</th>
                <th style="width: 90px;">NILAI PIUTANG</th>
                <th style="width: 50px;">TENGGAT</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalUM = 0; 
                $totalPiutang = 0; 
            @endphp
            @forelse($data as $index => $row)
                @php 
                    $totalUM += $row->uang_muka_netto; 
                    $totalPiutang += $row->nilai_piutang; 
                @endphp
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ strtoupper($row->nama_konsumen) }}</td>
                    <td>{{ $row->alamat_lengkap }}</td>
                    <td class="text-center font-bold font-mono">{{ $row->no_spk }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/Y') }}</td>
                    <td class="text-center font-mono">{{ $row->no_sjk }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/Y') }}</td>
                    <td>{{ $row->tipe_motor }}</td>
                    <td>{{ $row->sales }}</td>
                    <td class="font-bold text-center">{{ $row->leasing }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($row->uang_muka_netto, 0, ',', '.') }}</td>
                    <td class="text-right font-bold font-mono">Rp {{ number_format($row->nilai_piutang, 0, ',', '.') }}</td>
                    <td class="text-center font-bold font-mono">{{ $row->tenggat }} HARI</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center italic" style="padding: 15px;">Tidak terdapat data tagihan piutang berjalan.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="10" class="text-right">TOTAL REKAPITULASI:</td>
                <td class="text-right font-mono">Rp {{ number_format($totalUM, 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="color: red;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>