@extends('layouts.app')

@section('content')
<div x-data="dokumenManager()" @keydown.escape.window="isEditOpen = false; isDetailOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Manajemen Dokumen Kendaraan
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Pantau dan kelola proses penerimaan STNK, Notice Pajak, dan BPKB Konsumen.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col lg:flex-row justify-between items-center gap-4">
            <form action="{{ route('samsat.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">

                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama STNK, SJK..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 text-sm outline-none focus:border-honda-red">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status_dokumen" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-2 px-3 text-sm outline-none bg-white focus:border-honda-red w-full">
                        <option value="">Semua Status Dokumen</option>
                        <option value="belum" {{ $status_dokumen == 'belum' ? 'selected' : '' }}>Belum Ada Dokumen</option>
                        <option value="stnk_saja" {{ $status_dokumen == 'stnk_saja' ? 'selected' : '' }}>STNK Selesai (BPKB Belum)</option>
                        <option value="bpkb_saja" {{ $status_dokumen == 'bpkb_saja' ? 'selected' : '' }}>Hanya BPKB (STNK Belum)</option>
                        <option value="selesai" {{ $status_dokumen == 'selesai' ? 'selected' : '' }}>Selesai Keduanya</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-2 px-3 text-sm outline-none bg-white focus:border-honda-red w-full">
                        <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                    </select>
                </div>

                <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
                @if($search || $status_dokumen)
                    <a href="{{ route('samsat.index') }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold">No. SJK / Tgl Kirim</th>
                        <th class="py-4 px-6 font-semibold">Nama STNK / SPK</th>
                        <th class="py-4 px-6 font-semibold">Unit & Pembayaran</th>
                        <th class="py-4 px-6 font-semibold text-center">Status Dokumen</th>
                        <th class="py-4 px-6 text-center w-36 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dokumens as $doc)
                        @php
                            $stnkSelesai = $doc->samsat && $doc->samsat->tgl_terima_stnk;
                            $bpkbSelesai = $doc->samsat && $doc->samsat->tgl_terima_bpkb;

                            $statusColor = 'bg-red-50 text-red-600 border-red-200';
                            $statusText = 'Belum Ada';

                            if($stnkSelesai && $bpkbSelesai) {
                                $statusColor = 'bg-green-50 text-green-700 border-green-200';
                                $statusText = 'Selesai Keduanya';
                            } elseif($stnkSelesai && !$bpkbSelesai) {
                                $statusColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                $statusText = 'STNK Selesai';
                            } elseif(!$stnkSelesai && $bpkbSelesai) {
                                $statusColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                $statusText = 'Hanya BPKB';
                            }

                            $pembayaran = $doc->spk->leasing_id ? 'KREDIT - ' . ($doc->spk->leasing->nama_leasing ?? '') : 'KONTAN';
                        @endphp

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800">{{ $doc->no_bukti }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($doc->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800 uppercase">{{ $doc->spk->nama_stnk ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $doc->spk->no_spk ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-semibold text-gray-700 truncate max-w-[180px]">{{ $doc->spk->motorType->nama_type ?? '-' }}</div>
                                <div class="text-[10px] font-bold mt-1 px-1.5 py-0.5 inline-block rounded {{ $doc->spk->leasing_id ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $pembayaran }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button @click="openDetailModal({{ $doc }}, '{{ $pembayaran }}')" class="text-gray-500 hover:text-gray-800 transition-colors" title="Detail Data Lengkap">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button @click="openEditModal({{ $doc }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Update Dokumen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">Data Dokumen Kendaraan tidak ditemukan.</td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $dokumens->links() }}
        </div>
    </div>

    <div x-show="isDetailOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isDetailOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isDetailOpen = false"></div>
            <div x-show="isDetailOpen" x-transition class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-3xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-honda-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Detail SJK & Kendaraan
                    </h3>
                    <button @click="isDetailOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                    <div class="space-y-3">
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. Surat Jalan</span><div class="font-bold text-gray-800" x-text="dNoSjk"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. SPK</span><div class="font-bold text-gray-800" x-text="dNoSpk"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Nama Pemohon</span><div class="font-semibold text-gray-800 uppercase" x-text="dPemohon"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Nama STNK</span><div class="font-semibold text-gray-800 uppercase" x-text="dStnk"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. Telepon</span><div class="font-semibold text-gray-800" x-text="dTelp"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Alamat Lengkap</span><div class="font-semibold text-gray-800" x-text="dAlamat"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Metode Pembayaran</span><div class="font-bold text-honda-red" x-text="dPembayaran"></div></div>
                    </div>
                    <div class="space-y-3">
                        <div><span class="block text-xs text-gray-500 mb-0.5">Tipe Motor</span><div class="font-bold text-gray-800" x-text="dTipe"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Warna</span><div class="font-semibold text-gray-800" x-text="dWarna"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">Tahun</span><div class="font-semibold text-gray-800" x-text="dTahun"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. Mesin</span><div class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded inline-block" x-text="dMesin"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. Rangka</span><div class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded inline-block" x-text="dRangka"></div></div>
                        <div><span class="block text-xs text-gray-500 mb-0.5">No. Kunci</span><div class="font-semibold text-gray-800" x-text="dKunci"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="isEditOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isEditOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isEditOpen = false"></div>
            <div x-show="isEditOpen" x-transition class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-4xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Update Dokumen Kendaraan</h3>
                        <p class="text-xs text-gray-500" x-text="'Nama STNK: ' + dStnk"></p>
                    </div>
                    <button type="button" @click="isEditOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form @submit.prevent="submitUpdate" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    @csrf @method('PUT')

                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2">DATA STNK & PAJAK</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">No. Polisi</label>
                                <input type="text" name="no_polisi" x-model="eNoPolisi" class="w-full border border-gray-300 rounded-lg p-2 text-sm uppercase outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">No. STNK</label>
                                <input type="text" name="no_stnk" x-model="eNoStnk" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal STNK Jadi</label>
                                <input type="date" name="tgl_stnk" x-model="eTglStnk" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Terima STNK (Dealer)</label>
                                <input type="date" name="tgl_terima_stnk" x-model="eTglTerimaStnk" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 bg-white p-3 rounded-lg border border-blue-100 shadow-sm">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Jumlah Motor Dimiliki</label>
                                <input type="number" name="jumlah_motor" x-model="eJumlahMotor" min="1" required
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Pajak Progresif (Rp)</label>
                                <input type="number" name="pajak_progresif" x-model="ePajakProgresif"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-blue-500 font-mono font-bold bg-white text-gray-800">
                                <p class="text-[10px] text-gray-400 mt-1">Masukkan tagihan pajak progresif tanpa titik/koma.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-amber-800 mb-3 border-b border-amber-200 pb-2">DATA BPKB</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">No. BPKB</label>
                                <input type="text" name="no_bpkb" x-model="eNoBpkb" class="w-full border border-gray-300 rounded-lg p-2 text-sm uppercase outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal BPKB Jadi</label>
                                <input type="date" name="tgl_bpkb" x-model="eTglBpkb" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Terima BPKB (Dealer)</label>
                                <input type="date" name="tgl_terima_bpkb" x-model="eTglTerimaBpkb" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" @click="isEditOpen = false" class="px-6 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50" :disabled="isEditing">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700 flex items-center gap-2" :disabled="isEditing">
                            <svg x-show="!isEditing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <svg x-show="isEditing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isEditing ? 'Memperbarui...' : 'Simpan Update'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function dokumenManager() {
        return {
            isDetailOpen: false,

            dNoSjk: '', dNoSpk: '', dPemohon: '', dStnk: '', dTelp: '', dAlamat: '',
            dPembayaran: '', dTipe: '', dWarna: '', dTahun: '', dMesin: '', dRangka: '', dKunci: '',

            isEditOpen: false,
            isEditing: false,

            eSjkId: '',
            eNoPolisi: '', eNoStnk: '', eTglStnk: '', eTglTerimaStnk: '',
            eJumlahMotor: 1, ePajakProgresif: '', // set string kosong sebagai default agar aman
            eNoBpkb: '', eTglBpkb: '', eTglTerimaBpkb: '',

           openDetailModal(doc, pembayaran) {
                this.dNoSjk = doc.no_bukti;
                this.dNoSpk = doc.spk ? doc.spk.no_spk : '-';
                this.dPemohon = doc.spk ? doc.spk.nama_pemohon : '-';
                this.dStnk = doc.spk ? doc.spk.nama_stnk : '-';
                this.dTelp = doc.spk ? (doc.spk.telepon || '-') : '-';

                if (doc.spk) {
                    let fullAlamat = doc.spk.alamat || '';
                    if (doc.spk.rt_rw) fullAlamat += ', RT/RW ' + doc.spk.rt_rw;
                    if (doc.spk.desa_kelurahan) fullAlamat += ', Desa/Kel. ' + doc.spk.desa_kelurahan;
                    if (doc.spk.kecamatan) fullAlamat += ', Kec. ' + doc.spk.kecamatan;
                    if (doc.spk.kota_kabupaten) fullAlamat += ', Kab. ' + doc.spk.kota_kabupaten;
                    this.dAlamat = fullAlamat || '-';
                } else {
                    this.dAlamat = '-';
                }

                this.dPembayaran = pembayaran;
                this.dTipe = (doc.spk && doc.spk.motor_type) ? doc.spk.motor_type.nama_type : '-';
                this.dWarna = (doc.spk && doc.spk.motor_color) ? doc.spk.motor_color.warna : '-';
                this.dTahun = (doc.spk && doc.spk.motor_type) ? doc.spk.motor_type.tahun_pembuatan : '-';
                this.dMesin = doc.motor_unit ? doc.motor_unit.no_mesin : '-';
                this.dRangka = doc.motor_unit ? doc.motor_unit.no_rangka : '-';
                this.dKunci = doc.motor_unit ? doc.motor_unit.no_kunci : '-';

                this.isDetailOpen = true;
            },

            openEditModal(doc) {
                this.eSjkId = doc.id;
                this.dStnk = doc.spk ? doc.spk.nama_stnk : '-';

                let s = doc.samsat || {};
                this.eNoPolisi = s.no_polisi || '';
                this.eNoStnk = s.no_stnk || '';
                this.eTglStnk = s.tgl_stnk || '';
                this.eTglTerimaStnk = s.tgl_terima_stnk || '';

                this.eJumlahMotor = s.jumlah_motor || 1;
                // Jangan diisi 0 jika null, biarkan kosong agar tidak ada error casting input
                this.ePajakProgresif = s.pajak_progresif || '';

                this.eNoBpkb = s.no_bpkb || '';
                this.eTglBpkb = s.tgl_bpkb || '';
                this.eTglTerimaBpkb = s.tgl_terima_bpkb || '';

                this.isEditOpen = true;
            },

            submitUpdate(event) {
                this.isEditing = true;
                let formData = new FormData(event.target);

                fetch('/samsat/' + this.eSjkId, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    // PENTING: Matikan loading seketika di sini sebelum memanggil Swal.fire
                    this.isEditing = false;

                    if(data.success) {
                        Swal.fire({
                            icon: 'success', title: 'Berhasil!', text: data.message,
                            timer: 1500, showConfirmButton: false
                        }).then(() => {
                            this.isEditOpen = false;
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    // Matikan loading seketika jika error
                    this.isEditing = false;

                    let errorMsg = 'Terjadi kesalahan sistem.';
                    if(error.errors) errorMsg = Object.values(error.errors)[0][0];
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: errorMsg });
                });
            }
        }
    }
</script>
@endsection
