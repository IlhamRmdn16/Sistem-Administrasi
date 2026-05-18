@extends('layouts.app')

@section('content')
<div x-data="pdiManManager()" @keydown.escape.window="isCreateOpen = false; isEditOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Data PDI Man
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola data petugas Pre-Delivery Inspection.</p>
        </div>

        <button @click="isCreateOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah PDI Man
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm text-red-800 font-medium">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form action="{{ route('pdiman.index') }}" method="GET" class="w-full sm:w-96 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Kode atau Nama PDI Man..." class="w-full border border-gray-300 rounded-lg py-2 pl-10 px-4 outline-none focus:border-honda-red text-sm">
            </form>
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $pdiMans->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $pdiMans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $pdiMans->total() }}</span> data
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold w-16 text-center">No</th>
                        <th class="py-4 px-6 font-semibold w-48">Kode PDI Man</th>
                        <th class="py-4 px-6 font-semibold">Nama PDI Man</th>
                        <th class="py-4 px-6 text-center w-32 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pdiMans as $index => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-4 px-6 text-center text-sm text-gray-500">{{ $pdiMans->firstItem() + $index }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 font-mono text-sm rounded border border-gray-200">{{ $item->kode_pdi_man }}</span>
                            </td>
                            <td class="py-4 px-6 font-bold text-gray-800 uppercase">{{ $item->nama_pdi_man }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button @click="openEditModal({{ $item }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('pdiman.destroy', $item->id) }}" method="POST" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500">Data PDI Man belum tersedia.</td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $pdiMans->links() }}
        </div>
    </div>


    <div x-show="isCreateOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="isCreateOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isCreateOpen = false"></div>
            
            <div x-show="isCreateOpen" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Tambah Data PDI Man</h3>
                    <button @click="isCreateOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form action="{{ route('pdiman.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode PDI Man (Otomatis)</label>
                        <input type="text" name="kode_pdi_man" value="{{ $autoKode }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama PDI Man <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pdi_man" required placeholder="Contoh: Budi Santoso" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="isCreateOpen = false" class="px-5 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-honda-red text-white rounded-lg font-bold shadow hover:bg-red-700">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="isEditOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="isEditOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isEditOpen = false"></div>
            
            <div x-show="isEditOpen" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data PDI Man</h3>
                    <button @click="isEditOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form :action="'/master/pdi-man/' + eId" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode PDI Man</label>
                        <input type="text" name="kode_pdi_man" x-model="eKode" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama PDI Man <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pdi_man" x-model="eNama" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="isEditOpen = false" class="px-5 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function pdiManManager() {
        return {
            isCreateOpen: false,
            isEditOpen: false,
            eId: '',
            eKode: '',
            eNama: '',

            openEditModal(item) {
                this.eId = item.id;
                this.eKode = item.kode_pdi_man;
                this.eNama = item.nama_pdi_man;
                this.isEditOpen = true;
            }
        }
    }

    // Fungsi Konfirmasi Delete Menggunakan SweetAlert2
    function confirmDelete(button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data PDI Man ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }

    // Notifikasi Sukses Otomatis (Jika ada Session 'success')
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endsection