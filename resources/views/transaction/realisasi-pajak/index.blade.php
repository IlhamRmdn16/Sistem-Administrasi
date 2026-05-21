@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Sedikit penyesuaian agar Tom Select menyatu dengan gaya Tailwind Anda */
    .ts-control { border-radius: 0.5rem; border-color: #d1d5db; padding: 0.625rem; font-size: 0.875rem; }
    .ts-control.focus { border-color: #dc2626; box-shadow: 0 0 0 1px #dc2626; }
</style>

<div>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Cetak Realisasi Pajak Progresif
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Filter data pengajuan STNK untuk pencetakan laporan Progresif.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form action="{{ route('realisasi-pajak.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-600 mb-2">Pilih No. Pengajuan</label>
                <select id="select-pengajuan" name="pengajuan_id" class="w-full" placeholder="-- Cari & Pilih No. Bukti Pengajuan --" autocomplete="off">
                    <option value="">-- Cari & Pilih No. Bukti Pengajuan --</option>
                    @foreach($pengajuans as $p)
                        <option value="{{ $p->id }}" {{ request('pengajuan_id') == $p->id ? 'selected' : '' }}>
                            {{ strtoupper($p->no_bukti) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-600 mb-2">Tanggal Realisasi</label>
                <input type="date" name="tanggal_realisasi" value="{{ $tanggal_realisasi }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red">
            </div>

            <button type="submit" class="bg-gray-800 text-white font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-gray-900 transition-colors">
                Tampilkan Data
            </button>
        </form>
    </div>

    @if($selectedPengajuan)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Preview Data Cetak</h3>
                @if(count($groupedData) > 0)
                    <a href="{{ route('realisasi-pajak.print', ['pengajuan_id' => $selectedPengajuan->id, 'tanggal_realisasi' => $tanggal_realisasi]) }}" target="_blank" class="bg-honda-red text-white font-bold px-5 py-2 rounded-lg text-sm flex items-center gap-2 hover:bg-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak
                    </a>
                @endif
            </div>

            <div class="p-6">
                @if(count($groupedData) > 0)
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="border-b-2 border-gray-800">
                            <tr>
                                <th class="py-2 w-10 text-center">No.</th>
                                <th class="py-2">Tipe Motor</th>
                                <th class="py-2 text-center w-20">Milik</th>
                                <th class="py-2 text-center w-20">Unit</th>
                                <th class="py-2 text-right w-32">Pajak Progresif</th>
                                <th class="py-2 text-right w-32">Sub. Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($groupedData as $index => $group)
                                <tr>
                                    <td class="py-3 text-center align-top font-bold">{{ $index + 1 }}</td>
                                    <td class="py-3 align-top">
                                        <div class="font-bold">{{ $group['tipe_motor'] }}</div>
                                        @foreach($group['names'] as $nama)
                                            <div class="text-gray-600 mt-1">- {{ $nama }}</div>
                                        @endforeach
                                    </td>
                                    <td class="py-3 text-center align-top">{{ $group['milik'] }}</td>
                                    <td class="py-3 text-center align-top">{{ $group['unit'] }}</td>
                                    <td class="py-3 text-right align-top">{{ number_format($group['pajak_progresif'], 0, ',', '.') }}</td>
                                    <td class="py-3 text-right align-top font-semibold">{{ number_format($group['sub_total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-gray-800 font-bold">
                            <tr>
                                <td colspan="5" class="py-3 text-right pr-6">TOTAL :</td>
                                <td class="py-3 text-right text-lg">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="text-center py-12 text-gray-500 italic">
                        Tidak ada konsumen yang terkena Pajak Progresif (Milik >= 2) pada Pengajuan ini.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#select-pengajuan', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            maxOptions: null // Menampilkan semua opsi hasil pencarian tanpa dibatasi
        });
    });
</script>
@endsection
