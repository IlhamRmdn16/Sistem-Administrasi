@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan PDI Man - Global
            </h2>
        </div>
        <a href="{{ route('laporan.penjualan.pdi-man-global.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.pdi-man-global') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama PDI Man</label>
                <select name="pdi_man_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="">Semua PDI Man</option>
                    @foreach($pdiManList as $p)
                        <option value="{{ $p->id }}" {{ $pdi_man_id == $p->id ? 'selected' : '' }}>{{ $p->nama_pdi_man }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors h-[38px]">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-honda-red text-white text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-4 border-r border-red-500 w-1/4">Kode Tipe</th>
                        <th class="py-4 px-4 border-r border-red-500 w-2/4">Nama Tipe</th>
                        <th class="py-4 px-4 text-center border-r border-red-500 w-1/4 bg-gray-900">Jumlah Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php 
                        $grandUnit = 0;
                    @endphp
                    @forelse($reports as $pdiManName => $items)
                        <tr class="bg-gray-100"><td colspan="3" class="py-3 px-4 font-black text-gray-900 uppercase">Nama PDI Man : {{ $pdiManName }}</td></tr>
                        @php 
                            $subUnit = 0;
                        @endphp
                        @foreach($items as $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4 font-bold text-gray-700 border-r border-gray-100 font-mono text-xs">{{ $row->kode_tipe }}</td>
                                <td class="py-3 px-4 text-gray-800 border-r border-gray-100">{{ $row->nama_type }}</td>
                                <td class="py-3 px-4 text-center font-bold text-blue-600 bg-gray-50/50">{{ $row->jumlah_unit }}</td>
                            </tr>
                            @php
                                $subUnit += $row->jumlah_unit;
                            @endphp
                        @endforeach
                        <tr class="bg-gray-50 font-bold text-gray-900 border-y border-gray-200">
                            <td colspan="2" class="py-3 px-4 text-right border-r border-gray-200 uppercase text-xs">Sub Total {{ $pdiManName }} :</td>
                            <td class="py-3 px-4 text-center text-blue-700 bg-gray-100/70">{{ $subUnit }}</td>
                        </tr>
                        @php
                            $grandUnit += $subUnit;
                        @endphp
                    @empty
                        <tr><td colspan="3" class="py-12 text-center text-gray-400 italic">Tidak ada data penyiapan unit PDI pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-800 text-white font-black text-sm border-t-2 border-gray-900">
                    <tr>
                        <td colspan="2" class="py-4 px-4 text-right uppercase bg-gray-900 border-r border-gray-700">Grand Total Keseluruhan :</td>
                        <td class="py-4 px-4 text-center bg-gray-950 text-yellow-400">{{ $grandUnit }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection