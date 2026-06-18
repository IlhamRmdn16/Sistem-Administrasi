@extends('layouts.app')

@section('content')
<div class="max-w-[95rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Penjualan - Terperinci
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Rincian baris data per satu transaksi penjualan motor.</p>
        </div>
        <a href="{{ route('laporan.penjualan.terperinci.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.terperinci') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label><input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label><input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Dokumen</label>
                <select name="jenis_dokumen" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="all" {{ $jenis_dokumen == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="spk" {{ $jenis_dokumen == 'spk' ? 'selected' : '' }}>Hanya SPK</option>
                    <option value="gpk" {{ $jenis_dokumen == 'gpk' ? 'selected' : '' }}>Hanya GPK</option>
                </select>
            </div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pencarian</label><input type="text" name="search" value="{{ $search }}" placeholder="No. Dokumen / Pemohon / STNK / Kunci..." class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div class="flex gap-2">
                <select name="per_page" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-24"><option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 data</option><option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 data</option></select>
                <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors flex-1">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-honda-red text-white text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-center w-12 border-r border-red-500">No</th>
                        <th class="py-4 px-4 border-r border-red-500">No. Dokumen</th>
                        <th class="py-4 px-4 text-center border-r border-red-500">Tgl SPK</th>
                        <th class="py-4 px-4 border-r border-red-500">Nama Pemohon & STNK</th>
                        <th class="py-4 px-4 border-r border-red-500">Tipe Motor & Kunci</th>
                        <th class="py-4 px-4 text-center border-r border-red-500">Pembayaran</th>
                        <th class="py-4 px-4 text-right border-r border-red-500">Harga OTR</th>
                        <th class="py-4 px-4 text-right bg-gray-900">DP / Tanda Jadi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($transactions as $index => $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-400 border-r border-gray-100">{{ $transactions->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-gray-700 border-r border-gray-100">{{ $row->no_spk }}</td>
                            <td class="py-3 px-4 text-center text-gray-600 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 border-r border-gray-100 uppercase">
                                <span class="font-bold text-gray-800 block">{{ $row->nama_pemohon }}</span>
                                <span class="text-[10px] text-gray-500">STNK: {{ $row->nama_stnk }}</span>
                            </td>
                            <td class="py-3 px-4 border-r border-gray-100">
                                <span class="font-bold text-gray-800 block">{{ $row->motorUnit->type->nama_type ?? '-' }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">Kunci: {{ $row->motorUnit->no_kunci ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center border-r border-gray-100">
                                @if(in_array($row->jenis_pembayaran, ['Cash', 'Tunai']))
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded font-bold uppercase text-[10px]">CASH</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded font-bold uppercase text-[10px]">KREDIT</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-900 border-r border-gray-100">{{ number_format($row->harga_otr, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold bg-gray-50/50 {{ in_array($row->jenis_pembayaran, ['Cash', 'Tunai']) ? 'text-gray-400' : 'text-amber-700' }}">
                                @if(in_array($row->jenis_pembayaran, ['Cash', 'Tunai']))
                                    -
                                @else
                                    {{ number_format($row->uang_muka + $row->tanda_jadi, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-12 text-center text-gray-400 italic text-sm">Tidak ada baris transaksi penjualan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot class="bg-gray-50 font-black text-gray-900 text-xs border-t-2 border-gray-300 divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="py-2.5 px-4 text-right uppercase border-r border-gray-200 bg-gray-100/70">Total Nilai OTR (Semua Unit):</td>
                        <td class="py-2.5 px-4 text-right text-gray-900 border-r border-gray-100 font-bold text-sm">{{ number_format($totals['otr'], 0, ',', '.') }}</td>
                        <td class="py-2.5 px-4 bg-gray-100/40"></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="py-2.5 px-4 text-right uppercase border-r border-gray-200 text-emerald-700 bg-emerald-50/30">Total Uang Masuk dari Cash (Lunas):</td>
                        <td class="py-2.5 px-4 text-right text-emerald-700 border-r border-gray-100 font-bold text-sm">{{ number_format($totals['tunai'], 0, ',', '.') }}</td>
                        <td class="py-2.5 px-4 bg-emerald-50/20"></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="py-2.5 px-4 text-right uppercase border-r border-gray-200 text-amber-700 bg-amber-50/30">Total Uang Masuk dari Kredit (DP):</td>
                        <td class="py-2.5 px-4 text-center text-gray-400 border-r border-gray-100 font-normal italic">-</td>
                        <td class="py-2.5 px-4 text-right text-amber-700 bg-amber-100/50 font-bold text-sm">{{ number_format($totals['dp'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="p-4 bg-white">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
