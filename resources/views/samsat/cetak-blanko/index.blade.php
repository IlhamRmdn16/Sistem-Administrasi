@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto">

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Cetak Blanko Samsat
        </h2>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="p-4 bg-slate-50 border-b border-gray-100">
            <form action="{{ route('cetak-blanko-samsat.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Periode Surat Jalan</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate }}" class="border border-gray-300 rounded py-2 px-3 text-xs outline-none focus:border-honda-red">
                        <span class="text-xs text-gray-400">s/d</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="border border-gray-300 rounded py-2 px-3 text-xs outline-none focus:border-honda-red">
                    </div>
                </div>
                <div>
                    <button type="submit" class="bg-gray-800 text-white font-bold px-5 py-2 rounded text-xs hover:bg-gray-900 transition-colors">Tampilkan Data</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap min-w-[1200px]">
                <thead class="bg-white text-[10px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">No</th>
                        <th class="py-3 px-4">Surat Jalan & SPK</th>
                        <th class="py-3 px-4">Nama & Alamat STNK</th>
                        <th class="py-3 px-4">Tipe & Warna</th>
                        <th class="py-3 px-4">Identitas Mesin</th>
                        <th class="py-3 px-4 text-center">Status Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($suratJalans as $index => $item)
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer" onclick="window.open('{{ route('cetak-blanko-samsat.print', $item->id) }}', '_blank')">
                            <td class="py-3 px-4 text-center text-gray-400">{{ $suratJalans->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-800">{{ $item->no_bukti }}</div>
                                <div class="text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">SPK: {{ $item->spk->no_spk ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-normal min-w-[250px]">
                                <div class="font-bold text-gray-800 uppercase">{{ $item->spk->nama_stnk ?? '-' }}</div>
                                <div class="text-gray-500 mt-0.5 line-clamp-2 uppercase">
                                    {{ $item->spk->alamat }}, RT/RW {{ $item->spk->rt_rw }}, {{ $item->spk->desa_kelurahan }}, KEC. {{ $item->spk->kecamatan }}, {{ $item->spk->kota_kabupaten }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-800 uppercase">{{ $item->motorUnit->type->nama_type ?? '-' }}</div>
                                <div class="text-gray-500 uppercase">{{ $item->motorUnit->color->warna ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">Tahun: {{ $item->motorUnit->tahun_pembuatan ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px]">
                                <div><span class="text-gray-400 mr-1">R:</span><span class="font-bold text-gray-800">{{ $item->motorUnit->no_rangka ?? '-' }}</span></div>
                                <div><span class="text-gray-400 mr-1">M:</span><span class="font-bold text-gray-800">{{ $item->motorUnit->no_mesin ?? '-' }}</span></div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->is_cetak_samsat)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Sudah Dicetak
                                    </span>
                                    <div class="text-[9px] text-gray-400 mt-1 font-bold underline decoration-dotted hover:text-gray-600">Print Ulang?</div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-md text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        Belum Dicetak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 italic">Tidak ada Surat Jalan pada periode tersebut.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-100 bg-slate-50/30">
            {{ $suratJalans->links() }}
        </div>
    </div>
</div>
@endsection
