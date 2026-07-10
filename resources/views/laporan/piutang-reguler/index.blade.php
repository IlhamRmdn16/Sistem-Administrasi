@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Keuangan Konsumen (Reguler)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Manajemen piutang kontrol bayar konsumen dealer utama.</p>
        </div>
        <a href="{{ route('laporan.piutang-reguler.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.piutang-reguler.index') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-4">
            <div class="flex items-center gap-2 w-full lg:w-auto">
                <span class="text-sm text-gray-500 font-semibold whitespace-nowrap">Periode SPK:</span>
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red w-full">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red w-full">
            </div>
            
            <div class="w-full lg:w-72">
                <select name="jenis_laporan" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full font-medium">
                    <option value="piutang_konsumen" {{ $jenis_laporan == 'piutang_konsumen' ? 'selected' : '' }}>Laporan Piutang Konsumen</option>
                    <option value="pembayaran" {{ $jenis_laporan == 'pembayaran' ? 'selected' : '' }}>Laporan Pembayaran</option>
                    <option value="pembayaran_transfer" {{ $jenis_laporan == 'pembayaran_transfer' ? 'selected' : '' }}>Laporan Pembayaran Transfer</option>
                    <option value="kwitansi_lain" {{ $jenis_laporan == 'kwitansi_lain' ? 'selected' : '' }}>Laporan Kwitansi Lain-lain</option>
                </select>
            </div>

            <button type="submit" class="bg-honda-red text-white font-semibold px-6 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors w-full lg:w-auto">
                Tampilkan
            </button>
        </form>
    </div>

    @if($jenis_laporan === 'piutang_konsumen')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-4 px-4 text-center border-r border-gray-100">No</th>
                        <th class="py-4 px-4 border-r border-gray-100">Nama Konsumen</th>
                        <th class="py-4 px-4 border-r border-gray-100">Alamat (RT/RW)</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">No. SPK / GPK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">Tgl SPK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">No. SJK</th>
                        <th class="py-4 px-4 text-center border-r border-gray-100">Tgl SJK</th>
                        <th class="py-4 px-4 border-r border-gray-100">Tipe Motor</th>
                        <th class="py-4 px-4 border-r border-gray-100">Sales</th>
                        <th class="py-4 px-4 border-r border-gray-100">Leasing</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Uang Muka / OTR Netto</th>
                        <th class="py-4 px-4 text-right border-r border-gray-100">Nilai Piutang</th>
                        <th class="py-4 px-4 text-center">Tenggat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($data as $index => $row)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center text-gray-400 font-mono border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-gray-800 border-r border-gray-100">{{ $row->nama_konsumen }}</td>
                            <td class="py-3.5 px-4 text-gray-600 border-r border-gray-100 truncate max-w-xs">{{ $row->alamat_lengkap }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-gray-700 font-mono tracking-wider border-r border-gray-100">{{ $row->no_spk }}</td>
                            <td class="py-3.5 px-4 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_spk)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-700 font-mono border-r border-gray-100">{{ $row->no_sjk }}</td>
                            <td class="py-3.5 px-4 text-center text-gray-500 border-r border-gray-100">{{ \Carbon\Carbon::parse($row->tgl_sjk)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700 border-r border-gray-100">{{ $row->tipe_motor }}</td>
                            <td class="py-3.5 px-4 text-gray-600 border-r border-gray-100">{{ $row->sales }}</td>
                            <td class="py-3.5 px-4 border-r border-gray-100">
                                <span class="px-2 py-1 rounded-md text-xs font-bold {{ $row->leasing == 'KONTAN' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $row->leasing }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-gray-700 border-r border-gray-100">Rp {{ number_format($row->uang_muka_netto, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-honda-red border-r border-gray-100">Rp {{ number_format($row->nilai_piutang, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center font-bold">
                                <span class="text-red-600 font-mono">{{ $row->tenggat }} Hari</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-8 text-center text-gray-500 italic">Tidak ada saldo piutang berjalan terdeteksi pada periode penarikan SPK ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white p-12 text-center rounded-2xl border border-dashed border-gray-300 text-gray-400 italic">
        Sub-Fitur laporan "{{ transform($jenis_laporan, function($v){ return str_replace('_', ' ', $v); }) }}" sedang dalam antrean pengambangan diskusi berikutnya.
    </div>
    @endif
</div>
@endsection