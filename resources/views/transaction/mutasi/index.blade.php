@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                {{ $judul }}
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Riwayat dokumen perpindahan unit untuk {{ $judul }}.</p>
        </div>
        <a href="{{ route('mutasi.create', $jenis) }}" class="bg-honda-red hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg text-sm shadow-md transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Dokumen Baru
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form action="{{ route('mutasi.index', $jenis) }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>

            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Bukti Mutasi..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            <a href="{{ route('mutasi.index', $jenis) }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
        </form>
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
                        <tr class="hover:bg-slate-50 transition-colors">
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
                            <td class="py-3 px-4 text-center flex items-center justify-center gap-3">
                                <a href="{{ route('mutasi.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold transition-colors">Detail</a>

                                <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan mutasi ini? Stok unit akan dikembalikan ke lokasi asal.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                        Batal
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 italic">Belum ada data riwayat mutasi untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $mutasis->links() }}
        </div>
    </div>
</div>
@endsection
