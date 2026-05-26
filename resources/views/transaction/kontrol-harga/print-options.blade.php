@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Opsi Cetak Kwitansi
        </h2>
        <a href="{{ route('kontrol-harga.index') }}" class="text-gray-500 hover:text-honda-red text-sm font-bold flex items-center gap-1">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6 text-center">
        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Konsumen</div>
        <h3 class="text-xl font-black text-gray-900 uppercase">{{ $spk->nama_stnk }}</h3>
        <div class="text-sm text-gray-600 mt-1">No. SPK: <span class="font-bold">{{ $spk->no_spk }}</span></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('kontrol-harga.print.otr', $spk->id) }}" target="_blank" class="block bg-white p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-500 transition-all group text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800 text-lg mb-2">Kwitansi OTR</h4>
            <p class="text-xs text-gray-500">Cetak rincian kwitansi berdasarkan Harga OTR kendaraan.</p>
        </a>

        <a href="{{ route('kontrol-harga.print.dp-po', $spk->id) }}" target="_blank" class="block bg-white p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-green-500 transition-all group text-center">
            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800 text-lg mb-2">Kwitansi DP PO</h4>
            <p class="text-xs text-gray-500">Cetak rincian kwitansi khusus untuk pembayaran DP PO.</p>
        </a>

        <a href="{{ route('kontrol-harga.print.otr-dp-po', $spk->id) }}" target="_blank" class="block bg-white p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-purple-500 transition-all group text-center">
            <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800 text-lg mb-2">Kwitansi OTR - DP PO</h4>
            <p class="text-xs text-gray-500">Cetak kwitansi dengan perhitungan selisih antara OTR dan DP PO.</p>
        </a>
    </div>
</div>
@endsection
