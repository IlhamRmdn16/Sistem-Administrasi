@extends('layouts.app')

@section('content')
<div class="max-w-[95rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Terperinci Motor Masuk
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Detail fisik unit motor masuk berdasarkan periode tanggal.</p>
        </div>
        <a href="{{ route('laporan.motor-masuk.print-terperinci', ['dari_tanggal' => $dari_tanggal, 'sampai_tanggal' => $sampai_tanggal]) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form action="{{ route('laporan.motor-masuk.terperinci') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>
            
            <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full sm:w-auto">
                <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
            
            <button type="submit" class="bg-honda-red text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors w-full sm:w-auto">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-4 font-semibold text-center w-12 border-b border-gray-200">No</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">Tanggal</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">No. Bukti</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">No. Kunci</th>
                        <th class="py-4 px-4 font-semibold border-b border-gray-200">Tipe Motor</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">Warna</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">No. Mesin</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">No. Rangka</th>
                        <th class="py-4 px-4 font-semibold text-center border-b border-gray-200">Tahun</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @php $itemNumber = $data->firstItem(); @endphp
                    @forelse($data as $penerimaan)
                        @php $totalRows = count($penerimaan->motorUnits); @endphp
                        @foreach($penerimaan->motorUnits as $subIndex => $row)
                            <tr class="hover:bg-slate-50/30 transition-colors border-b border-gray-100">
                                @if($subIndex === 0)
                                    <td rowspan="{{ $totalRows }}" class="py-3 px-4 text-center text-gray-400 font-mono align-top border-r border-gray-100 bg-slate-50/50">{{ $itemNumber++ }}</td>
                                    <td rowspan="{{ $totalRows }}" class="py-3 px-4 text-center text-gray-600 align-top border-r border-gray-100 bg-slate-50/50">{{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d/m/Y') }}</td>
                                    <td rowspan="{{ $totalRows }}" class="py-3 px-4 text-center font-bold text-gray-900 font-mono tracking-wider align-top border-r border-gray-100 bg-slate-50/50">{{ $penerimaan->no_bukti }}</td>
                                @endif
                                <td class="py-3 px-4 text-center font-mono font-bold text-blue-600 border-r border-gray-100">{{ $row->no_kunci }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-800 border-r border-gray-100">{{ $row->type->nama_type ?? '-' }}</td>
                                <td class="py-3 px-4 text-center text-gray-600 border-r border-gray-100">{{ $row->color->warna ?? '-' }}</td>
                                <td class="py-3 px-4 text-center font-mono uppercase border-r border-gray-100 tracking-wide">{{ $row->no_mesin }}</td>
                                <td class="py-3 px-4 text-center font-mono uppercase border-r border-gray-100 tracking-wide">{{ $row->no_rangka }}</td>
                                <td class="py-3 px-4 text-center text-gray-600">{{ $row->tahun_pembuatan }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 italic border-b border-gray-100">Tidak ada rincian data unit pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection