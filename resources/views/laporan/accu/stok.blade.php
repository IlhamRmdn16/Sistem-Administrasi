@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Kontrol Accu - Stok Belum Terjual
            </h2>
        </div>
        <a href="{{ route('laporan.accu.stok.print') }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            Lihat PDF
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                <thead class="bg-honda-red text-white uppercase tracking-wider text-xs">
                    <tr>
                        <th class="py-3 px-4 text-center w-12 border-r border-red-500">No</th>
                        <th class="py-3 px-4 border-r border-red-500">No. Accu</th>
                        <th class="py-3 px-4 border-r border-red-500 text-center">No. Kunci</th>
                        <th class="py-3 px-4 border-r border-red-500">Tipe Motor</th>
                        <th class="py-3 px-4 bg-gray-900">Posisi Stok Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $index => $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-4 text-center text-gray-400 border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-4 font-bold text-gray-900 border-r border-gray-100 font-mono text-base tracking-wider">{{ $row->no_accu }}</td>
                            <td class="py-2.5 px-4 text-center font-bold font-mono border-r border-gray-100 text-blue-600">{{ $row->no_kunci }}</td>
                            <td class="py-2.5 px-4 border-r border-gray-100 text-gray-700">{{ $row->nama_type }}</td>
                            <td class="py-2.5 px-4 font-black uppercase text-amber-700 bg-amber-50/20 tracking-wide">{{ $row->posisi_display }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center text-gray-400 italic">Tidak ada sisa stok accu yang terdata di sistem.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
