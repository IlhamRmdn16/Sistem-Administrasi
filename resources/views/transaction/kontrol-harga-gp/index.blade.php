@extends('layouts.app')

@section('content')
<div>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Kontrol Harga Penjualan GP
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Khusus mengelola dokumen berawalan GPK.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('kontrol-harga-gp.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700 whitespace-nowrap">Periode:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-500 font-bold">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red">
            </div>
            <button type="submit" class="bg-gray-800 text-white font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-gray-900 transition-colors">Tampilkan</button>
        </form>
    </div>

    <form action="{{ route('kontrol-harga-gp.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto custom-scrollbar pb-4">
                <table class="w-full text-left border-collapse" style="min-width: 1700px;">
                    <thead class="bg-slate-50 border-b border-gray-200">
                        <tr>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider sticky left-0 bg-slate-50 z-10 w-24 border-r border-gray-200">
                                No. GPK
                            </th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider sticky left-24 bg-slate-50 z-10 w-48 border-r border-gray-200">Nama STNK</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-48">Alamat</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-32">Tipe Motor</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Discount</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Sub. AHM</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Sub. Dealer</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Sub. Main Dealer</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Sub. Leasing 1</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Sub. Leasing 2</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">DLL 1</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">DLL 2</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Ekstra</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-32">Nama Mediator</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Mediator Fee</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Tambahan</th>
                            <th class="p-3 font-semibold text-gray-600 text-[11px] uppercase tracking-wider w-28">Refund TF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($spks as $spk)
                            @php
                                $k = $kontrolHargas[$spk->id] ?? null;
                            @endphp
                            <tr class="hover:bg-blue-50/50 group">
                                <td class="p-2 border-r border-gray-100 bg-white group-hover:bg-blue-50/50 sticky left-0 z-10 text-[11px] font-bold text-gray-700">
                                    {{ $spk->no_spk }}
                                </td>
                                <td class="p-2 border-r border-gray-100 bg-white group-hover:bg-blue-50/50 sticky left-24 z-10">
                                    <a href="{{ route('kontrol-harga-gp.print-options', $spk->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline uppercase block truncate w-44" title="Klik untuk Cetak Kwitansi">
                                        {{ $spk->nama_stnk }}
                                    </a>
                                </td>
                                <td class="p-2 text-[11px] text-gray-600 truncate max-w-[12rem]">{{ $spk->alamat }}</td>
                                <td class="p-2 text-[11px] font-bold text-gray-800 truncate max-w-[8rem]">{{ $spk->motorUnit->type->nama_type ?? '-' }}</td>

                                <td><input type="number" name="kontrol[{{ $spk->id }}][discount]" value="{{ $k->discount ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][subsidi_ahm]" value="{{ $k->subsidi_ahm ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][subsidi_dealer]" value="{{ $k->subsidi_dealer ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][subsidi_main_dealer]" value="{{ $k->subsidi_main_dealer ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][subsidi_leasing_1]" value="{{ $k->subsidi_leasing_1 ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][subsidi_leasing_2]" value="{{ $k->subsidi_leasing_2 ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][dll_1]" value="{{ $k->dll_1 ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][dll_2]" value="{{ $k->dll_2 ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][ekstra]" value="{{ $k->ekstra ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>

                                <td><input type="text" name="kontrol[{{ $spk->id }}][nama_mediator]" value="{{ $k->nama_mediator ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none uppercase" placeholder="-"></td>

                                <td><input type="number" name="kontrol[{{ $spk->id }}][mediator_fee]" value="{{ $k->mediator_fee ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][tambahan]" value="{{ $k->tambahan ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                                <td><input type="number" name="kontrol[{{ $spk->id }}][refund_transfer]" value="{{ $k->refund_transfer ?? '' }}" class="w-full text-xs p-1.5 border border-transparent hover:border-gray-300 focus:border-honda-red rounded outline-none" placeholder="0"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="p-8 text-center text-gray-500 italic">
                                    Tidak ada data GPK pada periode tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($spks->count() > 0)
                <div class="p-4 border-t border-gray-100 bg-slate-50 flex justify-end">
                    <button type="submit" class="bg-honda-red text-white font-bold px-8 py-2.5 rounded-lg text-sm hover:bg-red-700 transition-colors shadow-md">
                        Simpan Semua Perubahan
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection