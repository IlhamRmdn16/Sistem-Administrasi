@extends('layouts.app')

@section('content')
<div class="max-w-[90rem] mx-auto"> <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Stok Unit Sales / POP Detil
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Melihat rincian mesin, rangka, dan waktu mutasi unit di setiap POP.</p>
        </div>

        <a href="{{ route('laporan.stok.sales-detil.print', ['search' => $search]) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('laporan.stok.sales-detil') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            
            <div class="relative w-full sm:w-[28rem]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama POP / Mesin / Rangka / Kunci / Tipe..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-100 text-gray-700 font-bold px-5 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-honda-red text-white text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-center w-12 border-r border-red-500">No</th>
                        <th class="py-4 px-4 border-r border-red-500">Sales / POP</th>
                        <th class="py-4 px-4 text-center border-r border-red-500">Tgl Mutasi</th>
                        <th class="py-4 px-4 border-r border-red-500">No. Kunci</th>
                        <th class="py-4 px-4 border-r border-red-500">Tipe Motor</th>
                        <th class="py-4 px-4 border-r border-red-500">Warna</th>
                        <th class="py-4 px-4 text-center border-r border-red-500">Tahun</th>
                        <th class="py-4 px-4 border-r border-red-500">No. Mesin</th>
                        <th class="py-4 px-4 border-r border-red-500">No. Rangka</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @php $prevSalesId = null; @endphp
                    
                    @forelse($salesDetils as $index => $item)
                        
                        @if($prevSalesId !== null && $prevSalesId !== $item->lokasi_pop_id)
                            <tr class="bg-slate-50">
                                <td colspan="9" class="py-2 border-y border-gray-200 shadow-inner"></td>
                            </tr>
                        @endif

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-2.5 px-4 text-center text-gray-400 border-r border-gray-100">{{ $salesDetils->firstItem() + $index }}</td>
                            <td class="py-2.5 px-4 font-bold text-gray-800 border-r border-gray-100 uppercase">{{ $item->lokasiPop->nama_sales ?? 'Tanpa Nama' }}</td>
                            <td class="py-2.5 px-4 text-center text-gray-600 border-r border-gray-100">
                                {{ $item->tgl_mutasi ? \Carbon\Carbon::parse($item->tgl_mutasi)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2.5 px-4 font-mono font-bold text-gray-700 border-r border-gray-100">{{ $item->no_kunci ?? '-' }}</td>
                            <td class="py-2.5 px-4 border-r border-gray-100">
                                <span class="font-bold text-gray-900 block">{{ $item->type->nama_type ?? '-' }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">{{ $item->type->kode_tipe ?? '-' }}</span>
                            </td>
                            <td class="py-2.5 px-4 font-bold text-gray-700 border-r border-gray-100 uppercase">{{ $item->color->warna ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-gray-700 border-r border-gray-100">{{ $item->tahun_pembuatan ?? '-' }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-blue-700 border-r border-gray-100 uppercase">{{ $item->no_mesin }}</td>
                            <td class="py-2.5 px-4 font-mono text-gray-600 border-r border-gray-100 uppercase">{{ $item->no_rangka }}</td>
                        </tr>

                        @php $prevSalesId = $item->lokasi_pop_id; @endphp
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400 italic">Tidak ada data rincian stok unit di Sales / POP.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-white">
            {{ $salesDetils->links() }}
        </div>
    </div>
</div>
@endsection