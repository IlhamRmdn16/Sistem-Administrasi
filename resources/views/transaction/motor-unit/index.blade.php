@extends('layouts.app')

@section('content')
<div x-data="unitManager()" @keydown.escape.window="isCreateModalOpen = false; isEditModalOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Registrasi Penerimaan Unit
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola data spesifikasi fisik kendaraan yang masuk ke inventaris dealer.</p>
        </div>

        <button @click="isCreateModalOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md shadow-red-200 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200 focus:ring-4 focus:ring-red-100 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Unit Masuk
        </button>
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
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Mesin, Rangka, DO..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            <a href="{{ route('motor-unit.index') }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-6 font-semibold">Dokumen (DO/SP)</th>
                        <th class="py-4 px-6 font-semibold">Tipe & Warna</th>
                        <th class="py-4 px-6 font-semibold">Identitas & Lokasi Unit</th>
                        <th class="py-4 px-6 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($motorUnits as $unit)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800">{{ $unit->no_do }}</div>
                                <div class="text-gray-500 mt-1">{{ $unit->no_sp }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800">{{ $unit->type->nama_type ?? '-' }}</div>
                                <div class="text-gray-500 mt-1 flex items-center gap-1">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                    {{ $unit->color->warna ?? '-' }} ({{ $unit->color->kode_warna ?? '-' }})
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="text-gray-800"><span class="text-gray-500 text-[10px] mr-1 uppercase">M:</span>{{ $unit->no_mesin }}</div>
                                <div class="text-gray-800"><span class="text-gray-500 text-[10px] mr-1 uppercase">R:</span>{{ $unit->no_rangka }}</div>
                                <div class="mt-1.5 inline-flex items-center gap-1 py-0.5 px-2 rounded-md bg-slate-100 text-[10px] font-bold text-slate-600 uppercase border border-slate-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $unit->posisi_stok }} {{ $unit->posisi_stok === 'POP' && $unit->lokasiPop ? '- ' . $unit->lokasiPop->nama_sales : '' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('motor-unit.print', $unit->id) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Cetak Dokumen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <button @click="openEditModal({{ $unit }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="confirmDeleteAjax({{ $unit->id }}, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 px-6 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Data unit kendaraan belum tersedia atau tidak ditemukan.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $motorUnits->links() }}
        </div>
    </div>

    <div x-show="isCreateModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="isCreateModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="isCreateModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isCreateModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">

                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Registrasi Penerimaan Unit</h3>
                    <button type="button" @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    <form @submit.prevent="submitCreate">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. DO (Delivery Order)</label>
                                <input type="text" name="no_do" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. SP & Tanggal</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="no_sp" placeholder="Contoh: 1234xxxx" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                    <span class="text-gray-500 font-bold px-1">/</span>
                                    <input type="date" name="tanggal_sp" x-model="cTanggal" required
                                        class="w-48 border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-3 outline-none transition-all text-gray-800">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 bg-blue-50/50 p-5 sm:p-6 rounded-xl border border-blue-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-blue-200 pb-2">Posisi Awal Kendaraan (Stok)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Disimpan Di Lokasi</label>
                                    <select name="posisi_stok" x-model="cPosisiStok" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white font-bold">
                                        <option value="">-- Pilih Lokasi --</option>
                                        <template x-for="lok in lokasiStatis" :key="lok">
                                            <option :value="lok" x-text="lok"></option>
                                        </template>
                                        <option value="POP">POP (Titip Pameran)</option>
                                    </select>
                                </div>
                                <div x-show="cPosisiStok === 'POP'" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Cabang POP</label>
                                    <select name="lokasi_pop_id" x-model="cLokasiPopId" :required="cPosisiStok === 'POP'"
                                        class="w-full border border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">-- Pilih POP --</option>
                                        <template x-for="p in pops" :key="p.id">
                                            <option :value="p.id" x-text="p.nama_sales"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 bg-slate-50 p-5 sm:p-6 rounded-xl border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Identitas Tipe Kendaraan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Motor</label>
                                    <select name="motor_type_id" x-model="cType" @change="updateCreateType()" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Tipe</option>
                                        <template x-for="t in types" :key="t.id">
                                            <option :value="t.id" x-text="t.nama_type"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Type</label>
                                    <input type="text" x-model="cKodeType" readonly
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna</label>
                                    <select name="motor_color_id" x-model="cColor" @change="updateCreateColor()" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Warna</option>
                                        <template x-for="c in cAvailableColors" :key="c.id">
                                            <option :value="c.id" x-text="c.warna + ' - ' + c.kode_warna"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Warna</label>
                                    <input type="text" x-model="cKodeWarna" readonly
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2">Spesifikasi Fisik Unit</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Mesin</label>
                                <input type="text" name="no_mesin" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Rangka</label>
                                <input type="text" name="no_rangka" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Seri Kunci</label>
                                <input type="text" name="no_seri_kunci" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Kunci</label>
                                <input type="text" name="no_kunci" x-model="cKunci" @input="processCreateKey()" required placeholder="Contoh: 2026E155"
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Pembuatan</label>
                                <input type="number" name="tahun_pembuatan" x-model="cTahun" readonly
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Accu</label>
                                <input type="text" name="no_accu" x-model="cAccu" readonly
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 mt-8 border-t border-gray-100 gap-3">
                            <button type="button" @click="isCreateModalOpen = false" class="bg-white border border-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-lg shadow-sm hover:bg-gray-50 transition-all" :disabled="isSubmitting">
                                Batal
                            </button>
                            <button type="submit" class="bg-honda-red text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2" :disabled="isSubmitting">
                                <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="isEditModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="isEditModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isEditModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">

                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Unit Kendaraan</h3>
                    <button type="button" @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    <form @submit.prevent="submitEdit">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. DO (Delivery Order)</label>
                                <input type="text" name="no_do" x-model="eDo" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. SP & Tanggal</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="no_sp" x-model="eSp" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                    <span class="text-gray-500 font-bold px-1">/</span>
                                    <input type="date" name="tanggal_sp" x-model="eTanggal" required
                                        class="w-48 border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-3 outline-none transition-all text-gray-800">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 bg-blue-50/50 p-5 sm:p-6 rounded-xl border border-blue-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-blue-200 pb-2">Posisi Awal Kendaraan (Stok)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Disimpan Di Lokasi</label>
                                    <select name="posisi_stok" x-model="ePosisiStok" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white font-bold">
                                        <option value="">-- Pilih Lokasi --</option>
                                        <template x-for="lok in lokasiStatis" :key="lok">
                                            <option :value="lok" x-text="lok" :selected="lok === ePosisiStok"></option>
                                        </template>
                                        <option value="POP" :selected="ePosisiStok === 'POP'">POP (Titip Pameran)</option>
                                    </select>
                                </div>
                                <div x-show="ePosisiStok === 'POP'" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Cabang POP</label>
                                    <select name="lokasi_pop_id" x-model="eLokasiPopId" :required="ePosisiStok === 'POP'"
                                        class="w-full border border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">-- Pilih POP --</option>
                                        <template x-for="p in pops" :key="p.id">
                                            <option :value="p.id" x-text="p.nama_sales" :selected="p.id == eLokasiPopId"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 bg-slate-50 p-5 sm:p-6 rounded-xl border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Identitas Tipe Kendaraan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Motor</label>
                                    <select name="motor_type_id" x-model="eType" @change="updateEditType()" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Tipe</option>
                                        <template x-for="t in types" :key="t.id">
                                            <option :value="t.id" x-text="t.nama_type" :selected="t.id == eType"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Type</label>
                                    <input type="text" x-model="eKodeType" readonly
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna</label>
                                    <select name="motor_color_id" x-model="eColor" @change="updateEditColor()" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Warna</option>
                                        <template x-for="c in eAvailableColors" :key="c.id">
                                            <option :value="c.id" x-text="c.warna + ' - ' + c.kode_warna" :selected="c.id == eColor"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Warna</label>
                                    <input type="text" x-model="eKodeWarna" readonly
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2">Spesifikasi Fisik Unit</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Mesin</label>
                                <input type="text" name="no_mesin" x-model="eMesin" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Rangka</label>
                                <input type="text" name="no_rangka" x-model="eRangka" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Seri Kunci</label>
                                <input type="text" name="no_seri_kunci" x-model="eSeri" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Kunci</label>
                                <input type="text" name="no_kunci" x-model="eKunci" @input="processEditKey()" required
                                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Pembuatan</label>
                                <input type="number" name="tahun_pembuatan" x-model="eTahun" readonly
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Accu</label>
                                <input type="text" name="no_accu" x-model="eAccu" readonly
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 mt-8 border-t border-gray-100 gap-3">
                            <button type="button" @click="isEditModalOpen = false" class="bg-white border border-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-lg shadow-sm hover:bg-gray-50 transition-all" :disabled="isEditing">
                                Batal
                            </button>
                            <button type="submit" class="bg-blue-600 text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center gap-2" :disabled="isEditing">
                                <svg x-show="!isEditing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <svg x-show="isEditing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isEditing ? 'Memperbarui...' : 'Perbarui Data'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function unitManager() {
        return {
            types: @json($types),
            lokasiStatis: @json($lokasiStatis),
            pops: @json($pops),
            todayDateInput: new Date().toISOString().split('T')[0],

            isCreateModalOpen: false,
            isSubmitting: false,
            cPosisiStok: '', cLokasiPopId: '',
            cType: '', cKodeType: '',
            cAvailableColors: [], cColor: '', cKodeWarna: '',
            cKunci: '', cTahun: '', cAccu: '',
            cTanggal: new Date().toISOString().split('T')[0],

            isEditModalOpen: false,
            isEditing: false,
            ePosisiStok: '', eLokasiPopId: '',
            eId: '', eDo: '', eSp: '', eTanggal: '', eMesin: '', eRangka: '', eSeri: '',
            eType: '', eKodeType: '',
            eAvailableColors: [], eColor: '', eKodeWarna: '',
            eKunci: '', eTahun: '', eAccu: '',

            updateCreateType() {
                let t = this.types.find(x => x.id == this.cType);
                if (t) {
                    this.cKodeType = t.kode_tipe;
                    this.cAvailableColors = t.colors;
                } else {
                    this.cKodeType = '';
                    this.cAvailableColors = [];
                }
                this.cColor = '';
                this.cKodeWarna = '';
            },

            updateCreateColor() {
                let c = this.cAvailableColors.find(x => x.id == this.cColor);
                this.cKodeWarna = c ? c.kode_warna : '';
            },

            processCreateKey() {
                let res = this.extractKeyInfo(this.cKunci);
                this.cTahun = res.tahun;
                this.cAccu = res.accu;
            },

            submitCreate(event) {
                this.isSubmitting = true;
                let formData = new FormData(event.target);

                fetch('{{ route("motor-unit.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        this.isCreateModalOpen = false;
                        this.isSubmitting = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    this.isSubmitting = false;
                    let errorMsg = 'Terjadi kesalahan sistem.';
                    if(error.errors) {
                        errorMsg = Object.values(error.errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: errorMsg
                    });
                });
            },

            openEditModal(unit) {
                this.eId = unit.id;
                this.eDo = unit.no_do;
                this.ePosisiStok = unit.posisi_stok || '';
                this.eLokasiPopId = unit.lokasi_pop_id || '';

                let spParts = unit.no_sp.split(' / ');
                this.eSp = spParts[0] || '';

                if (spParts.length > 1) {
                    let dParts = spParts[1].split('/');
                    if(dParts.length === 3) {
                        this.eTanggal = `${dParts[2]}-${dParts[1]}-${dParts[0]}`;
                    } else {
                        this.eTanggal = this.todayDateInput;
                    }
                } else {
                    this.eTanggal = this.todayDateInput;
                }

                this.eMesin = unit.no_mesin;
                this.eRangka = unit.no_rangka;
                this.eSeri = unit.no_seri_kunci;
                this.eKunci = unit.no_kunci;
                this.eTahun = unit.tahun_pembuatan;
                this.eAccu = unit.no_accu;

                this.eType = unit.motor_type_id;
                let t = this.types.find(x => x.id == this.eType);
                if(t) {
                    this.eKodeType = t.kode_tipe;
                    this.eAvailableColors = t.colors;
                    this.eColor = unit.motor_color_id;
                    let c = this.eAvailableColors.find(x => x.id == this.eColor);
                    this.eKodeWarna = c ? c.kode_warna : '';
                }

                this.isEditModalOpen = true;
            },

            updateEditType() {
                let t = this.types.find(x => x.id == this.eType);
                if (t) {
                    this.eKodeType = t.kode_tipe;
                    this.eAvailableColors = t.colors;
                } else {
                    this.eKodeType = '';
                    this.eAvailableColors = [];
                }
                this.eColor = '';
                this.eKodeWarna = '';
            },

            updateEditColor() {
                let c = this.eAvailableColors.find(x => x.id == this.eColor);
                this.eKodeWarna = c ? c.kode_warna : '';
            },

            processEditKey() {
                let res = this.extractKeyInfo(this.eKunci);
                this.eTahun = res.tahun;
                this.eAccu = res.accu;
            },

            submitEdit(event) {
                this.isEditing = true;
                let formData = new FormData(event.target);

                fetch('/transaction/motor-unit/' + this.eId, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        this.isEditModalOpen = false;
                        this.isEditing = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    this.isEditing = false;
                    let errorMsg = 'Terjadi kesalahan sistem.';
                    if(error.errors) {
                        errorMsg = Object.values(error.errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memperbarui',
                        text: errorMsg
                    });
                });
            },

            extractKeyInfo(keyInput) {
                let key = keyInput.trim();
                let result = { tahun: '', accu: '' };

                if (key.length >= 4) {
                    result.tahun = key.substring(0, 4);
                }

                if (key.length >= 5) {
                    let prefix = key.substring(0, 4);
                    let letter = key.charAt(4).toUpperCase();
                    let suffix = key.substring(5);

                    let monthMap = {
                        'A': '01', 'B': '02', 'C': '03', 'D': '04', 'E': '05', 'F': '06',
                        'G': '07', 'H': '08', 'I': '09', 'J': '10', 'K': '11', 'L': '12'
                    };

                    let monthCode = monthMap[letter] || letter;
                    result.accu = prefix + monthCode + suffix;
                }
                return result;
            }
        }
    }

    function confirmDeleteAjax(id, button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data Kendaraan ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
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
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        button.closest('tr').remove();
                    }
                });
            }
        });
    }
</script>
@endsection
