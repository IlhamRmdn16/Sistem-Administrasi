@extends('layouts.app')

@section('content')
<div x-data="pdiManManager()">

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Data PDI Man
        </h2>
        <p class="text-sm text-gray-500 mt-1 ml-4">Kelola data petugas Pre-Delivery Inspection dealer.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-6">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditMode ? 'Edit Data PDI Man' : 'Tambah PDI Man Baru'"></h3>
                <p class="text-xs text-gray-500 mt-0.5" x-text="isEditMode ? 'Ubah informasi data petugas terpilih.' : 'Masukkan petugas inspeksi baru ke sistem.'"></p>
            </div>

            <form :action="isEditMode ? '/master/pdi-man/' + eId : '{{ route('pdiman.store') }}'" method="POST">
                @csrf

                <input type="hidden" name="_method" value="PUT" :disabled="!isEditMode">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode PDI Man</label>
                        <input type="text" name="kode_pdi_man" :value="isEditMode ? eKode : '{{ $autoKode }}'" readonly
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 font-mono font-bold text-gray-600 cursor-not-allowed outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama PDI Man <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pdi_man" x-model="eNama" required placeholder="Contoh: Budi Santoso"
                            class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none transition-all focus:border-honda-red focus:ring-4 focus:ring-red-50">
                    </div>

                    <div class="flex flex-col gap-2 pt-3">
                        <button type="submit"
                            :class="isEditMode ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-100' : 'bg-honda-red hover:bg-red-700 focus:ring-red-100'"
                            class="w-full text-white rounded-lg font-bold py-2.5 text-sm shadow-sm transition-colors uppercase tracking-wider focus:ring-4"
                            x-text="isEditMode ? 'Perbarui Data' : 'Simpan Petugas'">
                        </button>

                        <button type="button" x-show="isEditMode" @click="cancelEditMode()"
                            class="w-full bg-gray-100 text-gray-600 border border-gray-200 rounded-lg font-bold py-2.5 text-sm hover:bg-gray-200 transition-colors uppercase tracking-wider">
                            Batal Edit
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="{{ route('pdiman.index') }}" method="GET" class="w-full sm:w-80 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Kode atau Nama..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 px-4 outline-none focus:border-honda-red text-sm">
                </form>
                <div class="text-xs text-gray-500 font-medium">
                    Menampilkan <span class="font-bold text-gray-800">{{ $pdiMans->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $pdiMans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $pdiMans->total() }}</span> data
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-[11px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold w-12 text-center">No</th>
                            <th class="py-3.5 px-4 font-semibold w-32">Kode</th>
                            <th class="py-3.5 px-4 font-semibold">Nama Petugas PDI</th>
                            <th class="py-3.5 px-4 text-center w-24 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($pdiMans as $index => $item)
                            <tr :class="eId == {{ $item->id }} ? 'bg-blue-50/70' : 'hover:bg-slate-50/50'" class="transition-colors">
                                <td class="py-3 px-4 text-center text-xs text-gray-400">{{ $pdiMans->firstItem() + $index }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 font-mono text-xs rounded border border-gray-200">{{ $item->kode_pdi_man }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-800 uppercase">{{ $item->nama_pdi_man }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2.5">
                                        <button @click="loadToForm({{ $item }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('pdiman.destroy', $item->id) }}" method="POST" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-400 italic">Data master PDI Man tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 bg-slate-50/30">
                {{ $pdiMans->links() }}
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function pdiManManager() {
        return {
            isEditMode: false,
            eId: '',
            eKode: '',
            eNama: '',

            // Memasukkan data baris tabel ke form kiri
            loadToForm(item) {
                this.isEditMode = true;
                this.eId = item.id;
                this.eKode = item.kode_pdi_man;
                this.eNama = item.nama_pdi_man;
                window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll halus ke atas agar fokus ke form
            },

            // Mengembalikan form kiri ke mode "Tambah Baru"
            cancelEditMode() {
                this.isEditMode = false;
                this.eId = '';
                this.eKode = '';
                this.eNama = '';
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

    // Notifikasi Sukses Otomatis dari Session Flash Laravel
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif
</script>
@endsection
