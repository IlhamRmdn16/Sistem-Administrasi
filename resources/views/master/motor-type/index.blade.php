@extends('layouts.app')

@section('content')
<div x-data="motorManager('{{ $autoKodeTipe }}')" @keydown.escape.window="isCreateModalOpen = false; isEditModalOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Master Data Motor
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola katalog spesifikasi, harga, dan varian tipe motor.</p>
        </div>

        <button @click="isCreateModalOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md shadow-red-200 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200 focus:ring-4 focus:ring-red-100 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Data
        </button>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form action="{{ route('motor-type.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="filter_tahun" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red w-full">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $filter_tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Kode Tipe, Motor, Nama..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red w-full">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            <a href="{{ route('motor-type.index') }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-6 font-semibold">Tipe & Jenis</th>
                        <th class="py-4 px-6 font-semibold">Kode Motor</th>
                        <th class="py-4 px-6 font-semibold">Tahun Pembuatan</th>
                        <th class="py-4 px-6 font-semibold">OTR (Rp)</th>
                        <th class="py-4 px-6 font-semibold">Varian Warna</th>
                        <th class="py-4 px-6 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($motorTypes as $type)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800">{{ $type->nama_type }}</div>
                                <div class="text-gray-500 mt-1">{{ $type->kode_tipe ?? '-' }} <span class="mx-1">•</span> {{ $type->jenis ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-700 font-bold">{{ $type->kode_motor }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $type->tahun_pembuatan ?? '-' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ number_format($type->otr, 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($type->colors as $color)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $color->warna }} <span class="ml-1 text-gray-400 font-bold">({{ $color->kode_warna }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button @click="openEditModal({{ $type }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="confirmDeleteAjax({{ $type->id }}, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Data master motor belum tersedia atau tidak ditemukan.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $motorTypes->links() }}
        </div>
    </div>

    <div x-show="isCreateModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="isCreateModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="isCreateModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isCreateModalOpen" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Tambah Data Motor Baru</h3>
                    <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form @submit.prevent="submitCreate">
                        @csrf

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">1. Identitas Utama</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Tipe</label>
                                    <input type="text" name="kode_tipe" x-model="cKodeTipe" readonly required
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Motor</label>
                                    <select name="jenis" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Jenis</option>
                                        <option value="CUB">CUB (Bebek)</option>
                                        <option value="MATIC">MATIC</option>
                                        <option value="SPORT">SPORT</option>
                                        <option value="EV">EV (Listrik)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Motor (Nama)</label>
                                    <input type="text" name="nama_type" placeholder="Contoh: Scoopy Fashion" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Pembuatan</label>
                                    <input type="number" name="tahun_pembuatan" placeholder="Contoh: 2026" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">2. Spesifikasi & Servis</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Motor (Katalog)</label>
                                    <input type="text" name="kode_motor" placeholder="Contoh: 00288" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampul Buku Jadwal Service</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="sampul_buku[]" value="3 Kali Gratis" class="w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red">
                                            <span class="text-sm text-gray-700">3 Kali Gratis</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="sampul_buku[]" value="4 Kali Gratis" class="w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red">
                                            <span class="text-sm text-gray-700">4 Kali Gratis</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">3. Rincian Harga</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga OTR (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="otr" placeholder="0" required
                                            class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 pl-11 px-4 outline-none transition-all text-gray-800">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notice Pajak (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="notice_pajak" placeholder="0" required
                                            class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 pl-11 px-4 outline-none transition-all text-gray-800">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 bg-slate-50 p-4 sm:p-5 rounded-xl border border-gray-100">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">4. Varian Warna</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Kode warna otomatis terisi saat diketik.</p>
                                </div>
                                <button type="button" @click="addCreateColor()" class="text-sm bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 shadow-sm transition-all flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(color, index) in cColors" :key="index">
                                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                                        <div class="flex-1 w-full">
                                            <input type="text" x-model="color.warna" @input="color.kode_warna = generateCode(color.warna)" :name="`colors[${index}][warna]`" placeholder="Ketik warna (contoh: Merah Hitam)" required
                                                class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                        </div>
                                        <div class="w-full sm:w-32 flex gap-3">
                                            <input type="text" x-model="color.kode_warna" :name="`colors[${index}][kode_warna]`" readonly placeholder="Kode" required
                                                class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none text-center font-bold cursor-not-allowed">

                                            <button type="button" @click="removeCreateColor(index)" x-show="cColors.length > 1" class="p-2.5 bg-red-50 text-red-500 hover:bg-red-100 hover:text-honda-red border border-red-100 rounded-lg transition-colors shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-end pt-5 mt-4 border-t border-gray-100 gap-3">
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

            <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isEditModalOpen" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Motor</h3>
                    <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form @submit.prevent="submitEdit">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">1. Identitas Utama</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Tipe</label>
                                    <input type="text" name="kode_tipe" x-model="eKodeTipe" readonly required
                                        class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Motor</label>
                                    <select name="jenis" x-model="eJenis" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                        <option value="">Pilih Jenis</option>
                                        <option value="CUB">CUB (Bebek)</option>
                                        <option value="MATIC">MATIC</option>
                                        <option value="SPORT">SPORT</option>
                                        <option value="EV">EV (Listrik)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Motor (Nama)</label>
                                    <input type="text" name="nama_type" x-model="eNamaType" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Pembuatan</label>
                                    <input type="number" name="tahun_pembuatan" x-model="eTahunPembuatan" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">2. Spesifikasi & Servis</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Motor (Katalog)</label>
                                    <input type="text" name="kode_motor" x-model="eKodeMotor" required
                                        class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampul Buku Jadwal Service</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="sampul_buku[]" x-model="eSampulBuku" value="3 Kali Gratis" class="w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red">
                                            <span class="text-sm text-gray-700">3 Kali Gratis</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="sampul_buku[]" x-model="eSampulBuku" value="4 Kali Gratis" class="w-4 h-4 text-honda-red border-gray-300 rounded focus:ring-honda-red">
                                            <span class="text-sm text-gray-700">4 Kali Gratis</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">3. Rincian Harga</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga OTR (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="otr" x-model="eOtr" required
                                            class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 pl-11 px-4 outline-none transition-all text-gray-800">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notice Pajak (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="notice_pajak" x-model="eNoticePajak" required
                                            class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 pl-11 px-4 outline-none transition-all text-gray-800">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 bg-slate-50 p-4 sm:p-5 rounded-xl border border-gray-100">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">4. Varian Warna</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Kode warna otomatis terisi saat diketik.</p>
                                </div>
                                <button type="button" @click="addEditColor()" class="text-sm bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 shadow-sm transition-all flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(color, index) in eColors" :key="index">
                                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                                        <div class="flex-1 w-full">
                                            <input type="text" x-model="color.warna" @input="color.kode_warna = generateCode(color.warna)" :name="`colors[${index}][warna]`" placeholder="Ketik warna (contoh: Merah Hitam)" required
                                                class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                                        </div>
                                        <div class="w-full sm:w-32 flex gap-3">
                                            <input type="text" x-model="color.kode_warna" :name="`colors[${index}][kode_warna]`" readonly placeholder="Kode" required
                                                class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none text-center font-bold cursor-not-allowed">

                                            <button type="button" @click="removeEditColor(index)" x-show="eColors.length > 1" class="p-2.5 bg-red-50 text-red-500 hover:bg-red-100 hover:text-honda-red border border-red-100 rounded-lg transition-colors shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-end pt-5 mt-4 border-t border-gray-100 gap-3">
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
    function motorManager(autoKodeTipe) {
        return {
            isCreateModalOpen: false,
            isEditModalOpen: false,
            isSubmitting: false,
            isEditing: false,

            cKodeTipe: autoKodeTipe,
            cColors: [{ warna: '', kode_warna: '' }],

            eId: '',
            eKodeTipe: '', eJenis: '', eNamaType: '', eTahunPembuatan: '',
            eKodeMotor: '', eSampulBuku: [],
            eOtr: '', eNoticePajak: '',
            eColors: [],

            addCreateColor() {
                this.cColors.push({ warna: '', kode_warna: '' });
            },

            removeCreateColor(index) {
                this.cColors.splice(index, 1);
            },

            addEditColor() {
                this.eColors.push({ warna: '', kode_warna: '' });
            },

            removeEditColor(index) {
                this.eColors.splice(index, 1);
            },

            generateCode(str) {
                if (!str) return '';
                let words = str.trim().split(/\s+/);
                if (words.length === 1) {
                    let word = words[0];
                    if (word.length === 1) return word.toUpperCase();
                    return (word.charAt(0) + word.slice(-1)).toUpperCase();
                } else {
                    return words.map(w => w.charAt(0)).join('').toUpperCase();
                }
            },

            submitCreate(event) {
                this.isSubmitting = true;
                let formData = new FormData(event.target);

                fetch('{{ route("motor-type.store") }}', {
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

            openEditModal(type) {
                this.eId = type.id;
                this.eKodeTipe = type.kode_tipe;
                this.eJenis = type.jenis;
                this.eNamaType = type.nama_type;
                this.eTahunPembuatan = type.tahun_pembuatan;
                this.eKodeMotor = type.kode_motor;

                this.eSampulBuku = type.sampul_buku ? (Array.isArray(type.sampul_buku) ? type.sampul_buku : JSON.parse(type.sampul_buku)) : [];

                this.eOtr = type.otr;
                this.eNoticePajak = type.notice_pajak;

                this.eColors = type.colors && type.colors.length > 0
                    ? JSON.parse(JSON.stringify(type.colors))
                    : [{ warna: '', kode_warna: '' }];

                this.isEditModalOpen = true;
            },

            submitEdit(event) {
                this.isEditing = true;
                let formData = new FormData(event.target);

                fetch('/master/motor-type/' + this.eId, {
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
            }
        }
    }

    function confirmDeleteAjax(id, button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data Master Motor ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/master/motor-type/${id}`, {
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
