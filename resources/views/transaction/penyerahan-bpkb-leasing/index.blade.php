@extends('layouts.app')

@section('content')
<div>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Penyerahan BPKB Ke Leasing
        </h2>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('penyerahan-bpkb-leasing.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <label class="text-sm font-bold text-gray-700 whitespace-nowrap">Pilih Tanggal Penyerahan:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red w-full sm:w-64">
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 text-center w-16">No.</th>
                        <th class="p-4 font-semibold text-gray-600">Kode Leasing</th>
                        <th class="p-4 font-semibold text-gray-600">Nama Leasing</th>
                        <th class="p-4 font-semibold text-gray-600 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leasings as $index => $leasing)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-gray-800">{{ $leasing->kode_leasing }}</td>
                            <td class="p-4 font-bold text-gray-900 uppercase">{{ $leasing->nama_leasing }}</td>
                            <td class="p-4 text-center">
                                <a href="{{ route('penyerahan-bpkb-leasing.show', ['leasing_id' => $leasing->id, 'tanggal' => $tanggal]) }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-900 transition-colors inline-block whitespace-nowrap">
                                    Proses Data
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
