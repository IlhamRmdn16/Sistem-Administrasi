@extends('layouts.app')

@section('content')
<div class="max-w-[90rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan - Global Unit
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Ringkasan kuantitas dan nominal penjualan (Cash vs Kredit) per Tipe Motor.</p>
        </div>

        <a href="{{ route('laporan.penjualan.global-unit.print', ['dari_tanggal' => $dari_tanggal, 'sampai_tanggal' => $sampai_tanggal, 'jenis_dokumen' => $jenis_dokumen, 'search' => $search]) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.global-unit') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Dokumen</label>
                <select name="jenis_dokumen" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="all" {{ $jenis_dokumen == 'all' ? 'selected' : '' }}>Semua (SPK & GPK)</option>
                    <option value="spk" {{ $jenis_dokumen == 'spk' ? 'selected' : '' }}>Hanya SPK</option>
                    <option value="gpk" {{ $jenis_dokumen == 'gpk' ? 'selected' : '' }}>Hanya GPK</option>
                </select>
            </div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cari Tipe</label><input type="text" name="search" value="{{ $search }}" placeholder="Kode / Tipe Motor..." class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div class="flex gap-2">
                <select name="per_page" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-24">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                </select>
                <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors flex-1">Filter</button>
            </div>
        </form>
    </div>

    @if($salesData->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Volume Terjual</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-gray-900">{{ number_format($grandTotal->t_unit, 0, ',', '.') }}</span>
                <span class="text-sm font-bold text-gray-500">Unit</span>
            </div>
            <div class="text-[11px] text-gray-500 mt-2 border-t pt-2 flex justify-between">
                <span>Cash: <b>{{ $grandTotal->t_cash }}</b></span>
                <span>Kredit: <b>{{ $grandTotal->t_kredit }}</b></span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omzet (Nilai OTR)</span>
            <div class="mt-2">
                <span class="text-2xl font-black text-gray-900">Rp {{ number_format($grandTotal->t_otr, 0, ',', '.') }}</span>
            </div>
            <div class="text-[11px] text-gray-400 mt-2 border-t pt-2">
                Akumulasi seluruh harga jual kendaraan.
            </div>
        </div>

        <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Kas Masuk Penjualan Tunai</span>
            <div class="mt-2">
                <span class="text-2xl font-black text-emerald-700">Rp {{ number_format($grandTotal->t_tunai, 0, ',', '.') }}</span>
            </div>
            <div class="text-[11px] text-emerald-600 mt-2 border-t border-emerald-100">
                Uang masuk lunas dari konsumen Cash.
            </div>
        </div>

        <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Kas Masuk Uang Muka (DP)</span>
            <div class="mt-2">
                <span class="text-2xl font-black text-amber-700">Rp {{ number_format($grandTotal->t_dp, 0, ',', '.') }}</span>
            </div>
            <div class="text-[11px] text-amber-600 mt-2 border-t border-amber-100">
                Uang masuk DP + Tanda Jadi konsumen Kredit.
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-honda-red text-white text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-center border-r border-red-500" rowspan="2">No</th>
                        <th class="py-4 px-4 border-r border-red-500" rowspan="2">Tipe Motor</th>
                        <th class="py-2 px-4 text-center border-r border-b border-red-500" colspan="3">Kuantitas Unit</th>
                        <th class="py-2 px-4 text-center border-b border-red-500" colspan="3">Nominal Uang Masuk Penjualan</th>
                    </tr>
                    <tr>
                        <th class="py-2 px-4 text-center border-r border-red-500 bg-red-800">Cash</th>
                        <th class="py-2 px-4 text-center border-r border-red-500 bg-red-800">Kredit</th>
                        <th class="py-2 px-4 text-center border-r border-red-500 bg-gray-900">Total Unit</th>
                        <th class="py-2 px-4 text-right border-r border-red-500 bg-red-800">Total OTR</th>
                        <th class="py-2 px-4 text-right border-r border-red-500 bg-emerald-800">Tunai (Cash)</th>
                        <th class="py-2 px-4 text-right bg-amber-800">DP / Tanda Jadi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($salesData as $index => $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-400 border-r border-gray-100">{{ $salesData->firstItem() + $index }}</td>
                            <td class="py-3 px-4 border-r border-gray-100">
                                <span class="font-bold text-gray-900 block">{{ $row->nama_type }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">{{ $row->kode_tipe }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600 border-r border-gray-100">{{ $row->unit_cash }}</td>
                            <td class="py-3 px-4 text-center font-bold text-amber-600 border-r border-gray-100">{{ $row->unit_kredit }}</td>
                            <td class="py-3 px-4 text-center font-black text-gray-900 border-r border-gray-100 bg-gray-50/50">{{ $row->total_unit }}</td>

                            <td class="py-3 px-4 text-right font-semibold text-gray-800 border-r border-gray-100">{{ number_format($row->total_otr, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700 border-r border-gray-100 bg-emerald-50/10">{{ number_format($row->total_tunai, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-amber-700 bg-amber-50/10">{{ number_format($row->total_dp_tanda_jadi, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 italic">Tidak ada data penjualan pada rentang periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($salesData->count() > 0)
                <tfoot class="bg-gray-100 font-black text-gray-900 text-sm border-t-2 border-gray-300 shadow-inner">
                    <tr class="bg-gray-200/60">
                        <td colspan="2" class="py-4 px-4 text-right uppercase border-r border-gray-300 font-black">Grand Total Keseluruhan :</td>
                        <td class="py-4 px-4 text-center text-emerald-700 border-r border-gray-300 font-black">{{ number_format($grandTotal->t_cash, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center text-amber-700 border-r border-gray-300 font-black">{{ number_format($grandTotal->t_kredit, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center text-gray-900 border-r border-gray-300 bg-gray-300 font-black">{{ number_format($grandTotal->t_unit, 0, ',', '.') }}</td>

                        <td class="py-4 px-4 text-right border-r border-gray-300 font-black">{{ number_format($grandTotal->t_otr, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right text-emerald-700 border-r border-gray-300 bg-emerald-100 font-black">{{ number_format($grandTotal->t_tunai, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right text-amber-700 bg-amber-100 font-black">{{ number_format($grandTotal->t_dp, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="p-4 bg-white">
            {{ $salesData->links() }}
        </div>
    </div>
</div>
@endsection
