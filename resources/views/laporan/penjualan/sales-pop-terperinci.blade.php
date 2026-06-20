@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan Sales / POP - Terperinci
            </h2>
        </div>
        <a href="{{ route('laporan.penjualan.sales-pop-terperinci.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.sales-pop-terperinci') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Sales / POP</label>
                <select name="sales_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white">
                    <option value="">Semua Sales / POP</option>
                    @foreach($salesList as $s)
                        <option value="{{ $s->id }}" {{ $sales_id == $s->id ? 'selected' : '' }}>{{ $s->nama_sales }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 h-[38px]">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap text-[11px]">
                <thead class="bg-honda-red text-white uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3 border-r border-red-500">Kode Tipe</th>
                        <th class="py-3 px-3 border-r border-red-500">Nama Tipe</th>
                        <th class="py-3 px-3 border-r border-red-500">No. Bukti (SJK)</th>
                        <th class="py-3 px-3 border-r border-red-500">Tgl Bukti</th>
                        <th class="py-3 px-3 border-r border-red-500">Nama Pemohon</th>
                        <th class="py-3 px-3 border-r border-red-500">Leasing</th>
                        <th class="py-3 px-3 border-r border-red-500">Mediator</th>
                        <th class="py-3 px-3 border-r border-red-500 text-right">MD Fee</th>
                        <th class="py-3 px-3 border-r border-red-500 text-right">Discount</th>
                        <th class="py-3 px-3 border-r border-red-500 text-center">Unit</th>
                        <th class="py-3 px-3 border-r border-red-500 text-right">OTR</th>
                        <th class="py-3 px-3 border-r border-red-500 text-right">Uang Muka</th>
                        <th class="py-3 px-3 text-right bg-gray-900">Tanda Jadi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php 
                        $gUnit = 0; $gOtr = 0; $gUm = 0; $gTj = 0; $gMd = 0; $gDisc = 0;
                    @endphp

                    @forelse($reports as $salesName => $items)
                        <tr class="bg-gray-100"><td colspan="13" class="py-3 px-4 font-black uppercase text-gray-800">Sales / POP : {{ $salesName }}</td></tr>
                        @php 
                            $sUnit = 0; $sOtr = 0; $sUm = 0; $sTj = 0; $sMd = 0; $sDisc = 0;
                        @endphp
                        @foreach($items as $row)
                            @php
                                $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                                $umTj = $isCash ? $row->harga_otr : $row->uang_muka;
                                $mdFee = $row->kontrolHarga->mediator_fee ?? 0;
                                $discount = $row->kontrolHarga->discount ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 border-r border-gray-100 font-mono font-bold">{{ $row->motorUnit->type->kode_tipe ?? '-' }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100">{{ $row->motorUnit->type->nama_type ?? '-' }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 font-mono">{{ $row->suratJalan->no_bukti ?? '-' }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100">{{ $row->suratJalan ? \Carbon\Carbon::parse($row->suratJalan->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 font-bold uppercase">{{ $row->nama_pemohon }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100">{{ $isCash ? 'CASH' : ($row->leasing->nama_leasing ?? '-') }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100">{{ $row->kontrolHarga->nama_mediator ?? '-' }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 text-right">{{ number_format($mdFee, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 text-right text-red-500">{{ number_format($discount, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 text-center font-bold text-blue-600">1</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 text-right">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 border-r border-gray-100 text-right">{{ number_format($umTj, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 bg-gray-50/50 text-right">{{ number_format($umTj, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $sUnit += 1; $sOtr += $row->harga_otr; $sUm += $umTj; $sTj += $umTj; $sMd += $mdFee; $sDisc += $discount;
                            @endphp
                        @endforeach
                        <tr class="bg-gray-50 font-bold border-y border-gray-200">
                            <td colspan="7" class="py-3 px-3 text-right uppercase">Sub Total {{ $salesName }} :</td>
                            <td class="py-3 px-3 text-right">{{ number_format($sMd, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right text-red-500">{{ number_format($sDisc, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center text-blue-700">{{ $sUnit }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($sOtr, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($sUm, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right bg-gray-100">{{ number_format($sTj, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $gUnit += $sUnit; $gOtr += $sOtr; $gUm += $sUm; $gTj += $sTj; $gMd += $sMd; $gDisc += $sDisc;
                        @endphp
                    @empty
                        <tr><td colspan="13" class="py-12 text-center text-gray-400 italic">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-800 text-white font-black border-t-2 border-gray-900">
                    <tr>
                        <td colspan="7" class="py-4 px-3 text-right uppercase bg-gray-900 border-r border-gray-700">Grand Total :</td>
                        <td class="py-4 px-3 text-right border-r border-gray-700">{{ number_format($gMd, 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right border-r border-gray-700 text-red-400">{{ number_format($gDisc, 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-center border-r border-gray-700 bg-gray-900 text-yellow-400">{{ $gUnit }}</td>
                        <td class="py-4 px-3 text-right border-r border-gray-700">{{ number_format($gOtr, 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right border-r border-gray-700">{{ number_format($gUm, 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right bg-gray-950 text-yellow-400">{{ number_format($gTj, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection