@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Kontrol Accu - Mutasi Penjualan
            </h2>
        </div>
        <a href="{{ route('laporan.accu.mutasi.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.accu.mutasi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal SJK</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal SJK</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none"></div>
            <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 h-[38px]">Filter Data</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                <thead class="bg-honda-red text-white uppercase tracking-wider text-xs">
                    <tr>
                        <th class="py-3 px-4 text-center w-12 border-r border-red-500">No</th>
                        <th class="py-3 px-4 border-r border-red-500">No. Accu</th>
                        <th class="py-3 px-4 border-r border-red-500 text-center">Tanggal SJK</th>
                        <th class="py-3 px-4 border-r border-red-500 text-center">No. Kunci</th>
                        <th class="py-3 px-4 border-r border-red-500">Tipe Motor</th>
                        <th class="py-3 px-4">Nama Konsumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $index => $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-4 text-center text-gray-400 border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-4 font-bold text-gray-900 border-r border-gray-100 font-mono text-base tracking-wider">{{ $row->no_accu }}</td>
                            <td class="py-2.5 px-4 text-center border-r border-gray-100 font-mono text-gray-600">{{ \Carbon\Carbon::parse($row->tanggal_sjk)->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold font-mono border-r border-gray-100 text-blue-600">{{ $row->no_kunci }}</td>
                            <td class="py-2.5 px-4 border-r border-gray-100 text-gray-700">{{ $row->nama_type }}</td>
                            <td class="py-2.5 px-4 font-bold uppercase text-gray-900">{{ $row->nama_pemohon }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-400 italic">Tidak ada data mutasi accu terjual pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
