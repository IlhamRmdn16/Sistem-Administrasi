@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Laporan Kontrol Subsidi Main Dealer
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Matriks lengkap kontrol harga penjualan, pembayaran konsumen, mediator fee, dan rincian alokasi subsidi.</p>
        </div>

        <a href="{{ route('laporan.penjualan.subsidi-main-dealer.print', request()->all()) }}" target="_blank" class="bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-gray-900 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Lihat PDF
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('laporan.penjualan.subsidi-main-dealer') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
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
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pencarian</label><input type="text" name="search" value="{{ $search }}" placeholder="No. Dokumen / Pemohon / Mediator / Sales..." class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red"></div>
            <div class="flex gap-2">
                <select name="per_page" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-24"><option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 data</option><option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 data</option></select>
                <button type="submit" class="bg-honda-red text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors flex-1">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-[2400px] w-full text-left border-collapse whitespace-nowrap text-xs">
                <thead class="bg-honda-red text-white uppercase tracking-wider text-[10px] text-center sticky top-0">
                    <tr>
                        <th class="py-3 px-2 border-r border-red-500" colspan="10">TERIMA (RINCIAN KONTROL PENJUALAN)</th>
                        <th class="py-3 px-2 border-r border-red-500" colspan="3">TERIMA (KUITANSI KONSUMEN)</th>
                        <th class="py-3 px-2 border-r border-red-500" rowspan="2">MD FEE</th>
                        <th class="py-3 px-2 border-r border-red-500" rowspan="2">SETOR</th>
                        <th class="py-3 px-2 border-r border-red-500" rowspan="2">TAMBAH</th>
                        <th class="py-3 px-2 border-r border-red-500" rowspan="2">LEASING</th>
                        <th class="py-3 px-2 border-r border-red-500" rowspan="2">AHM</th>
                        <th class="py-3 px-2 border-r border-red-500" colspan="4">SUBSIDI</th>
                        <th class="py-3 px-2 border-r border-red-500" colspan="2">TANGGAL</th>
                        <th class="py-3 px-2 bg-gray-900" colspan="2">NAMA</th>
                    </tr>
                    <tr>
                        <th class="p-2 border-r border-red-400 bg-red-800 w-10">No</th>
                        <th class="p-2 border-r border-red-400 bg-red-800">Nama Pemohon</th>
                        <th class="p-2 border-r border-red-400 bg-red-800">Tipe Motor</th>
                        <th class="p-2 border-r border-red-400 bg-red-800">No. Kunci</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Harga Cash</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">DP</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Discount</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">DP Murni</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Sisa</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Refund</th>

                        <th class="p-2 border-r border-red-400 bg-red-700 text-right">Kontan</th>
                        <th class="p-2 border-r border-red-400 bg-red-700 text-right">Transfer</th>
                        <th class="p-2 border-r border-red-400 bg-red-700">Bank</th>

                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Main Dealer</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Leasing</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">DLL</th>
                        <th class="p-2 border-r border-red-400 bg-red-800 text-right">Dealer</th>

                        <th class="p-2 border-r border-red-400 bg-red-700 text-center">SPK</th>
                        <th class="p-2 border-r border-red-400 bg-red-700 text-center">SJK</th>

                        <th class="p-2 border-r border-gray-800 bg-gray-800">Sales</th>
                        <th class="p-2 bg-gray-800">Mediator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($reports as $index => $row)
                        @php
                            $isCash = in_array($row->jenis_pembayaran, ['Cash', 'Tunai']);
                            $hargaCash = $isCash ? $row->harga_otr : 0;
                            $dpKredit = !$isCash ? ($row->uang_muka + $row->tanda_jadi) : 0;
                            $discount = $row->kontrolHarga->discount ?? 0;
                            $dpMurni = $isCash ? ($hargaCash - $discount) : ($dpKredit - $discount);
                            $kontan = $row->kuitansiKonsumens->sum('bayar_kontan');
                            $transfer = $row->kuitansiKonsumens->sum('bayar_transfer');
                            $totalBayar = $kontan + $transfer;
                            $sisaTagihan = $dpMurni - $totalBayar;
                            $bankNames = $row->kuitansiKonsumens->where('bayar_transfer', '>', 0)->map(function($k) {
                                return $k->rekening ? $k->rekening->nama_rekening . ' ('. $k->rekening->nomor_rekening .')' : '';
                            })->filter()->unique()->implode(', ');
                            $subsidiLeasing = ($row->kontrolHarga->subsidi_leasing_1 ?? 0) + ($row->kontrolHarga->subsidi_leasing_2 ?? 0);
                            $subsidiDll = ($row->kontrolHarga->dll_1 ?? 0) + ($row->kontrolHarga->dll_2 ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-2.5 text-center text-gray-400 border-r border-gray-100">{{ $reports->firstItem() + $index }}</td>
                            <td class="p-2.5 font-bold text-gray-900 border-r border-gray-100 uppercase">{{ $row->nama_pemohon }}</td>
                            <td class="p-2.5 border-r border-gray-100">
                                <span class="font-bold text-gray-800 block">{{ $row->motorUnit->type->nama_type ?? '-' }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $row->motorUnit->type->kode_tipe ?? '-' }}</span>
                            </td>
                            <td class="p-2.5 font-mono font-bold text-center text-gray-700 border-r border-gray-100">{{ $row->motorUnit->no_kunci ?? '-' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold {{ $hargaCash > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $hargaCash > 0 ? number_format($hargaCash, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold {{ $dpKredit > 0 ? 'text-amber-700' : 'text-gray-300' }}">{{ $dpKredit > 0 ? number_format($dpKredit, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-red-600 font-semibold">{{ $discount > 0 ? number_format($discount, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-black text-gray-900 bg-gray-50/30">{{ number_format($dpMurni, 0, ',', '.') }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-bold {{ $sisaTagihan > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ $sisaTagihan > 0 ? number_format($sisaTagihan, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-blue-600">{{ ($row->kontrolHarga->refund_transfer ?? 0) > 0 ? number_format($row->kontrolHarga->refund_transfer, 0, ',', '.') : '0' }}</td>

                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-emerald-700 bg-emerald-50/10">{{ $kontan > 0 ? number_format($kontan, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-purple-700 bg-purple-50/10">{{ $transfer > 0 ? number_format($transfer, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 border-r border-gray-100 font-mono text-[10px] text-gray-600 max-w-xs overflow-hidden text-ellipsis">{{ $bankNames ?: '-' }}</td>

                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold">{{ ($row->kontrolHarga->mediator_fee ?? 0) > 0 ? number_format($row->kontrolHarga->mediator_fee, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-emerald-700 bg-emerald-50/20">{{ $kontan > 0 ? number_format($kontan, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold">{{ ($row->kontrolHarga->tambahan ?? 0) > 0 ? number_format($row->kontrolHarga->tambahan, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 border-r border-gray-100 font-bold text-center uppercase text-[10px]">
                                @if($isCash)
                                    <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Cash</span>
                                @else
                                    <span class="text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">{{ $row->leasing->nama_leasing ?? 'Kredit' }}</span>
                                @endif
                            </td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold">{{ ($row->kontrolHarga->subsidi_ahm ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_ahm, 0, ',', '.') : '0' }}</td>

                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold bg-gray-50/30">{{ ($row->kontrolHarga->subsidi_main_dealer ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_main_dealer, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold bg-gray-50/30">{{ $subsidiLeasing > 0 ? number_format($subsidiLeasing, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold bg-gray-50/30">{{ $subsidiDll > 0 ? number_format($subsidiDll, 0, ',', '.') : '0' }}</td>
                            <td class="p-2.5 text-right border-r border-gray-100 text-gray-800 font-semibold bg-gray-50/30">{{ ($row->kontrolHarga->subsidi_dealer ?? 0) > 0 ? number_format($row->kontrolHarga->subsidi_dealer, 0, ',', '.') : '0' }}</td>

                            <td class="p-2.5 text-center border-r border-gray-100 text-gray-600 font-mono">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="p-2.5 text-center border-r border-gray-100 text-gray-600 font-mono">{{ $row->suratJalan ? \Carbon\Carbon::parse($row->suratJalan->tanggal)->format('d/m/Y') : '-' }}</td>

                            <td class="p-2.5 border-r border-gray-100 font-medium uppercase">{{ $row->sales->nama_sales ?? '-' }}</td>
                            <td class="p-2.5 font-medium uppercase text-gray-600">{{ $row->kontrolHarga->nama_mediator ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="26" class="py-12 text-center text-gray-400 italic text-sm">Tidak ada baris data kontrol subsidi untuk periode terpilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-800 text-white font-bold text-[10px] uppercase tracking-wider text-right sticky bottom-0 border-t-2 border-gray-900">
                    <tr>
                        <td colspan="4" class="p-2.5 text-right bg-gray-900 border-r border-gray-700">Grand Total:</td>
                        <td class="p-2.5 border-r border-gray-700">{{ number_format($grandTotals['harga_cash'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700">{{ number_format($grandTotals['dp'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 text-red-400">{{ number_format($grandTotals['discount'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['dp_murni'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 text-center font-normal italic">-</td>
                        <td class="p-2.5 border-r border-gray-700 text-center font-normal italic">-</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['kontan'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['transfer'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700"></td>
                        <td class="p-2.5 border-r border-gray-700">{{ number_format($grandTotals['md_fee'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['setor'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700">{{ number_format($grandTotals['tambah'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700"></td>
                        <td class="p-2.5 border-r border-gray-700">{{ number_format($grandTotals['ahm'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['mdealer'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['leasing'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['dll'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700 bg-gray-900">{{ number_format($grandTotals['dealer'], 0, ',', '.') }}</td>
                        <td class="p-2.5 border-r border-gray-700"></td>
                        <td class="p-2.5 border-r border-gray-700"></td>
                        <td class="p-2.5 border-r border-gray-700"></td>
                        <td class="p-2.5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="p-4 bg-white border-t">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
