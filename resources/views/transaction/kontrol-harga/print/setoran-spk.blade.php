<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SETORAN SPK - {{ $spk->nama_stnk }}</title>
    <style>
        /* Ukuran 1/3 F4 Portrait */
        @page { size: 110mm 215mm portrait; margin: 8mm; }

        body { font-family: 'Arial', sans-serif; font-size: 10px; margin: 0; padding: 0; color: #000; line-height: 1.4; }

        .title-main { font-size: 12px; font-weight: bold; text-align: center; text-decoration: underline; margin-bottom: 12px; text-transform: uppercase; }

        .content-table { width: 100%; border-collapse: collapse; }
        /* Kolom dibiarkan tanpa border untuk efek transparan */
        .content-table td { padding: 3px 2px; vertical-align: top; border: none; }

        .col-label { width: 45%; font-weight: bold; }
        .col-colon { width: 5%; text-align: center; }
        .col-value { width: 50%; }

        .uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .text-right { text-align: right; }

        /* Garis pembatas minimalis (horizontal line) */
        .divider td { border-bottom: 1px dashed #000; padding-bottom: 4px; margin-bottom: 4px; }
    </style>
</head>
<body onload="window.print()">

    <div class="title-main">SETORAN SPK</div>

    @php
        $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
        $labelAwal = $isKredit ? 'DP' : 'Harga OTR';
        $awal = $isKredit ? $spk->uang_muka : $spk->harga_otr;

        $discount = $kontrol->discount ?? 0;
        $murni = $awal - $discount;
        $labelMurni = $isKredit ? 'DP Murni' : 'OTR Nett';

        $subAhm = $kontrol->subsidi_ahm ?? 0;
        $subMain = $kontrol->subsidi_main_dealer ?? 0;
        $subDealer = $kontrol->subsidi_dealer ?? 0;
        $subLeasing = ($kontrol->subsidi_leasing_1 ?? 0) + ($kontrol->subsidi_leasing_2 ?? 0);
        $dll = ($kontrol->dll_1 ?? 0) + ($kontrol->dll_2 ?? 0);

        $refund = $kontrol->refund_transfer ?? 0;

        // Sisa tagihan
        $sisa = $murni - $bayar;
        $sisa = $sisa > 0 ? $sisa : 0;
    @endphp

    <table class="content-table">
        <tr>
            <td class="col-label">Tanggal Cetak</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="col-label">Konsumen</td>
            <td class="col-colon">:</td>
            <td class="col-value uppercase">{{ $spk->nama_stnk }}</td>
        </tr>
        <tr class="divider">
            <td class="col-label">Tipe Motor</td>
            <td class="col-colon">:</td>
            <td class="col-value uppercase">{{ $spk->motorUnit->type->kode_motor ?? '-' }}</td>
        </tr>

        <tr>
            <td class="col-label">{{ $isKredit ? 'Leasing' : 'Keterangan' }}</td>
            <td class="col-colon">:</td>
            <td class="col-value uppercase font-bold">{{ $isKredit ? ($spk->leasing->nama_leasing ?? '-') : 'TUNAI' }}</td>
        </tr>

        <tr>
            <td class="col-label">{{ $labelAwal }}</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right font-bold">{{ number_format($awal, 0, ',', '.') }}</td>
        </tr>

        @if($discount > 0)
        <tr>
            <td class="col-label">Discount</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($discount, 0, ',', '.') }}</td>
        </tr>
        @endif

        <tr class="divider">
            <td class="col-label">{{ $labelMurni }}</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right font-bold">{{ number_format($murni, 0, ',', '.') }}</td>
        </tr>

        @if($subAhm > 0)
        <tr>
            <td class="col-label">Sub AHM</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($subAhm, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($subMain > 0)
        <tr>
            <td class="col-label">Sub Main Dealer</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($subMain, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($subDealer > 0)
        <tr>
            <td class="col-label">Sub Dealer</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($subDealer, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($subLeasing > 0)
        <tr>
            <td class="col-label">Sub Leasing</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($subLeasing, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($dll > 0)
        <tr>
            <td class="col-label">DLL</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($dll, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if(!empty($kontrol->nama_mediator) || ($kontrol->mediator_fee ?? 0) > 0)
        <tr>
            <td class="col-label">Mediator</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">
                <div class="uppercase">{{ $kontrol->nama_mediator }}</div>
                <div>{{ number_format($kontrol->mediator_fee, 0, ',', '.') }}</div>
            </td>
        </tr>
        @endif

        @if(($subAhm + $subMain + $subDealer + $subLeasing + $dll + ($kontrol->mediator_fee ?? 0)) > 0)
        <tr class="divider"><td colspan="3"></td></tr>
        @endif

        @if($nilaiTransfer > 0)
        <tr>
            <td class="col-label">Rekening</td>
            <td class="col-colon">:</td>
            <td class="col-value uppercase">{{ $rekeningList }}</td>
        </tr>
        <tr>
            <td class="col-label">Nilai Transfer</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($nilaiTransfer, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if($refund > 0)
        <tr>
            <td class="col-label">Refund Transfer</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right">{{ number_format($refund, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if($setor > 0)
        <tr>
            <td class="col-label">Setor (Tunai Fisik)</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right font-bold">{{ number_format($setor, 0, ',', '.') }}</td>
        </tr>
        @endif

        <tr class="divider">
            <td class="col-label">TOTAL BAYAR</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right font-bold">{{ number_format($bayar, 0, ',', '.') }}</td>
        </tr>

        @if($sisa > 0)
        <tr>
            <td class="col-label">Sisa (Kekurangan)</td>
            <td class="col-colon">:</td>
            <td class="col-value text-right font-bold">{{ number_format($sisa, 0, ',', '.') }}</td>
        </tr>
        @endif

    </table>

</body>
</html>
