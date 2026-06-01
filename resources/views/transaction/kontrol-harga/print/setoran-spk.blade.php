<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SETORAN SPK - {{ $spk->nama_stnk }}</title>
    <style>
        /* Ukuran Murni 1/3 F4 Vertikal / Portrait (Lebar 110mm, Tinggi 215mm) */
        @page { size: 110mm 215mm portrait; margin: 4mm 6mm; }

        body { font-family: 'Arial', sans-serif; font-size: 9.5px; margin: 0; padding: 0; color: #000; line-height: 1.3; }

        /* Tabel Utama pembagi Kiri & Kanan secara transparan */
        .main-container-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-container-table > tbody > tr > td { vertical-align: top; width: 50%; padding: 0; border: none; }

        /* Tabel Konten di dalam masing-masing kolom */
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { padding: 1.5px 0; border: none; vertical-align: top; text-align: left; }

        /* Pengaturan lebar sub-kolom internal */
        .col-label { width: 42%; text-align: left; }
        .col-colon { width: 6%; text-align: left; }
        .col-value { width: 52%; text-align: left; }

        .uppercase { text-transform: uppercase; }

        /* Spacer vertikal dinamis */
        .vertical-spacer { height: 12px; }
    </style>
</head>
<body onload="window.print()">

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

        $sisa = $murni - $bayar;
        $sisa = $sisa > 0 ? $sisa : 0;
    @endphp

    <table class="main-container-table">
        <tr>
            <td style="padding-right: 6px;">
                <table class="content-table">
                    <tr>
                        <td class="col-label">Tanggal</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Konsumen</td>
                        <td class="col-colon">:</td>
                        <td class="col-value uppercase">{{ $spk->nama_stnk }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Tipe Motor</td>
                        <td class="col-colon">:</td>
                        <td class="col-value uppercase">{{ $spk->motorUnit->type->nama_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Leasing</td>
                        <td class="col-colon">:</td>
                        <td class="col-value uppercase">{{ $isKredit ? ($spk->leasing->nama_leasing ?? '-') : 'KONTAN' }}</td>
                    </tr>

                    <tr><td colspan="3"><div class="vertical-spacer"></div><div class="vertical-spacer"></div></td></tr>

                    @if($nilaiTransfer > 0)
                    <tr>
                        <td class="col-label">Rekening</td>
                        <td class="col-colon">:</td>
                        <td class="col-value uppercase">{{ $rekeningList }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Nilai Transfer</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($nilaiTransfer, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr><td colspan="3"><div class="vertical-spacer"></div></td></tr>

                    @if($refund > 0)
                    <tr>
                        <td class="col-label">RefundTransfer</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($refund, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </td>

            <td style="padding-left: 6px;">
                <table class="content-table">
                    <tr>
                        <td class="col-label">{{ $labelAwal }}</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($awal, 0, ',', '.') }}</td>
                    </tr>

                    @if($subAhm > 0)
                    <tr>
                        <td class="col-label">Sub AHM</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($subAhm, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @if($subMain > 0)
                    <tr>
                        <td class="col-label">Sub MAIN</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($subMain, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @if($subDealer > 0)
                    <tr>
                        <td class="col-label">Sub DEALER</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($subDealer, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @if($subLeasing > 0)
                    <tr>
                        <td class="col-label">Sub LEASE</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($subLeasing, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td class="col-label">{{ $labelMurni }}</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($murni, 0, ',', '.') }}</td>
                    </tr>

                    @if($discount > 0)
                    <tr>
                        <td class="col-label">Discount</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($discount, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @if($dll > 0)
                    <tr>
                        <td class="col-label">DLL</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($dll, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td class="col-label">Bayar</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($bayar, 0, ',', '.') }}</td>
                    </tr>

                    @if(!empty($kontrol->nama_mediator) || ($kontrol->mediator_fee ?? 0) > 0)
                    <tr>
                        <td class="col-label">Mediator</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($kontrol->mediator_fee, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr><td colspan="3"><div class="vertical-spacer"></div></td></tr>

                    @if($sisa > 0)
                    <tr>
                        <td class="col-label">Sisa</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($sisa, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @if($setor > 0)
                    <tr>
                        <td class="col-label">SETOR</td>
                        <td class="col-colon">:</td>
                        <td class="col-value">{{ number_format($setor, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
