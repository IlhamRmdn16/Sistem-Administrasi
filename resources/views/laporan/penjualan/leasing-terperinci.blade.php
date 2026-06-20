@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan Leasing - Terperinci
            </h2>
        </div>
        <a href="{{ route('laporan.penjualan.leasing-terperinci.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.leasing-terperinci') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Leasing</label>
                <select name="leasing_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white">
                    <option value="">Semua Leasing</option>
                    @foreach($leasingList as $l)
                        <option value="{{ $l->id }}" {{ $leasing_id == $l->id ? 'selected' : '' }}>{{ $l->nama_leasing }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 h-[38px]">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                <thead class="bg-honda-red text-white uppercase tracking-wider text-xs">
                    <tr>
                        <th class="py-3 px-4 border-r border-red-500">Tgl. SPK</th>
                        <th class="py-3 px-4 border-r border-red-500">Nama Pemohon</th>
                        <th class="py-3 px-4 border-r border-red-500">Nama STNK</th>
                        <th class="py-3 px-4 border-r border-red-500 font-mono">No. Kunci</th>
                        <th class="py-3 px-4 border-r border-red-500">Nama Tipe</th>
                        <th class="py-3 px-4 text-right bg-gray-900">Uang Muka</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php 
                        $grandUm = 0;
                        $grandUnit = 0;
                    @endphp
                    @forelse($reports as $leasingName => $items)
                        <tr class="bg-gray-100"><td colspan="6" class="py-3 px-4 font-black uppercase text-gray-800">{{ $leasingName }}</td></tr>
                        @php 
                            $subUm = 0;
                            $subUnit = 0;
                        @endphp
                        @foreach($items as $row)
                            @php
                                $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                                $nominalUm = $isCash ? $row->harga_otr : $row->uang_muka;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-4 border-r border-gray-100 font-mono">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-4 border-r border-gray-100 font-bold uppercase">{{ $row->nama_pemohon }}</td>
                                <td class="py-2.5 px-4 border-r border-gray-100 uppercase text-gray-600">{{ $row->nama_stnk }}</td>
                                <td class="py-2.5 px-4 border-r border-gray-100 font-mono font-bold text-center">{{ $row->motorUnit->no_kunci ?? '-' }}</td>
                                <td class="py-2.5 px-4 border-r border-gray-100">{{ $row->motorUnit->type->nama_type ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-right font-semibold text-gray-800 bg-gray-50/20">{{ number_format($nominalUm, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $subUm += $nominalUm;
                                $subUnit += 1;
                            @endphp
                        @endforeach
                        <tr class="bg-gray-50 font-bold border-y border-gray-200">
                            <td colspan="3" class="py-3 px-4 uppercase text-xs text-blue-700">Total : {{ $subUnit }} Unit</td>
                            <td colspan="2" class="py-3 px-4 text-right uppercase text-xs">Sub Total Uang Muka {{ $leasingName }} :</td>
                            <td class="py-3 px-4 text-right bg-gray-100 text-amber-700">{{ number_format($subUm, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $grandUm += $subUm;
                            $grandUnit += $subUnit;
                        @endphp
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-400 italic">Tidak ada rincian data transaksi leasing untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-800 text-white font-black border-t-2 border-gray-900">
                    <tr>
                        <td colspan="3" class="py-4 px-4 uppercase bg-gray-900 border-r border-gray-700 text-yellow-400">Total Keseluruhan : {{ $grandUnit }} Unit</td>
                        <td colspan="2" class="py-4 px-4 text-right uppercase bg-gray-900 border-r border-gray-700">Grand Total Uang Muka :</td>
                        <td class="py-4 px-4 text-right bg-gray-950 text-yellow-400">{{ number_format($grandUm, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection