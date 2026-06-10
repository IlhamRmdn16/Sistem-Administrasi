@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <a href="{{ route('mutasi-stok.index') }}" class="text-gray-400 hover:text-honda-red transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            Detail Mutasi
        </h2>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 flex flex-wrap gap-6 justify-between items-center bg-slate-50">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase">Nomor Bukti</p>
                <p class="text-lg font-extrabold text-gray-900">{{ $mutasi->no_bukti }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase">Tanggal</p>
                <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase">Lokasi Asal</p>
                <p class="text-sm font-bold text-red-600">{{ $mutasi->lokasi_asal }} {{ $mutasi->asalPop ? '('.$mutasi->asalPop->nama_sales.')' : '' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase">Lokasi Tujuan</p>
                <p class="text-sm font-bold text-green-600">{{ $mutasi->lokasi_tujuan }} {{ $mutasi->tujuanPop ? '('.$mutasi->tujuanPop->nama_sales.')' : '' }}</p>
            </div>
        </div>
        
        <div class="p-5">
            <p class="text-xs font-bold text-gray-800 mb-3">Daftar Unit yang Dimutasi:</p>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-slate-50 text-[10px] uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="py-2 px-3 w-10 text-center">No</th>
                            <th class="py-2 px-3">Tipe & Warna</th>
                            <th class="py-2 px-3">No. Mesin</th>
                            <th class="py-2 px-3">No. Rangka</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @foreach($mutasi->details as $index => $detail)
                            <tr>
                                <td class="py-2 px-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 font-bold">
                                    {{ $detail->motorUnit->type->nama_type ?? '-' }}
                                    <div class="text-[9px] text-gray-400 font-normal">{{ $detail->motorUnit->color->warna ?? '-' }}</div>
                                </td>
                                <td class="py-2 px-3 font-mono">{{ $detail->motorUnit->no_mesin }}</td>
                                <td class="py-2 px-3 font-mono">{{ $detail->motorUnit->no_rangka }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($mutasi->keterangan)
            <div class="mt-4 p-3 bg-yellow-50 rounded text-xs text-yellow-800">
                <span class="font-bold">Keterangan:</span> {{ $mutasi->keterangan }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection