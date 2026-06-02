@extends('layouts.app')

@section('content')
<div x-data="rekeningManager()">

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Master Data Rekening
        </h2>
        <p class="text-sm text-gray-500 mt-1 ml-4">Kelola daftar rekening bank perusahaan untuk keperluan transaksi.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-6">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditMode ? 'Edit Data Rekening' : 'Tambah Rekening Baru'"></h3>
                <p class="text-xs text-gray-500 mt-0.5" x-text="isEditMode ? 'Perbarui informasi nomor rekening atau bank.' : 'Masukkan data rekening baru ke sistem.'"></p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Rekening</label>
                    <input type="text" name="kode_rekening" :value="isEditMode ? eKode : '{{ $autoKodeRekening }}'" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 font-mono font-bold text-gray-600 cursor-not-allowed outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Rekening / Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_rekening" x-model="eNama" required placeholder="Contoh: BCA - Cabang Garut"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none transition-all focus:border-honda-red focus:ring-4 focus:ring-red-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="number" name="nomor_rekening" x-model="eNomor" required placeholder="Contoh: 1234567890"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none transition-all focus:border-honda-red focus:ring-4 focus:ring-red-50 font-mono">
                </div>

                <div class="flex flex-col gap-2 pt-3">
                    <button type="submit" :disabled="isSubmitting"
                        :class="isEditMode ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-100' : 'bg-honda-red hover:bg-red-700 focus:ring-red-100'"
                        class="w-full text-white rounded-lg font-bold py-2.5 text-sm shadow-sm transition-all uppercase tracking-wider focus:ring-4 flex justify-center items-center gap-2">
                        <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSubmitting ? 'Memproses...' : (isEditMode ? 'Perbarui Rekening' : 'Simpan Rekening')"></span>
                    </button>

                    <button type="button" x-show="isEditMode" @click="cancelEditMode()" :disabled="isSubmitting"
                        class="w-full bg-gray-100 text-gray-600 border border-gray-200 rounded-lg font-bold py-2.5 text-sm hover:bg-gray-200 transition-colors uppercase tracking-wider">
                        Batal Edit
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="{{ route('rekening.index') }}" method="GET" class="w-full sm:w-80 relative flex items-center gap-2">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari rekening..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 px-4 outline-none focus:border-honda-red text-sm">
                    </div>
                    @if($search)
                        <a href="{{ route('rekening.index') }}" class="text-xs font-bold text-gray-500 hover:text-red-500 bg-gray-100 p-2 rounded shrink-0">Reset</a>
                    @endif
                </form>
                <div class="text-xs text-gray-500 font-medium hidden sm:block">
                    Menampilkan <span class="font-bold text-gray-800">{{ $rekenings->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $rekenings->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $rekenings->total() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-[11px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold w-24">Kode</th>
                            <th class="py-3.5 px-4 font-semibold">Nama Rekening / Bank</th>
                            <th class="py-3.5 px-4 font-semibold w-40">Nomor Rekening</th>
                            <th class="py-3.5 px-4 text-center w-24 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($rekenings as $r)
                            <tr :class="eId == {{ $r->id }} ? 'bg-blue-50/70' : 'hover:bg-slate-50/50'" class="transition-colors">
                                <td class="py-3 px-4 font-bold text-gray-700 text-xs">{{ $r->kode_rekening }}</td>
                                <td class="py-3 px-4 font-bold text-gray-800 uppercase">{{ $r->nama_rekening }}</td>
                                <td class="py-3 px-4 text-gray-600 font-mono text-xs">{{ $r->nomor_rekening }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2.5">
                                        <button @click="loadToForm({{ $r }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" onclick="confirmDeleteAjax({{ $r->id }}, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-400 italic">Data master Rekening tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 bg-slate-50/30">
                {{ $rekenings->links() }}
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function rekeningManager() {
        return {
            isEditMode: false,
            isSubmitting: false,

            eId: '',
            eKode: '',
            eNama: '',
            eNomor: '',

            loadToForm(item) {
                this.isEditMode = true;
                this.eId = item.id;
                this.eKode = item.kode_rekening;
                this.eNama = item.nama_rekening;
                this.eNomor = item.nomor_rekening;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            cancelEditMode() {
                this.isEditMode = false;
                this.eId = '';
                this.eKode = '';
                this.eNama = '';
                this.eNomor = '';
            },

            submitForm(event) {
                this.isSubmitting = true;
                let formData = new FormData(event.target);

                // Jika edit, tambahkan _method PUT
                if (this.isEditMode) {
                    formData.append('_method', 'PUT');
                }

                let url = this.isEditMode ? '/master/rekening/' + this.eId : '{{ route("rekening.store") }}';

                fetch(url, {
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
            }
        }
    }

    function confirmDeleteAjax(id, button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data Rekening ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/master/rekening/${id}`, {
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
