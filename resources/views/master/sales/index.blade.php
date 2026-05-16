@extends('layouts.app')

@section('content')
<div x-data="salesManager('{{ $autoKodeSales }}')" @keydown.escape.window="isCreateModalOpen = false; isEditModalOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Master Data Sales
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola daftar tenaga penjual dan POP dealer.</p>
        </div>
        <button @click="isCreateModalOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md shadow-red-200 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 focus:ring-4 focus:ring-red-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tambah Data
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form action="{{ route('sales.index') }}" method="GET" class="w-full sm:w-96 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode, Nama, atau NIK..."
                    class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2 pl-10 px-4 outline-none transition-all duration-200 text-sm">
            </form>
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $sales->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $sales->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $sales->total() }}</span> data
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                    <tr>
                        <th class="py-4 px-6">Kode & Jenis</th>
                        <th class="py-4 px-6">Nama Sales</th>
                        <th class="py-4 px-6">No. Telepon</th>
                        <th class="py-4 px-6">Tgl. Masuk</th>
                        <th class="py-4 px-6 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $s)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-800">{{ $s->kode_sales }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-bold tracking-wide uppercase {{ $s->jenis_sales == 'Sales' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                {{ $s->jenis_sales }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm font-bold text-gray-800">{{ $s->nama_sales }}</div>
                            <div class="text-xs text-gray-500 mt-1">NIK: {{ $s->nik ?? '-' }}</div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">{{ $s->telepon ?? '-' }}</td>
                        <td class="py-4 px-6 text-sm text-gray-600">
                            {{ $s->tgl_masuk ? \Carbon\Carbon::parse($s->tgl_masuk)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-3">
                                <button @click="openEditModal({{ $s }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <form action="{{ route('sales.destroy', $s->id) }}" method="POST" onsubmit="confirmDelete(event, this)">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Data sales belum tersedia.
                        </td>
                    </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $sales->links() }}
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
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">

                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Tambah Data Sales</h3>
                    <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">1. Identitas Utama</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Sales</label>
                                <input type="text" name="kode_sales" x-model="cKodeSales" readonly required class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis</label>
                                <select name="jenis_sales" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Sales">Sales</option>
                                    <option value="POP">POP</option>
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_sales" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>

                            <div class="col-span-1 md:col-span-2 mt-4">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">2. Detail Tambahan (Opsional)</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIK Karyawan</label>
                                <input type="text" name="nik" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-5 mt-6 border-t border-gray-100 gap-3">
                            <button type="button" @click="isCreateModalOpen = false" class="bg-white border border-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-lg shadow-sm hover:bg-gray-50 transition-all">Batal</button>
                            <button type="submit" class="bg-honda-red text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan Data
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
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">

                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Sales</h3>
                    <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form :action="'/master/sales/' + eId" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">1. Identitas Utama</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Sales</label>
                                <input type="text" name="kode_sales" x-model="eKodeSales" readonly required class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg shadow-sm py-2.5 px-4 outline-none font-bold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis</label>
                                <select name="jenis_sales" x-model="eJenisSales" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800 bg-white">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Sales">Sales</option>
                                    <option value="POP">POP</option>
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_sales" x-model="eNama" required class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>

                            <div class="col-span-1 md:col-span-2 mt-4">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">2. Detail Tambahan (Opsional)</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIK Karyawan</label>
                                <input type="text" name="nik" x-model="eNik" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WhatsApp</label>
                                <input type="text" name="telepon" x-model="eTelp" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" x-model="eTglMasuk" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                                <textarea name="alamat" x-model="eAlamat" rows="3" class="w-full border border-gray-300 focus:border-honda-red focus:ring-4 focus:ring-red-50 rounded-lg shadow-sm py-2.5 px-4 outline-none transition-all text-gray-800"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-5 mt-6 border-t border-gray-100 gap-3">
                            <button type="button" @click="isEditModalOpen = false" class="bg-white border border-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-lg shadow-sm hover:bg-gray-50 transition-all">Batal</button>
                            <button type="submit" class="bg-blue-600 text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function salesManager(autoKodeSales) {
        return {
            isCreateModalOpen: false,
            isEditModalOpen: false,

            cKodeSales: autoKodeSales,

            eId: '',
            eKodeSales: '',
            eJenisSales: '',
            eNama: '',
            eNik: '',
            eTelp: '',
            eTglMasuk: '',
            eAlamat: '',

            openEditModal(sales) {
                this.eId = sales.id;
                this.eKodeSales = sales.kode_sales;
                this.eJenisSales = sales.jenis_sales;
                this.eNama = sales.nama_sales;
                this.eNik = sales.nik;
                this.eTelp = sales.telepon;
                this.eTglMasuk = sales.tgl_masuk;
                this.eAlamat = sales.alamat;

                this.isEditModalOpen = true;
            }
        }
    }
</script>
@endsection
