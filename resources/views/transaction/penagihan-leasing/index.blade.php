@extends('layouts.app')

@section('content')
<div x-data="penagihanManager()" class="max-w-7xl mx-auto">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Penagihan Leasing (BTL)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Buat dokumen penagihan piutang dan kelola riwayatnya.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex justify-between items-center font-bold shadow-sm">
            <span>{{ session('success') }}</span>
            @if(session('print_id'))
                <a href="{{ route('penagihan-leasing.print', session('print_id')) }}" target="_blank" class="bg-green-700 text-white px-5 py-1.5 rounded-lg text-sm hover:bg-green-800 shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Sekarang
                </a>
            @endif
        </div>
    @endif

    <!-- TAB NAVIGATION -->
    <div class="flex gap-2 mb-4 border-b border-gray-200 pb-px">
        <button @click="activeTab = 'baru'" :class="activeTab === 'baru' ? 'border-honda-red text-honda-red font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 text-sm uppercase tracking-wider transition-colors">
            Penagihan Baru
        </button>
        <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'border-honda-red text-honda-red font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 text-sm uppercase tracking-wider transition-colors">
            Riwayat & Arsip
        </button>
    </div>

    <!-- ================= TAB 1: FORM PENAGIHAN BARU ================= -->
    <div x-show="activeTab === 'baru'" x-transition.opacity>
        <form action="{{ route('penagihan-leasing.store') }}" method="POST" @submit="isSubmitting = true">
            @csrf

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Bukti (Otomatis)</label>
                        <input type="text" value="BTL[Tahun/Bulan/Urut]" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Cetak</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Leasing <span class="text-red-500">*</span></label>
                        <select name="leasing_id" x-model="selectedLeasing" @change="fetchPendingSj()" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red bg-white font-bold text-gray-800 uppercase">
                            <option value="">-- Pilih Leasing --</option>
                            @foreach($leasings as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_leasing }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 uppercase text-sm">Daftar Surat Jalan Menunggu Tagihan</h3>
                    <div x-show="pendingData.length > 0" class="text-xs bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full">
                        <span x-text="checkedCount"></span> dari <span x-text="pendingData.length"></span> Dipilih
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="text-[10px] text-gray-500 uppercase tracking-wider bg-white border-b border-gray-100">
                            <tr>
                                <th class="p-3 text-center">
                                    <input type="checkbox" @click="toggleAll($event)" class="w-4 h-4 text-honda-red rounded border-gray-300 cursor-pointer">
                                </th>
                                <th class="p-3">No</th>
                                <th class="p-3 font-bold">No. SJ</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">No Kunci</th>
                                <th class="p-3 font-bold">Nama STNK</th>
                                <th class="p-3">Alamat</th>
                                <th class="p-3">Tipe</th>
                                <th class="p-3">No Mesin</th>
                                <th class="p-3">No Rangka</th>
                                <th class="p-3 text-right">OTR</th>
                                <th class="p-3 text-right">DP PO</th>
                                <th class="p-3 text-right text-red-600 font-bold">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">

                            <!-- STATE JIKA BELUM PILIH LEASING ATAU KOSONG -->
                            <tr x-show="pendingData.length === 0">
                                <td colspan="13" class="p-10 text-center text-gray-400 italic">
                                    <div x-show="!isLoading" x-text="selectedLeasing ? 'Tidak ada Surat Jalan yang menunggu ditagih untuk Leasing ini.' : 'Silakan pilih Leasing di atas terlebih dahulu.'"></div>
                                    <div x-show="isLoading" class="text-blue-500 font-bold">Sedang memuat data...</div>
                                </td>
                            </tr>

                            <!-- RENDER DATA DARI ALPINE -->
                            <template x-for="(item, index) in pendingData" :key="item.id">
                                <tr class="hover:bg-slate-50 transition-colors cursor-pointer" @click="toggleRow(item.id)">
                                    <td class="p-3 text-center" @click.stop>
                                        <input type="checkbox" name="sj_ids[]" :value="item.id" x-model="checkedIds" class="sj-checkbox w-4 h-4 text-honda-red rounded border-gray-300 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center text-gray-400" x-text="index + 1"></td>
                                    <td class="p-3 font-bold text-gray-800" x-text="item.no_sj"></td>
                                    <td class="p-3 text-gray-600" x-text="item.tanggal"></td>
                                    <td class="p-3 font-mono text-gray-600" x-text="item.no_kunci"></td>
                                    <td class="p-3 font-bold uppercase text-gray-800" x-text="item.nama_stnk"></td>
                                    <td class="p-3 truncate max-w-[150px]" :title="item.alamat" x-text="item.alamat"></td>
                                    <td class="p-3 uppercase text-gray-700" x-text="item.tipe"></td>
                                    <td class="p-3 font-mono uppercase text-gray-600" x-text="item.no_mesin"></td>
                                    <td class="p-3 font-mono uppercase text-gray-600" x-text="item.no_rangka"></td>
                                    <td class="p-3 text-right" x-text="formatRp(item.otr)"></td>
                                    <td class="p-3 text-right" x-text="formatRp(item.dp_po)"></td>
                                    <td class="p-3 text-right font-bold text-red-600" x-text="formatRp(item.sisa)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER TOTAL & TOMBOL SIMPAN -->
                <div x-show="pendingData.length > 0" class="p-4 bg-white border-t border-gray-100 flex justify-between items-center">
                    <div class="text-sm">
                        Total Tagihan Dipilih: <span class="font-black text-lg text-red-600 ml-2" x-text="formatRp(totalSisa)"></span>
                    </div>
                    <button type="submit" :disabled="checkedIds.length === 0 || isSubmitting" class="bg-gray-900 text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-gray-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan & Proses Penagihan'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ================= TAB 2: RIWAYAT & ARSIP ================= -->
    <div x-show="activeTab === 'riwayat'" style="display: none;">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-slate-50 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="{{ route('penagihan-leasing.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-3">
                    <input type="hidden" name="tab" value="riwayat">
                    <select name="filter_leasing" class="border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm font-bold uppercase">
                        <option value="">-- Semua Leasing --</option>
                        @foreach($leasings as $l)
                            <option value="{{ $l->id }}" {{ request('filter_leasing') == $l->id ? 'selected' : '' }}>{{ $l->nama_leasing }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors">Filter</button>
                    @if(request('filter_leasing'))
                        <a href="{{ route('penagihan-leasing.index') }}?tab=riwayat" class="text-xs font-bold text-gray-500 hover:text-red-500 bg-gray-100 p-2 rounded">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white text-[11px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold w-12 text-center">No</th>
                            <th class="py-3.5 px-4 font-semibold">No. Bukti Penagihan</th>
                            <th class="py-3.5 px-4 font-semibold">Tanggal</th>
                            <th class="py-3.5 px-4 font-semibold">Leasing Tujuan</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Jumlah SJK</th>
                            <th class="py-3.5 px-4 text-center w-32 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($histories as $index => $h)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-4 text-center text-xs text-gray-400">{{ $histories->firstItem() + $index }}</td>
                                <td class="py-3 px-4 font-bold text-gray-800">{{ $h->no_bukti }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-bold text-blue-700 uppercase">{{ $h->leasing->nama_leasing ?? '-' }}</td>
                                <td class="py-3 px-4 text-center font-bold bg-blue-50/50 text-blue-600 rounded-lg">
                                    {{ $h->details->count() }} Unit
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('penagihan-leasing.print', $h->id) }}" target="_blank" class="bg-blue-100 text-blue-700 p-2 rounded hover:bg-blue-200 transition-colors" title="Print Ulang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <form action="{{ route('penagihan-leasing.destroy', $h->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmRollback(this)" class="bg-red-100 text-red-700 p-2 rounded hover:bg-red-200 transition-colors" title="Hapus & Rollback">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-400 italic">Belum ada riwayat penagihan yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-slate-50/30">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('penagihanManager', () => ({
            // Ambil parameter tab dari URL agar jika di-refresh karena filter, posisinya tidak pindah
            activeTab: new URLSearchParams(window.location.search).get('tab') || 'baru',
            selectedLeasing: '',
            pendingData: [],
            checkedIds: [],
            isLoading: false,
            isSubmitting: false,

            get checkedCount() {
                return this.checkedIds.length;
            },

            get totalSisa() {
                return this.pendingData
                    .filter(item => this.checkedIds.includes(item.id.toString()))
                    .reduce((sum, item) => sum + parseInt(item.sisa), 0);
            },

            formatRp(num) {
                return parseInt(num).toLocaleString('id-ID');
            },

            toggleRow(id) {
                let strId = id.toString();
                if(this.checkedIds.includes(strId)) {
                    this.checkedIds = this.checkedIds.filter(val => val !== strId);
                } else {
                    this.checkedIds.push(strId);
                }
            },

            toggleAll(event) {
                if (event.target.checked) {
                    this.checkedIds = this.pendingData.map(item => item.id.toString());
                } else {
                    this.checkedIds = [];
                }
            },

            fetchPendingSj() {
                this.pendingData = [];
                this.checkedIds = [];

                if (!this.selectedLeasing) return;

                this.isLoading = true;

                fetch(`/transaction/penagihan-leasing/api/pending/${this.selectedLeasing}`)
                    .then(res => res.json())
                    .then(data => {
                        this.pendingData = data;
                        this.isLoading = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false;
                    });
            }
        }));
    });

    function confirmRollback(button) {
        Swal.fire({
            title: 'Hapus & Batalkan Penagihan?',
            text: "Surat Jalan yang ada di dalam dokumen ini akan dikembalikan ke status BELUM DITAGIH.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
@endsection
