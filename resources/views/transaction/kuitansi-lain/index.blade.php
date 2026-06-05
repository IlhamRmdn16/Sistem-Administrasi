@extends('layouts.app')

@section('content')
<div x-data="kllManager()">

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Kuitansi Lain-Lain
        </h2>
        <p class="text-sm text-gray-500 mt-1 ml-4">Kelola penerimaan dana di luar transaksi reguler penjualan.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-6">
            <div class="mb-5 border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditMode ? 'Edit Kuitansi' : 'Tambah Kuitansi Baru'"></h3>
            </div>

            <form :action="isEditMode ? '/transaction/kuitansi-lain/' + eId : '{{ route('kuitansi-lain.store') }}'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT" :disabled="!isEditMode">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">No. Bukti</label>
                        <input type="text" name="no_bukti" :value="isEditMode ? eNoBukti : '{{ $autoKode }}'" readonly class="w-full bg-gray-50 border border-gray-200 rounded p-2 font-mono font-bold text-gray-600 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" x-model="eTanggal" required class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nama Penyetor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" x-model="eNama" required placeholder="Contoh: Nama Konsumen" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red font-bold uppercase">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Alamat Lengkap</label>
                    <input type="text" name="alamat" x-model="eAlamat" placeholder="Nama Jalan / Kampung / Lingkungan" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red mb-2 uppercase">

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <input type="text" name="rt_rw" x-model="eRtRw" placeholder="RT/RW" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                        <input type="text" name="desa" x-model="eDesa" placeholder="Desa/Kelurahan" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="kecamatan" x-model="eKecamatan" placeholder="Kecamatan" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                        <input type="text" name="kabupaten_kota" x-model="eKabupaten" placeholder="Kabupaten/Kota" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">No. Telepon</label>
                        <input type="text" name="no_telepon" x-model="eTelp" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Tipe Motor</label>
                        <input type="text" name="tipe_motor" x-model="eTipe" placeholder="Input Manual Tipe Motor" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Keterangan Pembayaran</label>
                    <input type="text" name="keterangan" x-model="eKet" placeholder="Contoh: Uang titipan booking unit" class="w-full border border-gray-300 rounded p-2 text-xs outline-none focus:border-honda-red uppercase">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nilai Kuitansi (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai" x-model="eNilai" required class="w-full border border-gray-300 bg-yellow-50 rounded p-2 outline-none focus:border-honda-red font-bold text-right text-sm text-gray-900">
                </div>

                <div class="flex flex-col gap-2 pt-3">
                    <button type="submit" :class="isEditMode ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800'" class="w-full text-white rounded font-bold py-2.5 text-sm shadow transition-colors">
                        <span x-text="isEditMode ? 'Perbarui Data' : 'Simpan Kuitansi'"></span>
                    </button>
                    <button type="button" x-show="isEditMode" @click="cancelEditMode()" class="w-full bg-gray-100 text-gray-600 border border-gray-200 rounded font-bold py-2 text-sm hover:bg-gray-200">
                        Batal Edit
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="p-4 bg-slate-50 border-b border-gray-100">
                <form action="{{ route('kuitansi-lain.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Bukti / Nama..." class="w-full border border-gray-300 rounded py-2 px-3 text-xs outline-none focus:border-honda-red">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Periode</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ $start_date }}" class="border border-gray-300 rounded py-2 px-2 text-xs outline-none focus:border-honda-red">
                            <span class="text-xs text-gray-400">s/d</span>
                            <input type="date" name="end_date" value="{{ $end_date }}" class="border border-gray-300 rounded py-2 px-2 text-xs outline-none focus:border-honda-red">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tampil</label>
                        <select name="per_page" class="border border-gray-300 rounded py-2 px-3 text-xs outline-none focus:border-honda-red">
                            <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-gray-800 text-white font-bold px-4 py-2 rounded text-xs hover:bg-gray-900">Filter</button>
                        <a href="{{ route('kuitansi-lain.index') }}" class="bg-gray-200 text-gray-700 font-bold px-3 py-2 rounded text-xs hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white text-[10px] uppercase text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4">No. Bukti / Tgl</th>
                            <th class="py-3 px-4">Nama / Keterangan</th>
                            <th class="py-3 px-4 text-right">Nilai Kuitansi</th>
                            <th class="py-3 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($kuitansis as $index => $item)
                            <tr :class="eId == {{ $item->id }} ? 'bg-blue-50/70' : 'hover:bg-slate-50'">
                                <td class="py-3 px-4 text-center text-gray-400">{{ $kuitansis->firstItem() + $index }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-800">{{ $item->no_bukti }}</div>
                                    <div class="text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold uppercase text-gray-800">{{ $item->nama }}</div>
                                    <div class="text-gray-500 uppercase truncate max-w-[200px]" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-red-600">
                                    {{ number_format($item->nilai, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2.5">
                                        <a href="{{ route('kuitansi-lain.print', $item->id) }}" target="_blank" class="text-green-600 hover:text-green-800" title="Cetak Kuitansi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <button @click="loadToForm({{ $item }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('kuitansi-lain.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-gray-400 italic">Data kuitansi tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-gray-100 bg-slate-50/30">
                {{ $kuitansis->links() }}
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function kllManager() {
        return {
            isEditMode: false,
            eId: '',
            eNoBukti: '', eTanggal: '{{ date("Y-m-d") }}', eNama: '', eAlamat: '',
            eRtRw: '', eDesa: '', eKecamatan: '', eKabupaten: '',
            eTelp: '', eTipe: '', eKet: '', eNilai: '',

            loadToForm(item) {
                this.isEditMode = true;
                this.eId = item.id;
                this.eNoBukti = item.no_bukti;
                this.eTanggal = item.tanggal;
                this.eNama = item.nama;
                this.eAlamat = item.alamat;
                this.eRtRw = item.rt_rw;
                this.eDesa = item.desa;
                this.eKecamatan = item.kecamatan;
                this.eKabupaten = item.kabupaten_kota;
                this.eTelp = item.no_telepon;
                this.eTipe = item.tipe_motor;
                this.eKet = item.keterangan;
                this.eNilai = item.nilai;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            cancelEditMode() {
                this.isEditMode = false;
                this.eId = ''; this.eNoBukti = ''; this.eTanggal = '{{ date("Y-m-d") }}';
                this.eNama = ''; this.eAlamat = ''; this.eRtRw = ''; this.eDesa = '';
                this.eKecamatan = ''; this.eKabupaten = ''; this.eTelp = ''; this.eTipe = '';
                this.eKet = ''; this.eNilai = '';
            }
        }
    }

    function confirmDelete(button) {
        Swal.fire({
            title: 'Hapus Kuitansi?',
            text: "Nomor kuitansi ini akan dikembalikan dan dapat digunakan ulang untuk transaksi berikutnya.",
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
</script>
@endsection
