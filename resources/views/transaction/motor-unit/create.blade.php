@extends('layouts.app')

@section('content')
<div class="max-w-[95rem] mx-auto" x-data="penerimaanForm()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Form Registrasi Penerimaan Grup Unit
            </h2>
        </div>
        <a href="{{ route('motor-unit.index') }}" class="bg-gray-100 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-sm">
            Kembali
        </a>
    </div>

    <form @submit.prevent="submitAllData">
        @csrf
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Data Dokumen Pengiriman (Header)</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Bukti</label>
                    <input type="text" placeholder="Otomatis Tergenerate" readonly class="w-full bg-gray-50 border border-gray-200 text-gray-400 font-bold rounded-lg p-2.5 text-sm cursor-not-allowed outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Masuk</label>
                    <input type="date" name="tanggal" x-model="header.tanggal" required class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Kendaraan (Plat Mobil)</label>
                    <input type="text" name="no_kendaraan" x-model="header.no_kendaraan" placeholder="Contoh: Z 9188 FA" required class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ekspedisi Pengirim</label>
                    <input type="text" name="ekspedisi" x-model="header.ekspedisi" placeholder="Nama PT / Driver" required class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. SJ (Surat Jalan)</label>
                    <input type="text" name="no_sj" x-model="header.no_sj" required class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. ND</label>
                    <input type="text" name="no_nd" x-model="header.no_nd" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. SO</label>
                    <input type="text" name="no_so" x-model="header.no_so" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red uppercase">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Daftar Rincian Spesifikasi Unit Motor</h3>
                <button type="button" @click="openFormModal" class="bg-gray-900 text-white font-bold py-1.5 px-4 rounded-lg text-xs hover:bg-gray-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Unit Motor Ke Tabel
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-3">Tipe & Warna</th>
                            <th class="py-3 px-3">No. Mesin</th>
                            <th class="py-3 px-3">No. Rangka</th>
                            <th class="py-3 px-3 text-center">No. Kunci / Seri</th>
                            <th class="py-3 px-3 text-center">Thn. Buat</th>
                            <th class="py-3 px-3">No. Accu</th>
                            <th class="py-3 px-3">Posisi Stok</th>
                            <th class="py-3 px-3 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in details" :key="index">
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 text-center text-gray-400 font-mono" x-text="index + 1"></td>
                                <td class="py-2 px-3">
                                    <div class="font-bold text-gray-800" x-text="item.nama_type"></div>
                                    <div class="text-gray-500 mt-0.5" x-text="item.warna + ' (' + item.kode_warna + ')'"></div>
                                </td>
                                <td class="py-2 px-3 font-bold font-mono tracking-wide text-gray-900 uppercase" x-text="item.no_mesin"></td>
                                <td class="py-2 px-3 font-mono text-gray-700 uppercase" x-text="item.no_rangka"></td>
                                <td class="py-2 px-3 text-center">
                                    <div class="font-bold text-blue-600 font-mono uppercase" x-text="item.no_kunci"></div>
                                    <div class="text-gray-400 font-mono text-[10px] uppercase" x-text="item.no_seri_kunci"></div>
                                </td>
                                <td class="py-2 px-3 text-center font-mono" x-text="item.tahun_pembuatan"></td>
                                <td class="py-2 px-3 font-mono font-bold uppercase text-gray-800" x-text="item.no_accu"></td>
                                <td class="py-2 px-3 font-bold uppercase text-amber-700" x-text="item.posisi_display"></td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="details.length === 0">
                            <td colspan="9" class="py-8 text-center text-gray-400 italic">Belum ada unit yang dimasukkan ke daftar. Klik tombol di kanan atas untuk mengisi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-6 mt-6">
                <a href="{{ route('motor-unit.index') }}" class="bg-white border border-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-lg text-sm hover:bg-gray-50">Batal</a>
                <button type="submit" class="bg-honda-red text-white font-bold py-2.5 px-8 rounded-lg text-sm hover:bg-red-700 flex items-center gap-2" :disabled="isSubmitting || details.length === 0">
                    <svg x-show="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Semua Unit'"></span>
                </button>
            </div>
        </div>
    </form>

    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Input Fisik Speseifikasi Unit Motor</h3>
                    <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 font-bold">&times;</button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Posisi Awal Kendaraan</label>
                        <select x-model="form.posisi_stok" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white font-bold text-amber-800">
                            <template x-for="lok in lokasiStatis" :key="lok">
                                <option :value="lok" x-text="lok" :selected="lok === 'Showroom Pusat'"></option>
                            </template>
                            <option value="POP">POP (Titip Pameran)</option>
                        </select>
                    </div>
                    <div x-show="form.posisi_stok === 'POP'">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Cabang POP</label>
                        <select x-model="form.lokasi_pop_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white">
                            <option value="">-- Pilih Cabang POP --</option>
                            <template x-for="p in pops" :key="p.id">
                                <option :value="p.id" x-text="p.nama_sales"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Tipe Motor</label>
                        <select x-model="form.motor_type_id" @change="syncTypeSelect" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white">
                            <option value="">-- Pilih Tipe --</option>
                            <template x-for="t in types" :key="t.id">
                                <option :value="t.id" x-text="t.nama_type"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Tipe</label>
                        <input type="text" x-model="form.kode_tipe" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg p-2 font-bold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Warna</label>
                        <select x-model="form.motor_color_id" @change="syncColorSelect" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white">
                            <option value="">-- Pilih Warna --</option>
                            <template x-for="c in availableColors" :key="c.id">
                                <option :value="c.id" x-text="c.warna"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Warna</label>
                        <input type="text" x-model="form.kode_warna" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg p-2 font-bold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Kunci</label>
                        <input type="text" x-model="form.no_kunci" @input="calculateKeyFields" placeholder="Contoh: 2026E155" class="w-full border border-gray-300 rounded-lg p-2 uppercase text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Seri Kunci</label>
                        <input type="text" x-model="form.no_seri_kunci" class="w-full border border-gray-300 rounded-lg p-2 uppercase text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Mesin</label>
                        <input type="text" x-model="form.no_mesin" class="w-full border border-gray-300 rounded-lg p-2 uppercase text-sm font-bold tracking-wider">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Rangka</label>
                        <input type="text" x-model="form.no_rangka" class="w-full border border-gray-300 rounded-lg p-2 uppercase text-sm font-bold tracking-wider">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Thn. Pembuatan</label>
                        <input type="number" x-model="form.tahun_pembuatan" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg p-2 font-bold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Accu</label>
                        <input type="text" x-model="form.no_accu" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg p-2 font-bold cursor-not-allowed">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isModalOpen = false" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold text-xs">Batal</button>
                    <button type="button" @click="pushUnitToTable" class="bg-gray-900 text-white px-5 py-2 rounded-lg font-bold text-xs hover:bg-gray-800">Masukkan Data</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function penerimaanForm() {
        return {
            types: @json($types),
            lokasiStatis: @json($lokasiStatis),
            pops: @json($pops),
            isModalOpen: false,
            isSubmitting: false,
            availableColors: [],
            header: {
                tanggal: new Date().toISOString().split('T')[0],
                no_kendaraan: '',
                ekspedisi: '',
                no_sj: '',
                no_nd: '',
                no_so: ''
            },
            form: {
                posisi_stok: 'Showroom Pusat',
                lokasi_pop_id: '',
                motor_type_id: '',
                nama_type: '',
                kode_tipe: '',
                motor_color_id: '',
                warna: '',
                kode_warna: '',
                no_kunci: '',
                no_seri_kunci: '',
                no_mesin: '',
                no_rangka: '',
                tahun_pembuatan: '',
                no_accu: '',
                posisi_display: ''
            },
            details: [],

            openFormModal() {
                this.form = {
                    posisi_stok: 'Showroom Pusat',
                    lokasi_pop_id: '',
                    motor_type_id: '',
                    nama_type: '',
                    kode_tipe: '',
                    motor_color_id: '',
                    warna: '',
                    kode_warna: '',
                    no_kunci: '',
                    no_seri_kunci: '',
                    no_mesin: '',
                    no_rangka: '',
                    tahun_pembuatan: '',
                    no_accu: '',
                    posisi_display: ''
                };
                this.availableColors = [];
                this.isModalOpen = true;
            },

            syncTypeSelect() {
                let matched = this.types.find(x => x.id == this.form.motor_type_id);
                if (matched) {
                    this.form.nama_type = matched.nama_type;
                    this.form.kode_tipe = matched.kode_tipe;
                    this.availableColors = matched.colors;
                } else {
                    this.form.nama_type = '';
                    this.form.kode_tipe = '';
                    this.availableColors = [];
                }
                this.form.motor_color_id = '';
                this.form.warna = '';
                this.form.kode_warna = '';
            },

            syncColorSelect() {
                let matched = this.availableColors.find(x => x.id == this.form.motor_color_id);
                if (matched) {
                    this.form.warna = matched.warna;
                    this.form.kode_warna = matched.kode_warna;
                } else {
                    this.form.warna = '';
                    this.form.kode_warna = '';
                }
            },

            calculateKeyFields() {
                let input = this.form.no_kunci.trim();
                if (input.length >= 4) {
                    this.form.tahun_pembuatan = input.substring(0, 4);
                } else {
                    this.form.tahun_pembuatan = '';
                }

                if (input.length >= 5) {
                    let prefix = input.substring(0, 4);
                    let codeLetter = input.charAt(4).toUpperCase();
                    let suffix = input.substring(5);

                    let letterMap = {
                        'A': '01', 'B': '02', 'C': '03', 'D': '04', 'E': '05', 'F': '06',
                        'G': '07', 'H': '08', 'I': '09', 'J': '10', 'K': '11', 'L': '12'
                    };
                    let realMonth = letterMap[codeLetter] || codeLetter;
                    this.form.no_accu = prefix + realMonth + suffix;
                } else {
                    this.form.no_accu = '';
                }
            },

            pushUnitToTable() {
                if (!this.form.motor_type_id || !this.form.motor_color_id || !this.form.no_mesin || !this.form.no_rangka || !this.form.no_kunci || !this.form.no_seri_kunci) {
                    Swal.fire({ icon: 'error', title: 'Data Belum Lengkap', text: 'Semua kolom wajib diisi kecuali No. ND/SO Header.' });
                    return;
                }

                if (this.form.posisi_stok === 'POP') {
                    if (!this.form.lokasi_pop_id) {
                        Swal.fire({ icon: 'error', title: 'POP Belum Dipilih', text: 'Silakan tentukan cabang POP penitipan unit.' });
                        return;
                    }
                    let matchedPop = this.pops.find(x => x.id == this.form.lokasi_pop_id);
                    this.form.posisi_display = 'POP - ' + (matchedPop ? matchedPop.nama_sales : 'Unknown');
                } else {
                    this.form.posisi_display = this.form.posisi_stok;
                }

                this.details.push(JSON.parse(JSON.stringify(this.form)));
                this.isModalOpen = false;
            },

            removeItem(index) {
                this.details.splice(index, 1);
            },

            submitAllData() {
                this.isSubmitting = true;
                let payload = {
                    tanggal: this.header.tanggal,
                    no_kendaraan: this.header.no_kendaraan,
                    ekspedisi: this.header.ekspedisi,
                    no_sj: this.header.no_sj,
                    no_nd: this.header.no_nd,
                    no_so: this.header.no_so,
                    details: this.details
                };

                fetch('{{ route("motor-unit.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: true })
                        .then(() => {
                            window.location.href = '{{ route("motor-unit.index") }}';
                        });
                    }
                })
                .catch(error => {
                    this.isSubmitting = false;
                    let msg = 'Terjadi gangguan sistem.';
                    if (error.errors) {
                        msg = Object.values(error.errors)[0][0];
                    } else if (error.message) {
                        msg = error.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg });
                });
            }
        }
    }
</script>
@endsection