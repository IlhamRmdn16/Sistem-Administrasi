@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan Sales / POP - Global
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Analisis akumulasi performa kuantitas dan nominal penjualan per Sales maupun POP.</p>
        </div>

        <a href="{{ route('laporan.penjualan.sales-pop-global.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.sales-pop-global') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Sales / POP</label>
                <select name="sales_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="">Semua Sales / POP</option>
                    @foreach($salesList as $s)
                        <option value="{{ $s->id }}" {{ $sales_id == $s->id ? 'selected' : '' }}>{{ $s->nama_sales }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors h-[38px]">Filter Data</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-honda-red text-white text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-center w-12 border-r border-red-500">No</th>
                        <th class="py-4 px-4 border-r border-red-500">Nama Sales / POP</th>
                        <th class="py-4 px-4 border-r border-red-500">Kode Tipe</th>
                        <th class="py-4 px-4 border-r border-red-500">Nama Tipe</th>
                        <th class="py-4 px-4 text-center border-r border-red-500">Jumlah Unit</th>
                        <th class="py-4 px-4 text-right border-r border-red-500">Total OTR</th>
                        <th class="py-4 px-4 text-right border-r border-red-500">Uang Muka</th>
                        <th class="py-4 px-4 text-right bg-gray-900">Tanda Jadi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php
                        $counter = 1;
                        $grandUnit = 0;
                        $grandOtr = 0;
                        $grandUm = 0;
                        $grandTj = 0;
                    @endphp

                    @forelse($reports as $salesName => $items)
                        @php
                            $subUnit = 0;
                            $subOtr = 0;
                            $subUm = 0;
                            $subTj = 0;
                        @endphp

                        @foreach($items as $row)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-4 text-center text-gray-400 border-r border-gray-100">{{ $counter++ }}</td>
                                <td class="py-3 px-4 font-bold text-gray-900 border-r border-gray-100 uppercase">{{ $salesName }}</td>
                                <td class="py-3 px-4 font-bold text-gray-700 border-r border-gray-100 font-mono text-xs">{{ $row->kode_tipe }}</td>
                                <td class="py-3 px-4 text-gray-800 border-r border-gray-100">{{ $row->nama_type }}</td>
                                <td class="py-3 px-4 text-center font-bold text-blue-600 border-r border-gray-100">{{ $row->jumlah_unit }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-800 border-r border-gray-100">{{ number_format($row->total_otr, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-800 border-r border-gray-100">{{ number_format($row->total_um, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-800 bg-gray-50/30">{{ number_format($row->total_tj, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $subUnit += $row->jumlah_unit;
                                $subOtr += $row->total_otr;
                                $subUm += $row->total_um;
                                $subTj += $row->total_tj;
                            @endphp
                        @endforeach

                        <tr class="bg-gray-50 font-bold text-gray-900 border-y border-gray-200">
                            <td colspan="4" class="py-3 px-4 text-right border-r border-gray-200 uppercase text-xs tracking-wider">Sub Total {{ $salesName }} :</td>
                            <td class="py-3 px-4 text-center text-blue-700 border-r border-gray-200">{{ $subUnit }}</td>
                            <td class="py-3 px-4 text-right border-r border-gray-200">{{ number_format($subOtr, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right border-r border-gray-200">{{ number_format($subUm, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right bg-gray-100/70">{{ number_format($subTj, 0, ',', '.') }}</td>
                        </tr>

                        @php
                            $grandUnit += $subUnit;
                            $grandOtr += $subOtr;
                            $grandUm += $subUm;
                            $grandTj += $subTj;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 italic">Tidak ada data transaksi penjualan Sales / POP pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-800 text-white font-black text-sm border-t-2 border-gray-900">
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-right uppercase bg-gray-900 border-r border-gray-700">Grand Total Keseluruhan :</td>
                        <td class="py-4 px-4 text-center border-r border-gray-700 bg-gray-900 text-yellow-400">{{ $grandUnit }}</td>
                        <td class="py-4 px-4 text-right border-r border-gray-700">{{ number_format($grandOtr, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right border-r border-gray-700">{{ number_format($grandUm, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right bg-gray-950 text-yellow-400">{{ number_format($grandTj, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
