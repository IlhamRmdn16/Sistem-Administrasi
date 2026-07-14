<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan - {{ strtoupper(str_replace('_', ' ', $jenis_laporan)) }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0 0 4px 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 0; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px 3px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">
    <div class="header">
        @php
            $judulLokasi = $lokasi_spk == 'gp' ? 'GP (GPK)' : ($lokasi_spk == 'pusat' ? 'PUSAT (SPK)' : 'SEMUA CABANG / LOKASI');
            $judulTipe = strtoupper(str_replace('_', ' ', $jenis_laporan));
            if($jenis_laporan == 'pembayaran') $judulTipe .= ' (FORMAT ' . strtoupper($format_laporan) . ')';
        @endphp
        <h2>LAPORAN {{ $judulTipe }} - {{ $judulLokasi }}</h2>
        <p>PERIODE TGL SPK: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}</p>
    </div>

    @if($jenis_laporan === 'piutang_konsumen')
        <table>
            <thead>
                <tr>
                    <th style="width: 20px;">NO</th>
                    <th>NAMA KONSUMEN</th>
                    <th style="width: 80px;">TIPE MOTOR</th>
                    <th>ALAMAT / RT-RW</th>
                    <th style="width: 60px;">NO. SPK</th>
                    <th style="width: 55px;">TGL SPK</th>
                    <th style="width: 60px;">NO. SJK</th>
                    <th style="width: 55px;">TGL SJK</th>
                    <th style="width: 60px;">SALES</th>
                    <th style="width: 60px;">LEASING</th>
                    <th style="width: 75px;">UM / OTR NETTO</th>
                    <th style="width: 75px;">NILAI PIUTANG</th>
                    <th style="width: 40px;">TENGGAT</th>
                </tr>
            </thead>
            <tbody>
                @php $tUM=0; $tPiu=0; @endphp
                @foreach($data as $index => $row)
                    @php $tUM += $row->uang_muka_netto; $tPiu += $row->nilai_piutang; @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ strtoupper($row->nama_konsumen) }}</td>
                        <td>{{ $row->tipe_motor }}</td>
                        <td>{{ $row->alamat_lengkap }}</td>
                        <td class="text-center font-mono">{{ $row->no_spk }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/Y') }}</td>
                        <td class="text-center font-mono">{{ $row->no_sjk }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/Y') }}</td>
                        <td>{{ $row->sales }}</td>
                        <td class="text-center">{{ $row->leasing }}</td>
                        <td class="text-right font-mono">{{ number_format($row->uang_muka_netto, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->nilai_piutang, 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $row->tenggat }} HARI</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="10" class="text-right">TOTAL REKAPITULASI:</td>
                    <td class="text-right font-mono">{{ number_format($tUM, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tPiu, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

    @can('akses-laporan-piutang-reguler')
    @elseif($jenis_laporan === 'pembayaran_transfer')
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>ATAS NAMA</th>
                    <th>TIPE MOTOR</th>
                    <th style="width: 90px;">HARGA</th>
                    <th style="width: 90px;">DISCOUNT</th>
                    <th style="width: 90px;">DP MURNI</th>
                    <th style="width: 90px;">SISA</th>
                    <th style="width: 90px;">KONTAN</th>
                    <th style="width: 90px;">TRANSFER</th>
                </tr>
            </thead>
            <tbody>
                @php $tH=0; $tD=0; $tDp=0; $tS=0; $tK=0; $tT=0; @endphp
                @foreach($data as $index => $row)
                    @php $tH+=$row->harga_otr; $tD+=$row->discount; $tDp+=$row->dp_murni; $tS+=$row->sisa; $tK+=$row->kontan; $tT+=$row->transfer; @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ strtoupper($row->nama_konsumen) }}</td>
                        <td>{{ $row->tipe_motor }}</td>
                        <td class="text-right font-mono">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                        <td class="text-right font-mono text-red-600">{{ number_format($row->discount, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->dp_murni, 0, ',', '.') }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->sisa, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->kontan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->transfer, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL:</td>
                    <td class="text-right font-mono">{{ number_format($tH, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tD, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tDp, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tS, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tK, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tT, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($jenis_laporan === 'refund_transfer')
        <table style="width: 70%; margin: 0 auto;">
            <thead>
                <tr>
                    <th style="width: 30px;">NO</th>
                    <th>ATAS NAMA</th>
                    <th>TIPE MOTOR</th>
                    <th style="width: 120px;">REFUND TRF</th>
                    <th style="width: 120px;">SALES</th>
                </tr>
            </thead>
            <tbody>
                @php $tRefTrf = 0; @endphp
                @foreach($data as $index => $row)
                    @php $tRefTrf += $row->refund_trf; @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ strtoupper($row->nama_konsumen) }}</td>
                        <td>{{ $row->tipe_motor }}</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($row->refund_trf, 0, ',', '.') }}</td>
                        <td>{{ $row->sales }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL REFUND TRANSFER:</td>
                    <td class="text-right font-mono">Rp {{ number_format($tRefTrf, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

    @elseif($jenis_laporan === 'pembayaran')
        <table>
            <thead>
                @if($format_laporan === 'lengkap')
                <tr>
                    <th colspan="14" style="border-right: 2px solid #000;">TERIMA</th>
                    <th colspan="6" style="border-right: 2px solid #000; background-color: #e6f2ff;">SUBSIDI</th>
                    <th colspan="3" style="border-right: 2px solid #000; background-color: #fff0e6;">TANGGAL</th>
                    <th colspan="2" style="background-color: #e6ffe6;">NAMA</th>
                </tr>
                @endif
                <tr>
                    <th style="width: 15px;">NO</th>
                    <th>ATAS NAMA</th>
                    <th>TIPE MOTOR</th>
                    <th style="width: 45px;">HARGA</th>
                    <th style="width: 45px;">DISC</th>
                    <th style="width: 45px;">REFUND</th>
                    <th style="width: 45px;">DP MURNI</th>
                    <th style="width: 45px;">SISA</th>
                    <th style="width: 45px;">KONTAN</th>
                    <th style="width: 45px;">TRANSFER</th>
                    <th style="width: 40px;">REKENING</th>
                    <th style="width: 45px;">MD FEE</th>
                    <th style="width: 45px;">SETOR</th>
                    <th style="width: 45px; {{ $format_laporan == 'lengkap' ? 'border-right: 2px solid #000;' : '' }}">TAMBAH</th>
                    
                    @if($format_laporan === 'lengkap')
                    <th style="width: 45px; background-color: #f0f8ff;">LEASING</th>
                    <th style="width: 40px; background-color: #f0f8ff;">AHM</th>
                    <th style="width: 40px; background-color: #f0f8ff;">MD</th>
                    <th style="width: 40px; background-color: #f0f8ff;">LSG</th>
                    <th style="width: 40px; background-color: #f0f8ff;">DLL</th>
                    <th style="width: 40px; background-color: #f0f8ff; border-right: 2px solid #000;">DLR</th>
                    
                    <th style="width: 45px; background-color: #fff8f0;">SPK</th>
                    <th style="width: 45px; background-color: #fff8f0;">SJK</th>
                    <th style="width: 45px; background-color: #fff8f0; border-right: 2px solid #000;">KUNCI</th>

                    <th style="width: 50px; background-color: #f0fff0;">SALES</th>
                    <th style="width: 50px; background-color: #f0fff0;">MEDIATOR</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php 
                    $tHrg=0; $tDsc=0; $tRef=0; $tDPM=0; $tSis=0; $tKon=0; $tTrf=0; $tMDF=0; $tSet=0; $tTam=0;
                    $tAHM=0; $tMdl=0; $tLs=0; $tDLL=0; $tDlr=0;
                @endphp
                @foreach($data as $index => $row)
                    @php 
                        $tHrg+=$row->harga_otr; $tDsc+=$row->discount; $tRef+=$row->refund; $tDPM+=$row->dp_murni; 
                        $tSis+=$row->sisa; $tKon+=$row->kontan; $tTrf+=$row->transfer; $tMDF+=$row->md_fee; 
                        $tSet+=$row->setor; $tTam+=$row->tambahan;
                        if($format_laporan === 'lengkap') {
                            $tAHM+=$row->subsidi_ahm; $tMdl+=$row->subsidi_mdealer; $tLs+=$row->subsidi_leasing; 
                            $tDLL+=$row->subsidi_dll; $tDlr+=$row->subsidi_dealer;
                        }
                    @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $index + 1 }}</td>
                        <td class="font-bold" style="font-size: 8px;">{{ strtoupper($row->nama_konsumen) }}</td>
                        <td style="font-size: 8px;">{{ $row->tipe_motor }}</td>
                        <td class="text-right font-mono">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->discount, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->refund, 0, ',', '.') }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->dp_murni, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->sisa, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->kontan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->transfer, 0, ',', '.') }}</td>
                        <td class="text-center" style="font-size: 7px;">{{ $row->rekening }}</td>
                        <td class="text-right font-mono">{{ number_format($row->md_fee, 0, ',', '.') }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->setor, 0, ',', '.') }}</td>
                        <td class="text-right font-mono {{ $format_laporan == 'lengkap' ? 'border-right: 2px solid #000;' : '' }}">{{ number_format($row->tambahan, 0, ',', '.') }}</td>
                        
                        @if($format_laporan === 'lengkap')
                        <td class="text-center font-bold" style="font-size: 7px;">{{ $row->subsidi_leasing_nama }}</td>
                        <td class="text-right font-mono">{{ number_format($row->subsidi_ahm, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->subsidi_mdealer, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->subsidi_leasing, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ number_format($row->subsidi_dll, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="border-right: 2px solid #000;">{{ number_format($row->subsidi_dealer, 0, ',', '.') }}</td>
                        
                        <td class="text-center" style="font-size: 7.5px;">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/y') }}</td>
                        <td class="text-center" style="font-size: 7.5px;">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/y') }}</td>
                        <td class="text-center font-mono font-bold" style="font-size: 7.5px; border-right: 2px solid #000;">{{ $row->no_kunci }}</td>

                        <td style="font-size: 7.5px;">{{ $row->sales }}</td>
                        <td style="font-size: 7.5px;">{{ $row->mediator }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL:</td>
                    <td class="text-right font-mono">{{ number_format($tHrg, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tDsc, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tRef, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tDPM, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tSis, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tKon, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tTrf, 0, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right font-mono">{{ number_format($tMDF, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tSet, 0, ',', '.') }}</td>
                    <td class="text-right font-mono {{ $format_laporan == 'lengkap' ? 'border-right: 2px solid #000;' : '' }}">{{ number_format($tTam, 0, ',', '.') }}</td>
                    
                    @if($format_laporan === 'lengkap')
                    <td></td>
                    <td class="text-right font-mono">{{ number_format($tAHM, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tMdl, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tLs, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($tDLL, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="border-right: 2px solid #000;">{{ number_format($tDlr, 0, ',', '.') }}</td>
                    <td colspan="5"></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    @endif
    @endcan
</body>
</html>