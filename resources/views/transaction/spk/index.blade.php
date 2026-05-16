@extends('layouts.app')

@section('content')
<div x-data="spkManager()" @keydown.escape.window="isCreateModalOpen = false; isEditModalOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Surat Pesanan Kendaraan (SPK)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola data pemesanan unit, biodata konsumen, dan skema pembayaran.</p>
        </div>

        <button @click="isCreateModalOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat SPK Baru
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
            <form action="{{ route('spk.index') }}" method="GET" class="w-full sm:w-96 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No SPK, Pemohon, atau Sales..." class="w-full border border-gray-300 rounded-lg py-2 pl-10 px-4 outline-none focus:border-honda-red text-sm">
            </form>
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $spks->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $spks->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $spks->total() }}</span> data
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold">No. SPK & Tgl</th>
                        <th class="py-4 px-6 font-semibold">Konsumen</th>
                        <th class="py-4 px-6 font-semibold">Tipe Unit & Warna</th>
                        <th class="py-4 px-6 font-semibold">Pembayaran</th>
                        <th class="py-4 px-6 text-center w-40 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($spks as $spk)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800">{{ $spk->no_spk }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($spk->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800">{{ $spk->nama_pemohon }}</div>
                                <div class="text-xs text-gray-500 mt-1">Sales: {{ $spk->sales->nama_sales ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-700">
                                <div class="font-bold">{{ $spk->motorType->nama_type ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $spk->motorColor->warna ?? '-' }} ({{ $spk->motorType->tahun_pembuatan ?? '-' }})</div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $spk->jenis_pembayaran == 'Cash' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
                                    {{ $spk->jenis_pembayaran }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('spk.print', $spk->id) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700" title="Cetak SPK">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <button @click="openEditModal({{ $spk }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('spk.destroy', $spk->id) }}" method="POST" onsubmit="confirmDelete(event, this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Data SPK belum tersedia.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $spks->links() }}
        </div>
    </div>

    <div x-show="isCreateModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isCreateModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isCreateModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="isCreateModalOpen" x-transition class="inline-block align-bottom bg-gray-50 rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-6xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center sticky top-0 z-20">
                    <h3 class="text-xl font-bold text-gray-900">Form Pembuatan SPK</h3>
                    <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form action="{{ route('spk.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. SPK (Otomatis)</label>
                                <input type="text" name="no_spk" value="{{ $autoNoSpk }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" x-model="cTanggal" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-honda-red outline-none">
                            </div>
                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Sales <span class="text-red-500">*</span></label>
                                <div @click.away="openSales = false" class="relative">
                                    <div @click="openSales = !openSales" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-honda-red">
                                        <span x-text="selectedSalesName || 'Pilih / Cari Sales...'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div x-show="openSales" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                            <input type="text" x-model="searchSales" placeholder="Ketik nama..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                        </div>
                                        <template x-for="s in sales.filter(x => x.nama_sales.toLowerCase().includes(searchSales.toLowerCase()))" :key="s.id">
                                            <div @click="setSales(s)" class="p-2.5 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50" x-text="s.nama_sales"></div>
                                        </template>
                                    </div>
                                    <input type="hidden" name="sales_id" :value="cSales" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-800 uppercase border-b pb-2 mb-4">Biodata Konsumen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div><label class="block text-xs text-gray-600 mb-1">Nama Pemohon *</label><input type="text" name="nama_pemohon" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Nama STNK *</label><input type="text" name="nama_stnk" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">NIK *</label><input type="number" name="nik" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div class="md:col-span-2"><label class="block text-xs text-gray-600 mb-1">Alamat Lengkap *</label><input type="text" name="alamat" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">RT/RW *</label><input type="text" name="rt_rw" placeholder="Misal: 01/05" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Desa/Kelurahan *</label><input type="text" name="desa_kelurahan" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Kecamatan *</label><input type="text" name="kecamatan" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Kota/Kabupaten *</label><input type="text" name="kota_kabupaten" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">No. Telp / WA *</label><input type="number" name="telepon" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div class="md:col-span-2"><label class="block text-xs text-gray-600 mb-1">Email (Opsional)</label><input type="email" name="email" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Detail Kendaraan</h4>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm font-bold text-gray-700">Jenis Transaksi:</label>
                                    <select name="jenis_pembayaran" x-model="cPembayaran" class="border border-honda-red text-honda-red font-bold rounded-lg px-3 py-1 bg-red-50 focus:outline-none">
                                        <option value="Cash">CASH</option>
                                        <option value="Kredit">KREDIT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                                <div class="md:col-span-2 relative">
                                    <label class="block text-xs text-gray-600 mb-1">Pilih Motor *</label>
                                    <div @click.away="openMotor = false" class="relative">
                                        <div @click="openMotor = !openMotor" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center">
                                            <span x-text="selectedMotorName || 'Cari Tipe Motor...'"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div x-show="openMotor" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                                <input type="text" x-model="searchMotor" placeholder="Ketik nama tipe..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                            </div>
                                            <template x-for="m in motors.filter(x => x.nama_type.toLowerCase().includes(searchMotor.toLowerCase()))" :key="m.id">
                                                <div @click="setMotor(m)" class="p-2.5 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50" x-text="m.nama_type + ' (' + m.kode_tipe + ')'"></div>
                                            </template>
                                        </div>
                                        <input type="hidden" name="motor_type_id" :value="cMotor" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Warna *</label>
                                    <select name="motor_color_id" x-model="cWarna" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:border-honda-red">
                                        <option value="">Pilih Warna</option>
                                        <template x-for="c in availableColors" :key="c.id">
                                            <option :value="c.id" x-text="c.warna"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Tahun</label>
                                    <input type="text" :value="cTahun" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 cursor-not-allowed text-center font-bold text-gray-600">
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 border border-gray-200 rounded-lg flex items-center justify-between">
                                <span class="font-bold text-gray-700">Harga OTR Kendaraan:</span>
                                <div class="text-xl font-black text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(cOtr)">Rp 0</div>
                                <input type="hidden" name="harga_otr" :value="cOtr">
                            </div>
                        </div>

                        <div x-show="cPembayaran == 'Cash'" x-transition class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm flex items-center justify-center">
                            <span class="text-xl font-black text-green-700 tracking-widest uppercase">Keterangan: Kontan / Cash</span>
                        </div>

                        <div x-show="cPembayaran == 'Kredit'" x-transition class="bg-blue-50 p-5 rounded-xl border border-blue-200 shadow-sm">
                            <h4 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-200 pb-2 mb-4">Detail Skema Kredit</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="relative">
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Leasing (Pembiayaan) *</label>
                                    <div @click.away="openLeasing = false" class="relative">
                                        <div @click="openLeasing = !openLeasing" class="w-full border border-blue-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-blue-500">
                                            <span x-text="selectedLeasingName || 'Cari Leasing...'"></span>
                                        </div>
                                        <div x-show="openLeasing" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                                <input type="text" x-model="searchLeasing" placeholder="Ketik..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none">
                                            </div>
                                            <template x-for="l in leasings.filter(x => x.nama_leasing.toLowerCase().includes(searchLeasing.toLowerCase()))" :key="l.id">
                                                <div @click="setLeasing(l)" class="p-2.5 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-50" x-text="l.nama_leasing"></div>
                                            </template>
                                        </div>
                                        <input type="hidden" name="leasing_id" :value="cLeasing">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Uang Muka (DP) (Rp) *</label>
                                    <input type="number" name="uang_muka" placeholder="Contoh: 2000000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Tanda Jadi (Rp) *</label>
                                    <input type="number" name="tanda_jadi" placeholder="Contoh: 500000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-400">
                                </div>

                                <div class="md:col-span-3 grid grid-cols-2 gap-5 p-4 border border-blue-200 bg-white rounded-lg mt-2">
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Tenor (Bulan) *</label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="tenor_bulan" placeholder="Misal: 35" class="w-24 border border-blue-300 rounded-lg p-2.5 outline-none text-center font-bold focus:border-blue-500">
                                            <span class="text-sm font-semibold text-gray-500">Bulan</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Angsuran / Cicilan (Rp) *</label>
                                        <input type="number" name="cicilan" placeholder="Misal: 1200000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none font-bold text-blue-700 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200 gap-3 sticky bottom-0 bg-white py-4">
                            <button type="button" @click="isCreateModalOpen = false" class="px-6 py-3 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-8 py-3 bg-honda-red text-white rounded-lg font-bold shadow hover:bg-red-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan SPK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="isEditModalOpen" x-transition class="inline-block align-bottom bg-gray-50 rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-6xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center sticky top-0 z-20">
                    <h3 class="text-xl font-bold text-gray-900">Edit Data SPK</h3>
                    <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <form :action="'/transaction/spk/' + eId" method="POST" class="space-y-6">
                        @csrf @method('PUT')

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. SPK</label>
                                <input type="text" name="no_spk" x-model="eNoSpk" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" x-model="eTanggal" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-honda-red outline-none">
                            </div>
                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Sales <span class="text-red-500">*</span></label>
                                <div @click.away="eOpenSales = false" class="relative">
                                    <div @click="eOpenSales = !eOpenSales" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-honda-red">
                                        <span x-text="eSalesName || 'Pilih / Cari Sales...'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div x-show="eOpenSales" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                            <input type="text" x-model="eSearchSales" placeholder="Ketik nama..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                        </div>
                                        <template x-for="s in sales.filter(x => x.nama_sales.toLowerCase().includes(eSearchSales.toLowerCase()))" :key="s.id">
                                            <div @click="setEditSales(s)" class="p-2.5 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50" x-text="s.nama_sales"></div>
                                        </template>
                                    </div>
                                    <input type="hidden" name="sales_id" :value="eSales" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-800 uppercase border-b pb-2 mb-4">Biodata Konsumen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div><label class="block text-xs text-gray-600 mb-1">Nama Pemohon *</label><input type="text" name="nama_pemohon" x-model="ePemohon" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Nama STNK *</label><input type="text" name="nama_stnk" x-model="eStnk" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">NIK *</label><input type="number" name="nik" x-model="eNik" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div class="md:col-span-2"><label class="block text-xs text-gray-600 mb-1">Alamat Lengkap *</label><input type="text" name="alamat" x-model="eAlamat" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">RT/RW *</label><input type="text" name="rt_rw" x-model="eRtRw" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Desa/Kelurahan *</label><input type="text" name="desa_kelurahan" x-model="eDesa" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Kecamatan *</label><input type="text" name="kecamatan" x-model="eKecamatan" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">Kota/Kabupaten *</label><input type="text" name="kota_kabupaten" x-model="eKota" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div><label class="block text-xs text-gray-600 mb-1">No. Telp / WA *</label><input type="number" name="telepon" x-model="eTelp" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                                <div class="md:col-span-2"><label class="block text-xs text-gray-600 mb-1">Email (Opsional)</label><input type="email" name="email" x-model="eEmail" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red"></div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Detail Kendaraan</h4>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm font-bold text-gray-700">Jenis Transaksi:</label>
                                    <select name="jenis_pembayaran" x-model="ePembayaran" class="border border-honda-red text-honda-red font-bold rounded-lg px-3 py-1 bg-red-50 focus:outline-none">
                                        <option value="Cash">CASH</option>
                                        <option value="Kredit">KREDIT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                                <div class="md:col-span-2 relative">
                                    <label class="block text-xs text-gray-600 mb-1">Pilih Motor *</label>
                                    <div @click.away="eOpenMotor = false" class="relative">
                                        <div @click="eOpenMotor = !eOpenMotor" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center">
                                            <span x-text="eMotorName || 'Cari Tipe Motor...'"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div x-show="eOpenMotor" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                                <input type="text" x-model="eSearchMotor" placeholder="Ketik nama tipe..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                            </div>
                                            <template x-for="m in motors.filter(x => x.nama_type.toLowerCase().includes(eSearchMotor.toLowerCase()))" :key="m.id">
                                                <div @click="setEditMotor(m)" class="p-2.5 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50" x-text="m.nama_type + ' (' + m.kode_tipe + ')'"></div>
                                            </template>
                                        </div>
                                        <input type="hidden" name="motor_type_id" :value="eMotor" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Warna *</label>
                                    <select name="motor_color_id" x-model="eWarna" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:border-honda-red">
                                        <option value="">Pilih Warna</option>
                                        <template x-for="c in eAvailableColors" :key="c.id">
                                            <option :value="c.id" x-text="c.warna" :selected="c.id == eWarna"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Tahun</label>
                                    <input type="text" :value="eTahun" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 cursor-not-allowed text-center font-bold text-gray-600">
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 border border-gray-200 rounded-lg flex items-center justify-between">
                                <span class="font-bold text-gray-700">Harga OTR Kendaraan:</span>
                                <div class="text-xl font-black text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(eOtr)">Rp 0</div>
                                <input type="hidden" name="harga_otr" :value="eOtr">
                            </div>
                        </div>

                        <div x-show="ePembayaran == 'Cash'" x-transition class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm flex items-center justify-center">
                            <span class="text-xl font-black text-green-700 tracking-widest uppercase">Keterangan: Kontan / Cash</span>
                        </div>

                        <div x-show="ePembayaran == 'Kredit'" x-transition class="bg-blue-50 p-5 rounded-xl border border-blue-200 shadow-sm">
                            <h4 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-200 pb-2 mb-4">Detail Skema Kredit</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="relative">
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Leasing (Pembiayaan) *</label>
                                    <div @click.away="eOpenLeasing = false" class="relative">
                                        <div @click="eOpenLeasing = !eOpenLeasing" class="w-full border border-blue-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-blue-500">
                                            <span x-text="eLeasingName || 'Cari Leasing...'"></span>
                                        </div>
                                        <div x-show="eOpenLeasing" class="absolute z-30 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                                <input type="text" x-model="eSearchLeasing" placeholder="Ketik..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none">
                                            </div>
                                            <template x-for="l in leasings.filter(x => x.nama_leasing.toLowerCase().includes(eSearchLeasing.toLowerCase()))" :key="l.id">
                                                <div @click="setEditLeasing(l)" class="p-2.5 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-50" x-text="l.nama_leasing"></div>
                                            </template>
                                        </div>
                                        <input type="hidden" name="leasing_id" :value="eLeasing">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Uang Muka (DP) (Rp) *</label>
                                    <input type="number" name="uang_muka" x-model="eDp" placeholder="Contoh: 2000000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Tanda Jadi (Rp) *</label>
                                    <input type="number" name="tanda_jadi" x-model="eTandaJadi" placeholder="Contoh: 500000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-400">
                                </div>

                                <div class="md:col-span-3 grid grid-cols-2 gap-5 p-4 border border-blue-200 bg-white rounded-lg mt-2">
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Tenor (Bulan) *</label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="tenor_bulan" x-model="eTenor" placeholder="Misal: 35" class="w-24 border border-blue-300 rounded-lg p-2.5 outline-none text-center font-bold focus:border-blue-500">
                                            <span class="text-sm font-semibold text-gray-500">Bulan</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Angsuran / Cicilan (Rp) *</label>
                                        <input type="number" name="cicilan" x-model="eCicilan" placeholder="Misal: 1200000" class="w-full border border-blue-300 rounded-lg p-2.5 outline-none font-bold text-blue-700 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200 gap-3 sticky bottom-0 bg-white py-4">
                            <button type="button" @click="isEditModalOpen = false" class="px-6 py-3 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Perbarui SPK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function spkManager() {
        return {
            // Master Data
            sales: @json($sales),
            motors: @json($motorTypes),
            leasings: @json($leasings),

            // --- STATE UNTUK CREATE ---
            isCreateModalOpen: false,
            cTanggal: new Date().toISOString().split('T')[0],
            cPembayaran: 'Cash',

            openSales: false, searchSales: '', cSales: '', selectedSalesName: '',
            setSales(s) { this.cSales = s.id; this.selectedSalesName = s.nama_sales; this.openSales = false; },

            openMotor: false, searchMotor: '', cMotor: '', selectedMotorName: '',
            availableColors: [], cWarna: '', cTahun: '', cOtr: 0,
            setMotor(m) {
                this.cMotor = m.id; this.selectedMotorName = m.nama_type; this.cTahun = m.tahun_pembuatan; this.cOtr = m.otr;
                this.availableColors = m.colors; this.cWarna = ''; this.openMotor = false;
            },

            openLeasing: false, searchLeasing: '', cLeasing: '', selectedLeasingName: '',
            setLeasing(l) { this.cLeasing = l.id; this.selectedLeasingName = l.nama_leasing; this.openLeasing = false; },


            // --- STATE UNTUK EDIT ---
            isEditModalOpen: false,
            eId: '', eNoSpk: '', eTanggal: '',
            eSales: '', eSalesName: '', eSearchSales: '', eOpenSales: false,
            ePemohon: '', eStnk: '', eNik: '', eAlamat: '', eRtRw: '', eDesa: '', eKecamatan: '', eKota: '', eTelp: '', eEmail: '',
            ePembayaran: '',
            eMotor: '', eMotorName: '', eSearchMotor: '', eOpenMotor: false, eAvailableColors: [], eWarna: '', eTahun: '', eOtr: 0,
            eLeasing: '', eLeasingName: '', eSearchLeasing: '', eOpenLeasing: false,
            eDp: '', eTandaJadi: '', eTenor: '', eCicilan: '',

            setEditSales(s) { this.eSales = s.id; this.eSalesName = s.nama_sales; this.eOpenSales = false; },
            setEditMotor(m) {
                this.eMotor = m.id; this.eMotorName = m.nama_type; this.eTahun = m.tahun_pembuatan; this.eOtr = m.otr;
                this.eAvailableColors = m.colors; this.eWarna = ''; this.eOpenMotor = false;
            },
            setEditLeasing(l) { this.eLeasing = l.id; this.eLeasingName = l.nama_leasing; this.eOpenLeasing = false; },

            openEditModal(spk) {
                this.eId = spk.id;
                this.eNoSpk = spk.no_spk;
                this.eTanggal = spk.tanggal;

                // Mapping Sales
                this.eSales = spk.sales_id;
                let s = this.sales.find(x => x.id == spk.sales_id);
                this.eSalesName = s ? s.nama_sales : '';

                // Mapping Biodata
                this.ePemohon = spk.nama_pemohon;
                this.eStnk = spk.nama_stnk;
                this.eNik = spk.nik;
                this.eAlamat = spk.alamat;
                this.eRtRw = spk.rt_rw; // MAPPING RT/RW Edit
                this.eDesa = spk.desa_kelurahan;
                this.eKecamatan = spk.kecamatan;
                this.eKota = spk.kota_kabupaten;
                this.eTelp = spk.telepon;
                this.eEmail = spk.email || '';

                // Mapping Motor & Pembayaran
                this.ePembayaran = spk.jenis_pembayaran;
                this.eMotor = spk.motor_type_id;
                let m = this.motors.find(x => x.id == spk.motor_type_id);
                if(m) {
                    this.eMotorName = m.nama_type;
                    this.eTahun = m.tahun_pembuatan;
                    this.eOtr = spk.harga_otr;
                    this.eAvailableColors = m.colors;
                    this.eWarna = spk.motor_color_id;
                }

                // Mapping Kredit
                this.eDp = spk.uang_muka || '';
                this.eTandaJadi = spk.tanda_jadi || '';
                this.eTenor = spk.tenor_bulan || '';
                this.eCicilan = spk.cicilan || '';

                this.eLeasing = spk.leasing_id || '';
                let l = this.leasings.find(x => x.id == spk.leasing_id);
                this.eLeasingName = l ? l.nama_leasing : '';

                this.isEditModalOpen = true;
            }
        }
    }
</script>
@endsection
