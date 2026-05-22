@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control { border-radius: 0.5rem; border-color: #d1d5db; padding: 0.625rem; font-size: 0.875rem; }
    .ts-control.focus { border-color: #dc2626; box-shadow: 0 0 0 1px #dc2626; }
    .tab-active { border-bottom: 2px solid #dc2626; color: #dc2626; font-weight: bold; }
    .tab-inactive { color: #6b7280; font-weight: 500; }
</style>

<div x-data="kwitansiApp()">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Kwitansi Pajak Progresif
        </h2>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex gap-8">
            <button @click="activeTab = 'buat'" :class="activeTab === 'buat' ? 'tab-active' : 'tab-inactive'" class="pb-4 px-1 text-sm transition-colors hover:text-red-600">
                Buat Kwitansi Baru
            </button>
            <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'tab-active' : 'tab-inactive'" class="pb-4 px-1 text-sm transition-colors hover:text-red-600">
                Riwayat & Cetak Ulang
            </button>
        </nav>
    </div>

    <div x-show="activeTab === 'buat'" style="display: none;">
        <form action="{{ route('kwitansi-progresif.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf
            
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Data Transaksi</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Pilih Konsumen (Pajak Progresif)</label>
                        <select id="select-sjk" name="surat_jalan_id" class="w-full" required>
                            <option value="">Cari Nama / No SPK...</option>
                            @foreach($belumLunas as $doc)
                                <option value="{{ $doc->id }}" data-info="{{ json_encode($doc) }}">
                                    {{ $doc->spk->nama_stnk }} - {{ $doc->samsat->no_polisi }} (Rp {{ number_format($doc->samsat->pajak_progresif,0,',','.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red" required>
                    </div>
                </div>

                <div x-show="selectedDoc.id" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-transition>
                    <div class="flex justify-between items-center border-b pb-2 mb-4">
                        <h3 class="font-bold text-gray-800">Rincian Pembayaran</h3>
                        <span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-1 rounded">Tagihan: Rp <span x-text="formatRp(tagihan)"></span></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kontan / Cash</label>
                            <input type="number" name="bayar_kontan" x-model.number="bayarKontan" min="0" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Transfer</label>
                            <input type="number" name="bayar_transfer" x-model.number="bayarTransfer" min="0" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                        </div>
                    </div>

                    <div class="mb-4" x-show="bayarTransfer > 0">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Rekening Tujuan</label>
                        <select name="rekening_tujuan" :required="bayarTransfer > 0" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-honda-red">
                            <option value="">-- Pilih Rekening Dealer --</option>
                            @foreach($rekenings as $rek)
                                <option value="{{ $rek->nama_rekening }} - {{ $rek->nomor_rekening }}">
                                    {{ $rek->kode_rekening }} | {{ $rek->nama_rekening }} ({{ $rek->nomor_rekening }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6" x-show="isKredit">
                        <label class="block text-xs font-bold text-purple-600 mb-1">No. PO Leasing (Khusus Kredit)</label>
                        <input type="text" name="no_po_leasing" class="w-full border border-purple-300 bg-purple-50 rounded-lg p-2.5 text-sm outline-none focus:border-purple-600" placeholder="Ketik No PO...">
                    </div>

                    <div class="p-3 rounded-lg text-center font-bold text-sm mb-4" :class="selisih === 0 ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-amber-100 text-amber-700 border border-amber-300'">
                        Total Input: Rp <span x-text="formatRp(totalInput)"></span>
                        <div x-show="selisih !== 0" class="text-xs mt-1">Selisih: Rp <span x-text="formatRp(Math.abs(selisih))"></span></div>
                        <div x-show="selisih === 0" class="text-xs mt-1">✓ Pembayaran Pas</div>
                    </div>

                    <button type="submit" :disabled="selisih !== 0 || tagihan === 0" class="w-full bg-gray-800 text-white font-bold px-6 py-3 rounded-lg text-sm hover:bg-gray-900 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Simpan & Cetak Kwitansi
                    </button>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-slate-50 rounded-2xl border border-gray-200 shadow-inner p-6 h-full">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Deskripsi Konsumen</h3>
                    <div x-show="!selectedDoc.id" class="text-center py-12 text-gray-400 italic text-sm">
                        Silakan pilih konsumen di sebelah kiri untuk melihat detail data.
                    </div>
                    <div x-show="selectedDoc.id" style="display: none;">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="py-2 text-gray-500 w-1/3">No. SPK</td><td class="py-2 font-bold" x-text="selectedDoc.spk?.no_spk"></td></tr>
                                <tr><td class="py-2 text-gray-500">Nama Sales</td><td class="py-2" x-text="selectedDoc.spk?.sales?.nama_sales ?? '-'"></td></tr>
                                <tr><td class="py-2 text-gray-500">Nama Pemohon</td><td class="py-2 font-semibold" x-text="selectedDoc.spk?.nama_pemohon"></td></tr>
                                <tr><td class="py-2 text-gray-500">Nama STNK</td><td class="py-2 font-bold text-honda-red" x-text="selectedDoc.spk?.nama_stnk"></td></tr>
                                <tr><td class="py-2 text-gray-500">Alamat Lengkap</td><td class="py-2 text-xs leading-relaxed" x-text="formatAlamat()"></td></tr>
                                <tr><td class="py-2 text-gray-500">No. Telepon</td><td class="py-2" x-text="selectedDoc.spk?.telepon"></td></tr>
                                <tr><td colspan="2" class="py-3"><div class="border-t border-gray-200"></div></td></tr>
                                <tr><td class="py-2 text-gray-500">Kendaraan</td><td class="py-2 font-bold" x-text="selectedDoc.spk?.motor_type?.nama_type + ' (' + selectedDoc.spk?.motor_color?.warna + ')'"></td></tr>
                                <tr><td class="py-2 text-gray-500">Mesin / Rangka</td><td class="py-2 text-xs font-mono" x-text="(selectedDoc.motor_unit?.no_mesin ?? '-') + ' / ' + (selectedDoc.motor_unit?.no_rangka ?? '-')"></td></tr>
                                <tr><td colspan="2" class="py-3"><div class="border-t border-gray-200"></div></td></tr>
                                <tr>
                                    <td class="py-2 text-gray-500">Pembayaran</td>
                                    <td class="py-2">
                                        <span x-show="isKredit" class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-bold" x-text="selectedDoc.spk?.leasing?.nama_leasing"></span>
                                        <span x-show="!isKredit" class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-bold">CASH / KONTAN</span>
                                    </td>
                                </tr>
                                <tr x-show="isKredit"><td class="py-2 text-gray-500">Tenor</td><td class="py-2" x-text="selectedDoc.spk?.tenor_bulan + ' Bulan'"></td></tr>
                                <tr>
                                    <td class="py-2 text-gray-500">Finansial SPK</td>
                                    <td class="py-2 text-xs">
                                        OTR: Rp <span x-text="formatRp(selectedDoc.spk?.harga_otr)"></span> <br>
                                        DP: Rp <span x-text="formatRp(selectedDoc.spk?.uang_muka)"></span> | TJ: Rp <span x-text="formatRp(selectedDoc.spk?.tanda_jadi)"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div x-show="activeTab === 'riwayat'" style="display: none;">
        
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
            <form action="{{ route('kwitansi-progresif.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="riwayat">
                
                <div class="md:col-span-4">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Cari Data</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Kwitansi / Nama Konsumen / Tipe..." class="w-full border border-gray-300 rounded-lg p-2 text-xs outline-none focus:border-honda-red">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-300 rounded-lg p-2 text-xs outline-none focus:border-honda-red">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-300 rounded-lg p-2 text-xs outline-none focus:border-honda-red">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Tampilkan</label>
                    <select name="per_page" class="w-full border border-gray-300 rounded-lg p-2 text-xs outline-none focus:border-honda-red">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="w-full bg-gray-800 text-white font-bold py-2 rounded-lg text-xs hover:bg-gray-900 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('kwitansi-progresif.index', ['tab' => 'riwayat']) }}" class="w-full bg-gray-200 text-gray-700 text-center font-bold py-2 rounded-lg text-xs hover:bg-gray-300 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-gray-200">
                        <tr>
                            <th class="p-3 font-semibold text-gray-600 text-center w-12">No.</th>
                            <th class="p-3 font-semibold text-gray-600">No Kwitansi / Tgl</th>
                            <th class="p-3 font-semibold text-gray-600">Konsumen & Motor</th>
                            <th class="p-3 font-semibold text-gray-600">Metode Bayar</th>
                            <th class="p-3 font-semibold text-gray-600 text-right">Total Pajak</th>
                            <th class="p-3 font-semibold text-gray-600 text-center">Status</th>
                            <th class="p-3 font-semibold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($riwayat as $kwt)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center text-gray-500 font-medium">
                                    {{ $riwayat->firstItem() + $loop->index }}
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-gray-800">{{ $kwt->no_kwitansi }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($kwt->tanggal)->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold uppercase text-gray-900">{{ $kwt->suratJalan->spk->nama_stnk }}</div>
                                    <div class="text-xs text-gray-500">{{ $kwt->suratJalan->spk->motorType->nama_type ?? '-' }}</div>
                                </td>
                                <td class="p-3 text-xs">
                                    @if($kwt->bayar_kontan > 0) <div class="text-gray-700">Cash: Rp {{ number_format($kwt->bayar_kontan,0,',','.') }}</div> @endif
                                    @if($kwt->bayar_transfer > 0) <div class="text-blue-600 font-medium">TF: Rp {{ number_format($kwt->bayar_transfer,0,',','.') }}</div> @endif
                                </td>
                                <td class="p-3 text-right font-bold text-red-600">
                                    Rp {{ number_format($kwt->suratJalan->samsat->pajak_progresif,0,',','.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">LUNAS</span>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('kwitansi-progresif.print', $kwt->id) }}" target="_blank" class="bg-slate-100 border hover:bg-slate-200 text-gray-700 px-2 py-1 rounded text-xs font-bold transition-colors">
                                        Print Ulang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-8 text-center text-gray-500 italic">Data kwitansi tidak ditemukan atau masih kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-slate-50">{{ $riwayat->links() }}</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kwitansiApp', () => ({
            // Mengambil status tab aktif dari backend agar tidak meloncat saat difilter
            activeTab: '{{ $tab }}',
            selectedDoc: {},
            tagihan: 0,
            bayarKontan: 0,
            bayarTransfer: 0,
            isKredit: false,

            init() {
                let select = new TomSelect('#select-sjk', {
                    create: false,
                    onChange: (value) => {
                        if (!value) {
                            this.selectedDoc = {};
                            this.tagihan = 0;
                            this.isKredit = false;
                            return;
                        }
                        let rawData = document.querySelector(`option[value="${value}"]`).getAttribute('data-info');
                        let data = JSON.parse(rawData);
                        
                        this.selectedDoc = data;
                        this.tagihan = data.samsat?.pajak_progresif || 0;
                        this.isKredit = data.spk?.leasing_id ? true : false;
                        
                        this.bayarKontan = this.tagihan;
                        this.bayarTransfer = 0;
                    }
                });
            },

            get totalInput() {
                return (this.bayarKontan || 0) + (this.bayarTransfer || 0);
            },

            get selisih() {
                return this.tagihan - this.totalInput;
            },

            formatRp(angka) {
                if(!angka) return '0';
                return new Intl.NumberFormat('id-ID').format(angka);
            },

            formatAlamat() {
                let s = this.selectedDoc.spk;
                if(!s) return '-';
                let arr = [];
                if(s.alamat) arr.push(s.alamat);
                if(s.rt_rw) arr.push('RT/RW ' + s.rt_rw);
                if(s.desa_kelurahan) arr.push('Kel/Desa ' + s.desa_kelurahan);
                if(s.kecamatan) arr.push('Kec. ' + s.kecamatan);
                if(s.kota_kabupaten) arr.push('Kab. ' + s.kota_kabupaten);
                return arr.join(', ');
            }
        }));
    });
</script>
@endsection