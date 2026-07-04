@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Registrasi Penerimaan Unit Masuk
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola dokumen induk penerimaan pengiriman unit dari Main Dealer.</p>
        </div>
        <a href="{{ route('motor-unit.create') }}" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Unit Masuk
        </a>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form action="{{ route('motor-unit.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Bukti, SJ, Mesin..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>
            <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
            <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            <a href="{{ route('motor-unit.index') }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6" x-data="{ expandedId: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-4 font-semibold w-10"></th>
                        <th class="py-4 px-6 font-semibold">No. Bukti Penerimaan</th>
                        <th class="py-4 px-6 font-semibold text-center">Tanggal Masuk</th>
                        <th class="py-4 px-6 font-semibold text-center">Jumlah Unit</th>
                        <th class="py-4 px-6 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($penerimaanData as $row)
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer" @click="expandedId = expandedId === {{ $row->id }} ? null : {{ $row->id }}">
                            <td class="py-4 px-4 text-center">
                                <svg :class="expandedId === {{ $row->id }} ? 'rotate-180 text-honda-red' : 'text-gray-400'" class="w-5 h-5 transition-transform duration-200 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900 tracking-wider font-mono text-base">{{ $row->no_bukti }}</div>
                                <div class="text-gray-500 text-xs mt-0.5">Surat Jalan: {{ $row->no_sj }} | Mobil: {{ $row->no_kendaraan }}</div>
                            </td>
                            <td class="py-4 px-6 text-center font-mono text-gray-700">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-block py-1 px-3 bg-red-50 text-honda-red font-bold rounded-full text-xs border border-red-100">{{ $row->motor_units_count }} Unit</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('motor-unit.edit', $row->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Buka Detail / Edit" @click.stop>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button type="button" onclick="deleteMasterGroup({{ $row->id }}, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Grup Dokumen" @click.stop>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="expandedId === {{ $row->id }}" style="display: none;" class="bg-gray-50/50">
                            <td colspan="5" class="py-4 px-8 border-t border-gray-100">
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-gray-100 text-gray-600">
                                            <tr>
                                                <th class="py-2 px-4 font-bold border-r border-gray-200">Tipe & Warna</th>
                                                <th class="py-2 px-4 font-bold border-r border-gray-200 text-center">No. Mesin & Rangka</th>
                                                <th class="py-2 px-4 font-bold border-r border-gray-200 text-center">No. Kunci</th>
                                                <th class="py-2 px-4 font-bold text-center">Cetak</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse($row->motorUnits as $unit)
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-2 px-4 border-r border-gray-100">
                                                    <div class="font-bold text-gray-800">{{ $unit->type->nama_type ?? '-' }}</div>
                                                    <div class="text-gray-500">{{ $unit->color->warna ?? '-' }}</div>
                                                </td>
                                                <td class="py-2 px-4 border-r border-gray-100 text-center font-mono">
                                                    <div class="uppercase">{{ $unit->no_mesin }}</div>
                                                    <div class="uppercase text-gray-500">{{ $unit->no_rangka }}</div>
                                                </td>
                                                <td class="py-2 px-4 border-r border-gray-100 text-center font-mono font-bold text-blue-600 uppercase">{{ $unit->no_kunci }}</td>
                                                <td class="py-2 px-4 text-center">
                                                    <a href="{{ route('motor-unit.print', $unit->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 font-bold py-1 px-3 rounded text-[10px] transition-colors border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        CETAK
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="py-4 text-center text-gray-400 italic">Tidak ada rincian unit.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Data registrasi penerimaan unit belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $penerimaanData->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteMasterGroup(id, button) {
        Swal.fire({
            title: 'Hapus Seluruh Grup?',
            text: "Menghapus nomor bukti ini juga akan menghapus otomatis seluruh unit motor terdaftar di dalamnya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/transaction/motor-unit/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    button.closest('tr').remove();
                });
            }
        });
    }
</script>
@endsection