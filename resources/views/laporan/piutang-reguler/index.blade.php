@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto" x-data="{ jenisLaporan: '{{ $jenis_laporan }}' }">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Keuangan Konsumen (Reguler)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Manajemen piutang, kontrol bayar, dan pemantauan kas masuk dealer.</p>
        </div>
        <a href="{{ route('laporan.piutang-reguler.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.piutang-reguler.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 font-semibold whitespace-nowrap">Tgl SPK:</span>
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>
            
            @if(!$isAdminGp)
            <div class="w-40">
                <select name="lokasi_spk" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full font-medium text-gray-700">
                    <option value="semua" {{ $lokasi_spk == 'semua' ? 'selected' : '' }}>Semua Cabang</option>
                    <option value="pusat" {{ $lokasi_spk == 'pusat' ? 'selected' : '' }}>Pusat (SPK)</option>
                    <option value="gp" {{ $lokasi_spk == 'gp' ? 'selected' : '' }}>GP (GPK)</option>
                </select>
            </div>
            @endif

            <div class="w-72">
                <select name="jenis_laporan" x-model="jenisLaporan" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full font-medium">
                    <option value="piutang_konsumen">Laporan Piutang Konsumen</option>
                    <option value="pembayaran">Laporan Pembayaran</option>
                    <option value="pembayaran_transfer">Laporan Pembayaran Transfer</option>
                    <option value="refund_transfer">Refund Transfer</option>
                    <option value="kwitansi_lain">Laporan Kwitansi Lain-lain</option>
                </select>
            </div>

            <div class="w-40" x-show="jenisLaporan === 'pembayaran'" style="display: none;">
                <select name="format_laporan" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full font-medium">
                    <option value="standar" {{ $format_laporan == 'standar' ? 'selected' : '' }}>Format Standar</option>
                    <option value="lengkap" {{ $format_laporan == 'lengkap' ? 'selected' : '' }}>Format Lengkap</option>
                </select>
            </div>

            <button type="submit" class="bg-honda-red text-white font-semibold px-6 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                Tampilkan
            </button>
        </form>
    </div>

    @if($jenis_laporan === 'piutang_konsumen')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-4 px-4 text-center border-r border-gray-100">No</th>
                        <th class="py-4 px-4 border-r border-gray-100">Nama Konsumen</th>
                        <th class="py-4 px-4 border-r border-gray-100">Tipe Motor</th>
                        <th class="py-4 px-4 border-r border-gray-100">Alamat (RT/RW)</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">No. SPK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">Tgl SPK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">No. SJK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">Tgl SJK</th>
                        <th class="py-4 px-4 border-r border-gray-100">Sales</th>
                        <th class="py-4 px-4 border-r border-gray-100">Leasing</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">DP / OTR Netto</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Nilai Piutang</th>
                        <th class="py-4 px-4 text-center">Tenggat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php $sumUM = 0; $sumPiutang = 0; @endphp
                    @forelse($data as $index => $row)
                        @php $sumUM += $row->uang_muka_netto; $sumPiutang += $row->nilai_piutang; @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center text-gray-400 font-mono border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-gray-800 border-r border-gray-100">{{ $row->nama_konsumen }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700 border-r border-gray-100">{{ $row->tipe_motor }}</td>
                            <td class="py-3.5 px-4 text-gray-600 border-r border-gray-100 whitespace-normal min-w-[150px]">{{ $row->alamat_lengkap }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-gray-700 font-mono border-r border-gray-100">{{ $row->no_spk }}</td>
                            <td class="py-3.5 px-4 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-700 font-mono border-r border-gray-100">{{ $row->no_sjk }}</td>
                            <td class="py-3.5 px-4 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 text-gray-600 border-r border-gray-100">{{ $row->sales }}</td>
                            <td class="py-3.5 px-4 border-r border-gray-100">
                                <span class="px-2 py-1 rounded-md text-xs font-bold {{ $row->leasing == 'KONTAN' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">{{ $row->leasing }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-gray-700 border-r border-gray-100">Rp {{ number_format($row->uang_muka_netto, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-honda-red border-r border-gray-100">Rp {{ number_format($row->nilai_piutang, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center font-bold"><span class="text-red-600 font-mono">{{ $row->tenggat }} Hari</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="py-8 text-center text-gray-500 italic">Tidak ada data piutang konsumen.</td></tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900 text-sm">
                    <tr>
                        <td colspan="10" class="py-4 px-4 text-right uppercase tracking-wider">Total Keseluruhan</td>
                        <td class="py-4 px-4 text-right">Rp {{ number_format($sumUM, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right text-honda-red">Rp {{ number_format($sumPiutang, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @elseif($jenis_laporan === 'pembayaran_transfer')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-4 px-4 text-center border-r border-gray-100 w-16">No</th>
                        <th class="py-4 px-4 border-r border-gray-100">Atas Nama</th>
                        <th class="py-4 px-4 border-r border-gray-100">Tipe Motor</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Harga</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Discount</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">DP Murni</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Sisa</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100 text-green-700">Kontan</th>
                        <th class="py-4 px-4 text-right text-blue-700">Transfer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php $tH=0; $tD=0; $tDp=0; $tS=0; $tK=0; $tT=0; @endphp
                    @forelse($data as $index => $row)
                        @php $tH+=$row->harga_otr; $tD+=$row->discount; $tDp+=$row->dp_murni; $tS+=$row->sisa; $tK+=$row->kontan; $tT+=$row->transfer; @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center text-gray-400 font-mono border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-gray-800 border-r border-gray-100">{{ $row->nama_konsumen }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700 border-r border-gray-100">{{ $row->tipe_motor }}</td>
                            <td class="py-3.5 px-4 text-right font-mono border-r border-gray-100">Rp {{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-red-500 border-r border-gray-100">Rp {{ number_format($row->discount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono border-r border-gray-100">Rp {{ number_format($row->dp_murni, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-honda-red border-r border-gray-100">Rp {{ number_format($row->sisa, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-medium text-green-600 border-r border-gray-100">Rp {{ number_format($row->kontan, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-blue-600">Rp {{ number_format($row->transfer, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-8 text-center text-gray-500 italic">Tidak ada data pembayaran transfer pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900 text-sm">
                    <tr>
                        <td colspan="3" class="py-4 px-4 text-right uppercase tracking-wider">Total Rekap</td>
                        <td class="py-4 px-4 text-right font-mono">Rp {{ number_format($tH, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-mono text-red-500">Rp {{ number_format($tD, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-mono">Rp {{ number_format($tDp, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-mono text-honda-red">Rp {{ number_format($tS, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-mono text-green-600">Rp {{ number_format($tK, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-mono text-blue-600">Rp {{ number_format($tT, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @elseif($jenis_laporan === 'refund_transfer')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6 max-w-4xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-4 px-4 text-center border-r border-gray-100 w-16">No</th>
                        <th class="py-4 px-4 border-r border-gray-100">Atas Nama</th>
                        <th class="py-4 px-4 border-r border-gray-100">Tipe Motor</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100 w-48">RefundTrf</th>
                        <th class="py-4 px-4 border-r border-gray-100">Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php $sumRefund = 0; @endphp
                    @forelse($data as $index => $row)
                        @php $sumRefund += $row->refund_trf; @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center text-gray-400 font-mono border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-gray-800 border-r border-gray-100">{{ $row->nama_konsumen }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700 border-r border-gray-100">{{ $row->tipe_motor }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-orange-600 border-r border-gray-100">Rp {{ number_format($row->refund_trf, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-gray-600 border-r border-gray-100">{{ $row->sales }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-500 italic">Tidak ada data refund transfer.</td></tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900 text-sm">
                    <tr>
                        <td colspan="3" class="py-4 px-4 text-right uppercase tracking-wider">Total Refund Transfer</td>
                        <td class="py-4 px-4 text-right text-orange-600">Rp {{ number_format($sumRefund, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @elseif($jenis_laporan === 'pembayaran')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    @if($format_laporan === 'lengkap')
                    <tr class="bg-slate-100 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-800 font-bold">
                        <th colspan="14" class="py-3 px-4 text-center border-r border-gray-300">TERIMA</th>
                        <th colspan="6" class="py-3 px-4 text-center border-r border-gray-300 bg-blue-50/50">SUBSIDI</th>
                        <th colspan="3" class="py-3 px-4 text-center border-r border-gray-300 bg-orange-50/50">TANGGAL</th>
                        <th colspan="2" class="py-3 px-4 text-center bg-green-50/50">NAMA</th>
                    </tr>
                    @endif
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-3 px-3 text-center border-r border-gray-100">No</th>
                        <th class="py-3 px-3 border-r border-gray-100">Atas Nama</th>
                        <th class="py-3 px-3 border-r border-gray-100">Tipe Motor</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Harga</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Discount</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Refund</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">DP Murni</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Sisa</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Kontan</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Transfer</th>
                        <th class="py-3 px-3 text-center border-r border-gray-100">Rekening</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">MD Fee</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100">Setor</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100 {{ $format_laporan == 'lengkap' ? 'border-r-gray-300' : '' }}">Tambahan</th>
                        
                        @if($format_laporan === 'lengkap')
                        <th class="py-3 px-3 text-center border-r border-gray-100 bg-blue-50/20">Leasing</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100 bg-blue-50/20">AHM</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100 bg-blue-50/20">MDealer</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100 bg-blue-50/20">Leasing</th>
                        <th class="py-3 px-3 text-right border-r border-gray-100 bg-blue-50/20">DLL</th>
                        <th class="py-3 px-3 text-right border-r border-gray-300 bg-blue-50/20">Dealer</th>
                        
                        <th class="py-3 px-3 text-center border-r border-gray-100 bg-orange-50/20">SPK</th>
                        <th class="py-3 px-3 text-center border-r border-gray-100 bg-orange-50/20">SJK</th>
                        <th class="py-3 px-3 text-center border-r border-gray-300 bg-orange-50/20">No. Kunci</th>

                        <th class="py-3 px-3 border-r border-gray-100 bg-green-50/20">Sales</th>
                        <th class="py-3 px-3 bg-green-50/20">Mediator</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php 
                        $tHarga=0; $tDisc=0; $tRef=0; $tDPM=0; $tSisa=0; $tKon=0; $tTrf=0; $tMDF=0; $tSet=0; $tTamb=0;
                        $tAHM=0; $tMdl=0; $tLs=0; $tDLL=0; $tDlr=0;
                    @endphp
                    @forelse($data as $index => $row)
                        @php 
                            $tHarga+=$row->harga_otr; $tDisc+=$row->discount; $tRef+=$row->refund; $tDPM+=$row->dp_murni; 
                            $tSisa+=$row->sisa; $tKon+=$row->kontan; $tTrf+=$row->transfer; $tMDF+=$row->md_fee; 
                            $tSet+=$row->setor; $tTamb+=$row->tambahan;
                            if($format_laporan === 'lengkap') {
                                $tAHM+=$row->subsidi_ahm; $tMdl+=$row->subsidi_mdealer; $tLs+=$row->subsidi_leasing; 
                                $tDLL+=$row->subsidi_dll; $tDlr+=$row->subsidi_dealer;
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors text-xs">
                            <td class="py-3 px-3 text-center text-gray-400 font-mono border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3 px-3 font-bold text-gray-800 border-r border-gray-100">{{ $row->nama_konsumen }}</td>
                            <td class="py-3 px-3 font-semibold text-gray-700 border-r border-gray-100">{{ $row->tipe_motor }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono text-red-500 border-r border-gray-100">{{ number_format($row->discount, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono text-orange-500 border-r border-gray-100">{{ number_format($row->refund, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-gray-800 border-r border-gray-100">{{ number_format($row->dp_murni, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono text-honda-red border-r border-gray-100">{{ number_format($row->sisa, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono text-green-600 border-r border-gray-100">{{ number_format($row->kontan, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono text-blue-600 border-r border-gray-100">{{ number_format($row->transfer, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-100">{{ $row->rekening }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->md_fee, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-green-700 border-r border-gray-100">{{ number_format($row->setor, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100 {{ $format_laporan == 'lengkap' ? 'border-r-gray-300' : '' }}">{{ number_format($row->tambahan, 0, ',', '.') }}</td>
                            
                            @if($format_laporan === 'lengkap')
                            <td class="py-3 px-3 text-center font-bold text-blue-700 border-r border-gray-100">{{ $row->subsidi_leasing_nama }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->subsidi_ahm, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->subsidi_mdealer, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->subsidi_leasing, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono border-r border-gray-100">{{ number_format($row->subsidi_dll, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono style="border-right: 2px solid #000;">{{ number_format($row->subsidi_dealer, 0, ',', '.') }}</td>
                            
                            <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/Y') }}</td>
                            <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/Y') }}</td>
                            <td class="py-3 px-3 text-center font-mono font-bold border-r border-gray-300">{{ $row->no_kunci }}</td>

                            <td class="py-3 px-3 text-gray-600 border-r border-gray-100">{{ $row->sales }}</td>
                            <td class="py-3 px-3 text-gray-600">{{ $row->mediator }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $format_laporan === 'lengkap' ? 25 : 14 }}" class="py-8 text-center text-gray-500 italic">Tidak ada data pembayaran.</td></tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900 text-xs">
                    <tr>
                        <td colspan="3" class="py-3 px-3 text-right uppercase tracking-wider">TOTAL</td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tHarga, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-red-500">Rp {{ number_format($tDisc, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-orange-500">Rp {{ number_format($tRef, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tDPM, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-honda-red">Rp {{ number_format($tSisa, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-green-600">Rp {{ number_format($tKon, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-blue-600">Rp {{ number_format($tTrf, 0, ',', '.') }}</td>
                        <td class="border-r border-gray-100"></td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tMDF, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono text-green-700">Rp {{ number_format($tSet, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono border-r {{ $format_laporan == 'lengkap' ? 'border-gray-300' : 'border-gray-100' }}">Rp {{ number_format($tTamb, 0, ',', '.') }}</td>
                        
                        @if($format_laporan === 'lengkap')
                        <td class="border-r border-gray-100"></td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tAHM, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tMdl, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tLs, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($tDLL, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right font-mono border-r border-gray-300">Rp {{ number_format($tDlr, 0, ',', '.') }}</td>
                        <td colspan="5"></td>
                        @endif
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    
    @else
    <div class="bg-white p-12 text-center rounded-2xl border border-dashed border-gray-300 text-gray-400 italic">
        Laporan belum diimplementasikan.
    </div>
    @endif
</div>
@endsection