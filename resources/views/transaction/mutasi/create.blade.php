@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto" x-data="mutasiForm('{{ $kunciAsal }}', '{{ $kunciTujuan }}')">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <a href="{{ route('mutasi.index', $jenis) }}" class="text-gray-400 hover:text-honda-red transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            Buat {{ $judul }}
        </h2>
    </div>

    <form action="{{ route('mutasi.store', $jenis) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 uppercase border-b border-gray-100 pb-2 mb-4">Informasi Dokumen</h3>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1">No. Bukti</label>
                    <input type="text" name="no_bukti" value="{{ $noBukti }}" required readonly class="w-full border border-gray-300 rounded p-2 text-sm bg-gray-50 font-bold text-gray-700">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red"></textarea>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 uppercase border-b border-gray-100 pb-2 mb-4">Rute Mutasi</h3>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-red-600 uppercase mb-1">Dari Lokasi Asal</label>
                    <select x-model="lokasiAsal" @change="fetchUnits()" :disabled="isAsalLocked" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-red-500 mb-2 font-bold disabled:bg-gray-100">
                        <option value="">-- Pilih Lokasi Asal --</option>
                        @foreach($lokasiStatis as $lok)
                            <option value="{{ $lok }}">{{ $lok }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="lokasi_asal" :value="lokasiAsal">

                    <select x-show="lokasiAsal === 'POP'" x-model="lokasiAsalPopId" name="lokasi_asal_pop_id" @change="fetchUnits()" style="display: none;" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-red-500 bg-red-50">
                        <option value="">-- Pilih Nama POP --</option>
                        @foreach($pops as $pop)
                            <option value="{{ $pop->id }}">{{ $pop->nama_sales }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-green-600 uppercase mb-1">Ke Lokasi Tujuan</label>
                    <select x-model="lokasiTujuan" :disabled="isTujuanLocked" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-green-500 mb-2 font-bold disabled:bg-gray-100">
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        @foreach($lokasiStatis as $lok)
                            <option value="{{ $lok }}">{{ $lok }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="lokasi_tujuan" :value="lokasiTujuan">

                    <select x-show="lokasiTujuan === 'POP'" name="lokasi_tujuan_pop_id" style="display: none;" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-green-500 bg-green-50">
                        <option value="">-- Pilih Nama POP --</option>
                        @foreach($pops as $pop)
                            <option value="{{ $pop->id }}">{{ $pop->nama_sales }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full bg-honda-red hover:bg-red-800 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-colors">
                Simpan Mutasi
            </button>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm h-full flex flex-col">
                <h3 class="text-sm font-bold text-gray-800 uppercase border-b border-gray-100 pb-2 mb-4">Pilih Unit & Keranjang Mutasi</h3>

                <div class="flex items-end gap-2 mb-6">
                    <div class="flex-1 relative">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Cari Motor di Lokasi Asal</label>
                        <select x-model="selectedUnitId" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red font-mono">
                            <option value="">-- Ketik/Pilih No Rangka atau Mesin --</option>
                            <template x-for="unit in availableUnits" :key="unit.id">
                                <option :value="unit.id" x-text="unit.type.nama_type + ' | M: ' + unit.no_mesin + ' | R: ' + unit.no_rangka"></option>
                            </template>
                        </select>
                        <div x-show="availableUnits.length === 0 && lokasiAsal !== ''" style="display: none;" class="absolute top-full left-0 mt-1 text-[10px] text-red-500 font-bold">Tidak ada unit tersedia di lokasi asal ini.</div>
                    </div>
                    <button type="button" @click="addUnitToCart()" class="bg-gray-800 text-white font-bold px-5 py-2 rounded text-sm hover:bg-gray-900">Tambahkan</button>
                </div>

                <div class="flex-1 overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 text-[10px] uppercase text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="py-2 px-3">Tipe & Warna</th>
                                <th class="py-2 px-3">No. Mesin</th>
                                <th class="py-2 px-3">No. Rangka</th>
                                <th class="py-2 px-3 text-center w-10">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <template x-for="(cartItem, index) in cart" :key="index">
                                <tr>
                                    <td class="py-2 px-3 font-bold">
                                        <input type="hidden" name="motor_unit_ids[]" :value="cartItem.id">
                                        <span x-text="cartItem.type.nama_type"></span>
                                        <div class="text-[9px] text-gray-400 font-normal" x-text="cartItem.color.warna"></div>
                                    </td>
                                    <td class="py-2 px-3 font-mono" x-text="cartItem.no_mesin"></td>
                                    <td class="py-2 px-3 font-mono" x-text="cartItem.no_rangka"></td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" @click="removeUnitFromCart(index)" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="cart.length === 0">
                                <td colspan="4" class="py-10 text-center text-gray-400 italic">Keranjang mutasi masih kosong.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function mutasiForm(kunciAsal, kunciTujuan) {
        return {
            lokasiAsal: kunciAsal || '',
            lokasiAsalPopId: '',
            lokasiTujuan: kunciTujuan || '',
            isAsalLocked: kunciAsal !== '',
            isTujuanLocked: kunciTujuan !== '',
            availableUnits: [],
            selectedUnitId: '',
            cart: [],

            init() {
                if (this.lokasiAsal !== '') {
                    this.fetchUnits();
                }
            },

            fetchUnits() {
                if(this.lokasiAsal === 'POP' && this.lokasiAsalPopId === '') {
                    this.availableUnits = [];
                    return;
                }
                if(this.lokasiAsal === '') {
                    this.availableUnits = [];
                    return;
                }

                let url = `/transaction/mutasi/api/available-units?posisi_stok=${this.lokasiAsal}`;
                if (this.lokasiAsal === 'POP') url += `&lokasi_pop_id=${this.lokasiAsalPopId}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.availableUnits = data;
                        this.cart = [];
                    });
            },

            addUnitToCart() {
                if(this.selectedUnitId === '') return;

                const isAlreadyInCart = this.cart.some(item => item.id == this.selectedUnitId);
                if (isAlreadyInCart) {
                    alert('Unit ini sudah ada di keranjang!');
                    return;
                }

                const unit = this.availableUnits.find(u => u.id == this.selectedUnitId);
                if(unit) {
                    this.cart.push(unit);
                    this.selectedUnitId = '';
                }
            },

            removeUnitFromCart(index) {
                this.cart.splice(index, 1);
            }
        }
    }
</script>
@endsection
