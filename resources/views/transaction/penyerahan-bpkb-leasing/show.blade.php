@extends('layouts.app')

@section('content')
<div x-data="{ checkAll: false }">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Detail Penyerahan BPKB
        </h2>
        <a href="{{ route('penyerahan-bpkb-leasing.index', ['tanggal' => $tanggal]) }}" class="text-gray-500 hover:text-honda-red text-sm font-bold flex items-center gap-1">
            &larr; Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Leasing</label>
                <input type="text" value="{{ $leasing->nama_leasing }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 text-sm font-bold text-gray-800 outline-none uppercase">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Penyerahan</label>
                <input type="date" value="{{ $tanggal }}" onchange="window.location.href='?tanggal='+this.value" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red cursor-pointer">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">No. Bukti Penyerahan</label>
                <input type="text" value="{{ $no_bukti }}" readonly class="w-full bg-honda-red/5 border border-honda-red/20 text-honda-red rounded-lg p-2.5 text-sm font-bold outline-none text-center">
            </div>
        </div>
    </div>

    @if($document)
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl mb-6 flex items-center justify-between">
            <div class="text-blue-800 text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Dokumen telah diproses pada tanggal ini.
            </div>
            <div class="flex gap-3">
                <form action="{{ route('penyerahan-bpkb-leasing.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan dokumen penyerahan ini? Data akan dikembalikan ke status belum diserahkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-white border border-red-300 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-50 transition-colors">
                        Batal Penyerahan
                    </button>
                </form>
                <a href="{{ route('penyerahan-bpkb-leasing.print', $document->id) }}" target="_blank" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">
                    Print Ulang
                </a>
            </div>
        </div>
    @endif

    <form action="{{ route('penyerahan-bpkb-leasing.store') }}" method="POST">
        @csrf
        <input type="hidden" name="leasing_id" value="{{ $leasing->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="no_bukti" value="{{ $no_bukti }}">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-gray-200">
                        <tr>
                            <th class="p-3 font-semibold text-gray-600 text-center w-12">No.</th>
                            <th class="p-3 font-semibold text-gray-600 text-center w-24">
                                @if(!$document)
                                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                                        <input type="checkbox" x-model="checkAll" @change="$event.target.checked ? document.querySelectorAll('.sjk-cb').forEach(cb => cb.checked = true) : document.querySelectorAll('.sjk-cb').forEach(cb => cb.checked = false)" class="w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red">
                                        <span class="text-[10px]">Pilih Semua</span>
                                    </label>
                                @else
                                    Approved
                                @endif
                            </th>
                            <th class="p-3 font-semibold text-gray-600">Surat Jalan</th>
                            <th class="p-3 font-semibold text-gray-600">Konsumen</th>
                            <th class="p-3 font-semibold text-gray-600">Kendaraan</th>
                            <th class="p-3 font-semibold text-gray-600">Alamat Lengkap</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                <td class="p-3 text-center">
                                    @if($document)
                                        <input type="checkbox" checked disabled class="w-4 h-4 text-green-500 border-green-300 rounded cursor-not-allowed">
                                    @else
                                        <input type="checkbox" name="sjk_ids[]" value="{{ $item->id }}" class="sjk-cb w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red cursor-pointer">
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-gray-800">{{ $item->no_bukti }}</div>
                                    <div class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-gray-900 uppercase">{{ $item->spk->nama_stnk }}</div>
                                    <div class="text-[11px] font-mono text-blue-600 bg-blue-50 px-1 py-0.5 rounded inline-block mt-0.5">BPKB: {{ $item->samsat->no_bpkb }}</div>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-gray-800">{{ $item->motorUnit->type->nama_type ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-500">Kunci: {{ $item->motorUnit->no_kunci ?? '-' }} | Polisi: <span class="font-bold text-gray-700">{{ $item->samsat->no_polisi ?? '-' }}</span></div>
                                    <div class="text-[11px] text-gray-500 font-mono">Mesin: {{ $item->motorUnit->no_mesin ?? '-' }}</div>
                                </td>
                                <td class="p-3 text-xs text-gray-600 leading-relaxed max-w-xs">
                                    {{ $item->spk->alamat }}
                                    @if($item->spk->rt_rw) RT/RW {{ $item->spk->rt_rw }} @endif
                                    @if($item->spk->desa_kelurahan) Kel/Desa {{ $item->spk->desa_kelurahan }} @endif
                                    @if($item->spk->kecamatan) Kec. {{ $item->spk->kecamatan }} @endif
                                    @if($item->spk->kota_kabupaten) Kab. {{ $item->spk->kota_kabupaten }} @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500 italic">
                                    @if($document)
                                        Tidak ada detail di dalam dokumen ini.
                                    @else
                                        Tidak ada data konsumen yang BPKB-nya siap diserahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(!$document && $items->count() > 0)
                <div class="p-4 border-t border-gray-100 bg-slate-50 flex justify-end">
                    <button type="submit" class="bg-gray-800 text-white font-bold px-8 py-2.5 rounded-lg text-sm hover:bg-gray-900 transition-colors">
                        Simpan & Cetak
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
