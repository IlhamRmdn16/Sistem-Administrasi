@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                {{ $judul }} (Global)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Rekapitulasi total mutasi unit menuju Showroom GP berdasarkan tipe motor.</p>
        </div>
        <a href="{{ route('laporan.mutasi-ke-gp.print-global', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.mutasi-ke-gp.global') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red w-full">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red w-full">
            </div>
            <button type="submit" class="bg-honda-red text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors w-full sm:w-auto">Tampilkan</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-6 font-semibold w-16 text-center border-r border-gray-100">No</th>
                        <th class="py-4 px-6 font-semibold text-center w-40 border-r border-gray-100">Kode Tipe</th>
                        <th class="py-4 px-6 font-semibold border-r border-gray-100">Tipe Motor</th>
                        <th class="py-4 px-6 font-semibold text-center w-40">Jumlah Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($data as $index => $row)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-6 text-center text-gray-400 border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3 px-6 text-center font-bold text-gray-600 border-r border-gray-100">{{ $row->kode_tipe }}</td>
                            <td class="py-3 px-6 font-semibold text-gray-700 border-r border-gray-100">{{ $row->nama_type }}</td>
                            <td class="py-3 px-6 text-center font-bold text-honda-red text-base">{{ $row->jumlah_unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 italic">Tidak ada data mutasi ke Showroom GP pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900">
                    <tr>
                        <td colspan="3" class="py-4 px-6 text-right uppercase tracking-wider">Total Keseluruhan</td>
                        <td class="py-4 px-6 text-center text-honda-red text-lg">{{ $total_unit }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection