@extends('layouts.app')

@section('content')
<div x-data="pencairanManager()" class="max-w-7xl mx-auto">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Pencairan Leasing Pokok (PLP)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola aktualisasi pencairan uang dari pihak bank/leasing.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-2 mb-4 border-b border-gray-200 pb-px">
        <button @click="activeTab = 'baru'" :class="activeTab === 'baru' ? 'border-honda-red text-honda-red font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 text-sm uppercase tracking-wider transition-colors">
            Pencairan Baru
        </button>
        <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'border-honda-red text-honda-red font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 text-sm uppercase tracking-wider transition-colors">
            Riwayat Pencairan
        </button>
    </div>

    <div x-show="activeTab === 'baru'" x-transition.opacity>
        <form action="{{ route('pencairan-leasing.store') }}" method="POST" @submit="isSubmitting = true">
            @csrf

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Bukti (Otomatis)</label>
                        <input type="text" value="PLP[Tahun/Bulan/Urut]" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Masuk Rekening</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Leasing <span class="text-red-500">*</span></label>
                        <select name="leasing_id" x-model="selectedLeasing" @change="fetchPendingSj()" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red font-bold text-gray-800 uppercase">
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
                    <h3 class="font-black text-gray-800 uppercase text-sm">Daftar Unit Menunggu Cair</h3>
                    <div x-show="pendingData.length > 0" class="text-xs bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full">
                        <span x-text="checkedCount"></span> Unit Dipilih
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="text-[10px] text-gray-500 uppercase tracking-wider bg-white border-b border-gray-100">
                            <tr>
                                <th class="p-3 text-center w-10">
                                    <input type="checkbox" @click="toggleAll($event)" class="w-4 h-4 text-honda-red rounded border-gray-300 cursor-pointer">
                                </th>
                                <th class="p-3">Identitas Unit</th>
                                <th class="p-3 text-right text-gray-700 bg-yellow-50">INPUT PENCAIRAN</th>
                                <th class="p-3 text-right">Nilai Realisasi</th>
                                <th class="p-3 text-right">Total DLL (Est)</th>
                                <th class="p-3 text-right">Selisih</th>
                                <th class="p-3 text-right font-bold">Margin / Deviasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr x-show="pendingData.length === 0">
                                <td colspan="7" class="p-10 text-center text-gray-400 italic">
                                    <div x-show="!isLoading" x-text="selectedLeasing ? 'Semua unit di Leasing ini sudah lunas dicairkan.' : 'Pilih Leasing terlebih dahulu.'"></div>
                                    <div x-show="isLoading" class="text-blue-500 font-bold">Memuat data...</div>
                                </td>
                            </tr>
                            <template x-for="(item, index) in pendingData" :key="item.id">
                                <tr :class="checkedIds.includes(item.id.toString()) ? 'bg-blue-50/30' : 'hover:bg-slate-50'">
                                    <td class="p-3 text-center align-top pt-5">
                                        <input type="checkbox" name="sj_ids[]" :value="item.id" x-model="checkedIds" class="w-4 h-4 text-honda-red rounded border-gray-300 cursor-pointer">
                                    </td>
                                    <td class="p-3 align-top">
                                        <div class="font-bold text-gray-800 text-sm" x-text="item.nama_stnk"></div>
                                        <div class="text-gray-500 mt-0.5" x-text="item.no_sj + ' | ' + item.tanggal"></div>
                                        <div class="text-blue-600 font-bold uppercase mt-0.5" x-text="item.tipe"></div>
                                    </td>

                                    <td class="p-3 align-top bg-yellow-50/30 w-48">
                                        <input type="number" :name="'pencairan[' + item.id + ']'" x-model.number="item.nilai_pencairan"
                                            placeholder="Ketik angka cair..."
                                            :disabled="!checkedIds.includes(item.id.toString())"
                                            class="w-full border border-yellow-400 bg-yellow-50 text-gray-900 font-bold text-right rounded-lg p-2 outline-none focus:border-honda-red focus:bg-white transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    </td>

                                    <td class="p-3 text-right align-top pt-5" x-text="formatRp(item.realisasi)"></td>
                                    <td class="p-3 text-right align-top pt-5 text-gray-500" x-text="formatRp(item.total_dll)"></td>

                                    <td class="p-3 text-right align-top pt-5 font-bold" x-text="formatRp(hitungSelisih(item))"></td>

                                    <td class="p-3 text-right align-top pt-5 font-bold text-sm"
                                        :class="{
                                            'text-green-600': hitungMargin(item) > 0,
                                            'text-red-600': hitungMargin(item) < 0,
                                            'text-gray-400': hitungMargin(item) === 0
                                        }">
                                        <span x-text="hitungMargin(item) > 0 ? '+' : ''"></span><span x-text="formatRp(hitungMargin(item))"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="pendingData.length > 0" class="p-4 bg-white border-t border-gray-100 flex justify-end">
                    <button type="submit" :disabled="checkedIds.length === 0 || isSubmitting" class="bg-honda-red text-white font-bold py-3 px-8 rounded-lg shadow-md hover:bg-red-800 transition-all disabled:opacity-50 flex items-center gap-2">
                        <span x-text="isSubmitting ? 'Memproses Data...' : 'Simpan Pencairan Sekarang'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div x-show="activeTab === 'riwayat'" style="display: none;">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">

            <div class="p-4 bg-slate-50 border-b border-gray-100">
                <form action="{{ route('pencairan-leasing.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end gap-4">
                    <input type="hidden" name="tab" value="riwayat">

                    <div class="flex-1 w-full sm:min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari No Bukti</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari PLP..." class="w-full border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm">
                            <span class="text-gray-400">s/d</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Leasing</label>
                        <select name="filter_leasing" class="border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm font-bold uppercase">
                            <option value="">-- Semua --</option>
                            @foreach($leasings as $l)
                                <option value="{{ $l->id }}" {{ request('filter_leasing') == $l->id ? 'selected' : '' }}>{{ $l->nama_leasing }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tampil</label>
                        <select name="per_page" class="border border-gray-300 rounded-lg py-2 px-3 outline-none focus:border-honda-red text-sm">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900">Filter</button>
                        <a href="{{ route('pencairan-leasing.index') }}?tab=riwayat" class="bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white text-[11px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4">No. Bukti / Tgl / Leasing</th>
                            <th class="py-3 px-4">Detail Konsumen</th>
                            <th class="py-3 px-4 text-right">Pencairan</th>
                            <th class="py-3 px-4 text-right">Realisasi</th>
                            <th class="py-3 px-4 text-right">Selisih</th>
                            <th class="py-3 px-4 text-right">Margin / DLL</th>
                            <th class="py-3 px-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($histories as $index => $h)
                            @foreach($h->details as $idx => $d)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-gray-400">{{ $idx == 0 ? ($histories->firstItem() + $index) : '' }}</td>
                                    <td class="py-3 px-4">
                                        @if($idx == 0)
                                            <div class="font-bold text-gray-800">{{ $h->no_bukti }}</div>
                                            <div class="text-gray-500">{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</div>
                                            <div class="font-bold text-blue-600 uppercase">{{ $h->leasing->nama_leasing }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold uppercase">{{ $d->suratJalan->spk->nama_stnk ?? '-' }}</div>
                                        <div class="text-gray-500">SJ: {{ $d->suratJalan->no_bukti ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-gray-800">{{ number_format($d->nilai_pencairan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($d->nilai_realisasi, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($d->selisih_aktual, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-bold {{ $d->margin_lebih_kurang > 0 ? 'text-green-600' : ($d->margin_lebih_kurang < 0 ? 'text-red-600' : 'text-gray-400') }}">
                                        {{ $d->margin_lebih_kurang > 0 ? '+' : '' }}{{ number_format($d->margin_lebih_kurang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($idx == 0)
                                            <form action="{{ route('pencairan-leasing.destroy', $h->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmRollback(this)" class="bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200" title="Hapus Dokumen (Rollback)">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-400 italic">Belum ada riwayat pencairan yang ditemukan.</td>
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
        Alpine.data('pencairanManager', () => ({
            activeTab: new URLSearchParams(window.location.search).get('tab') || 'baru',
            selectedLeasing: '',
            pendingData: [],
            checkedIds: [],
            isLoading: false,
            isSubmitting: false,

            get checkedCount() {
                return this.checkedIds.length;
            },

            formatRp(num) {
                if(!num) return '0';
                return parseInt(num).toLocaleString('id-ID');
            },

            // Kalkulasi Inline Real-time
            hitungSelisih(item) {
                let cair = item.nilai_pencairan ? parseInt(item.nilai_pencairan) : 0;
                return cair - parseInt(item.realisasi);
            },

            hitungMargin(item) {
                return this.hitungSelisih(item) - parseInt(item.total_dll);
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

                fetch(`/transaction/pencairan-leasing/api/pending/${this.selectedLeasing}`)
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
            title: 'Hapus Pencairan?',
            text: "Data pencairan ini akan dihapus permanen dan unit akan kembali ke status Menunggu Cair.",
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
