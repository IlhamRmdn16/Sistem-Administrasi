@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Mutasi Stok Unit
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola perpindahan fisik unit motor antar lokasi dan POP.</p>
        </div>
        <a href="{{ route('mutasi-stok.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2.5 px-5 rounded-lg text-sm transition-colors">
            + Buat Mutasi Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 text-[10px] uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">No</th>
                        <th class="py-3 px-4">No. Bukti & Tanggal</th>
                        <th class="py-3 px-4">Lokasi Asal</th>
                        <th class="py-3 px-4">Lokasi Tujuan</th>
                        <th class="py-3 px-4 text-center">Total Unit</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($mutasis as $index => $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 text-center text-gray-400">{{ $mutasis->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-800">{{ $item->no_bukti }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold text-red-600">
                                {{ $item->lokasi_asal }} {{ $item->asalPop ? '('.$item->asalPop->nama_sales.')' : '' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-green-600">
                                {{ $item->lokasi_tujuan }} {{ $item->tujuanPop ? '('.$item->tujuanPop->nama_sales.')' : '' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold">{{ $item->details->count() }} Unit</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('mutasi-stok.show', $item->id) }}" class="text-blue-600 hover:underline text-xs font-bold">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 italic">Belum ada data mutasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100 bg-slate-50/30">
            {{ $mutasis->links() }}
        </div>
    </div>
</div>
@endsection