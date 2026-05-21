@extends('layouts.app')

@section('content')
<div x-data="riwayatManager()" @keydown.escape.window="isEditOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-gray-800 rounded-full"></div>
                Riwayat Pengajuan STNK
            </h2>
        </div>
        <a href="{{ route('pengajuan-stnk.index') }}" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Pengajuan Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form action="{{ route('pengajuan-stnk.riwayat') }}" method="GET" class="w-full flex items-center gap-3">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Bukti..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 text-sm outline-none focus:border-honda-red">
                </div>
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-2 px-3 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
                <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors">Filter</button>
                @if($search)
                    <a href="{{ route('pengajuan-stnk.riwayat') }}" class="bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold">No. Bukti / Tanggal</th>
                        <th class="py-4 px-6 font-semibold">Total Unit</th>
                        <th class="py-4 px-6 font-semibold text-right">Grand Total</th>
                        <th class="py-4 px-6 text-center w-40 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengajuans as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800">{{ $p->no_bukti }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm font-semibold text-gray-700">
                                {{ $p->details->count() }} Unit
                            </td>
                            <td class="py-4 px-6 text-sm font-bold text-gray-800 text-right font-mono">
                                Rp {{ number_format($p->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('pengajuan-stnk.print', $p->id) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700" title="Cetak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <button @click="openEditModal({{ $p }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="confirmDeleteAjax({{ $p->id }})" class="text-red-500 hover:text-red-700" title="Hapus & Kembalikan Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-12 text-center text-gray-500">Belum ada riwayat pengajuan STNK.</td></tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $pengajuans->links() }}</div>
    </div>

    <div x-show="isEditOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="isEditOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isEditOpen = false"></div>
            <div x-show="isEditOpen" x-transition class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-6xl overflow-hidden relative z-10 flex flex-col max-h-[90vh]">

                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-gray-900">Edit Pengajuan STNK</h3>
                    <button @click="isEditOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-slate-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Bukti</label>
                            <input type="text" x-model="form.no_bukti" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Pengajuan</label>
                            <input type="date" x-model="form.tanggal" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red bg-white">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead class="bg-slate-100 border-b border-gray-200 text-xs uppercase text-gray-600">
                                    <tr>
                                        <th class="py-3 px-4 text-center w-12">
                                            <input type="checkbox" @change="toggleAll" :checked="isAllChecked" class="w-4 h-4 text-honda-red rounded border-gray-300 focus:ring-honda-red">
                                        </th>
                                        <th class="py-3 px-4 font-semibold">Nama STNK</th>
                                        <th class="py-3 px-4 font-semibold">Alamat</th>
                                        <th class="py-3 px-4 font-semibold">Nama Tipe</th>
                                        <th class="py-3 px-4 font-semibold">No. Mesin</th>
                                        <th class="py-3 px-4 font-semibold text-center w-36">Input Notice Pajak</th>
                                        <th class="py-3 px-4 font-semibold text-right">ADM</th>
                                        <th class="py-3 px-4 font-semibold text-right">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(s, index) in currentSuratJalans" :key="s.id">
                                        <tr class="hover:bg-red-50/50" :class="{'bg-red-50/30': isChecked(s.id)}">
                                            <td class="py-3 px-4 text-center cursor-pointer" @click="toggleCheck(s.id)">
                                                <input type="checkbox" :value="s.id" x-model="form.checked" @click.stop class="w-4 h-4 text-honda-red rounded border-gray-300 focus:ring-honda-red">
                                            </td>
                                            <td class="py-3 px-4 font-bold text-gray-800" x-text="s.spk.nama_stnk"></td>
                                            <td class="py-3 px-4 text-xs text-gray-600 truncate max-w-[200px]" x-text="buildAlamat(s.spk)"></td>
                                            <td class="py-3 px-4 text-xs font-semibold" x-text="s.spk.motor_type.nama_type"></td>
                                            <td class="py-3 px-4 font-mono text-xs" x-text="s.motor_unit.no_mesin"></td>
                                            <td class="py-2 px-3">
                                                <input type="number" x-model="s.input_notice" :disabled="!isChecked(s.id)" placeholder="0" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono text-sm outline-none focus:border-honda-red disabled:bg-gray-100 disabled:text-gray-400">
                                            </td>
                                            <td class="py-3 px-4 text-right font-mono text-xs" x-text="isChecked(s.id) ? formatRupiah(admValue) : '0'"></td>
                                            <td class="py-3 px-4 text-right font-mono text-xs font-bold" x-text="isChecked(s.id) ? formatRupiah((Number(s.input_notice) || 0) + admValue) : '0'"></td>
                                        </tr>
                                    </template>
                                    <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                                        <td colspan="5" class="py-3 px-4 text-right">TOTAL :</td>
                                        <td class="py-3 px-4 text-right font-mono text-red-600" x-text="formatRupiah(totalPajak)"></td>
                                        <td class="py-3 px-4 text-right font-mono text-red-600" x-text="formatRupiah(totalAdm)"></td>
                                        <td class="py-3 px-4 text-right font-mono text-red-600" x-text="formatRupiah(totalPajak + totalAdm)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-800 text-sm">BIAYA TAMBAHAN (Opsional)</h4>
                            <button type="button" @click="addTambahan" class="text-xs bg-gray-800 text-white px-3 py-1.5 rounded hover:bg-gray-900 transition-colors">
                                + Tambah Baris
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(t, index) in form.tambahans" :key="index">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <input type="text" x-model="t.keterangan" placeholder="Nama Tambahan" class="flex-1 border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red">
                                    <div class="flex items-center gap-2">
                                        <input type="number" x-model.number="t.nominal" placeholder="Biaya" class="w-32 border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red font-mono">
                                        <span class="text-xs text-gray-500 font-bold">x <span x-text="checkedCount"></span> unit</span>
                                        <div class="w-32 text-right font-mono font-bold text-sm bg-white border border-gray-200 p-2 rounded" x-text="formatRupiah(t.nominal * checkedCount)"></div>
                                        <button @click="removeTambahan(index)" class="text-red-500 hover:text-red-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="form.tambahans.length === 0" class="text-xs text-gray-400 italic">Belum ada biaya tambahan.</div>
                        </div>
                    </div>

                    <div class="bg-slate-800 rounded-xl p-5 text-white flex flex-col sm:flex-row justify-between items-center shadow-lg">
                        <div class="text-sm mb-3 sm:mb-0">
                            <div class="text-gray-300">Total Unit Terpilih: <span class="font-bold text-white text-lg" x-text="checkedCount"></span></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400 font-bold tracking-wider mb-1">GRAND TOTAL KESELURUHAN</div>
                            <div class="text-3xl font-mono font-bold text-emerald-400" x-text="formatRupiah(grandTotal)"></div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-end gap-3 shrink-0">
                    <button type="button" @click="isEditOpen = false" class="px-6 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50" :disabled="isSubmitting">Batal</button>
                    <button type="button" @click="submitUpdate" class="px-8 py-2.5 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700 flex items-center gap-2" :disabled="isSubmitting || checkedCount === 0">
                        <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function riwayatManager() {
        return {
            availableSuratJalans: @json($availableSuratJalans),
            admValue: {{ $admValue }},
            isEditOpen: false,
            isSubmitting: false,
            currentSuratJalans: [],

            form: {
                id: '',
                no_bukti: '',
                tanggal: '',
                checked: [],
                tambahans: []
            },

            isChecked(id) {
                return this.form.checked.map(String).includes(String(id));
            },

            get checkedCount() { return this.form.checked.length; },
            get isAllChecked() { return this.currentSuratJalans.length > 0 && this.checkedCount === this.currentSuratJalans.length; },

            get totalPajak() {
                return this.currentSuratJalans
                    .filter(s => this.isChecked(s.id))
                    .reduce((sum, s) => sum + (Number(s.input_notice) || 0), 0);
            },
            get totalAdm() { return this.checkedCount * this.admValue; },
            get totalTambahan() {
                return this.form.tambahans.reduce((sum, t) => sum + (t.nominal * this.checkedCount), 0);
            },
            get grandTotal() { return this.totalPajak + this.totalAdm + this.totalTambahan; },

            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka || 0);
            },

            buildAlamat(spk) {
                let addr = spk.alamat || '';
                if(spk.rt_rw) addr += ', RT/RW ' + spk.rt_rw;
                if(spk.desa_kelurahan) addr += ', Desa ' + spk.desa_kelurahan;
                if(spk.kecamatan) addr += ', Kec. ' + spk.kecamatan;
                return addr;
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.form.checked = this.currentSuratJalans.map(s => String(s.id));
                } else {
                    this.form.checked = [];
                }
            },

            toggleCheck(id) {
                const strId = String(id);
                const idx = this.form.checked.findIndex(val => String(val) === strId);
                if (idx > -1) this.form.checked.splice(idx, 1);
                else this.form.checked.push(strId);
            },

            addTambahan() {
                this.form.tambahans.push({ keterangan: '', nominal: 0 });
            },

            removeTambahan(idx) {
                this.form.tambahans.splice(idx, 1);
            },

            openEditModal(p) {
                // Mapping ulang mengambil langsung dari properti suratJalan
                let attachedSuratJalans = p.details.map(d => {
                    let sj = d.surat_jalan;
                    sj.input_notice = d.notice_pajak;
                    return sj;
                });

                let available = this.availableSuratJalans.map(s => {
                    s.input_notice = '';
                    return s;
                });

                this.currentSuratJalans = [...available, ...attachedSuratJalans];

                this.form.id = p.id;
                this.form.no_bukti = p.no_bukti;
                this.form.tanggal = p.tanggal;
                this.form.checked = attachedSuratJalans.map(s => String(s.id));
                this.form.tambahans = p.tambahans.map(t => ({ keterangan: t.keterangan, nominal: t.nominal }));

                this.isEditOpen = true;
            },

            submitUpdate() {
                let incompletePajak = this.currentSuratJalans.some(s => this.isChecked(s.id) && (s.input_notice === '' || s.input_notice < 0));

                if(incompletePajak) {
                    Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Pastikan Anda telah mengisi nominal Notice Pajak pada semua unit yang dicentang.' });
                    return;
                }

                this.isSubmitting = true;

                const payloadItems = this.currentSuratJalans
                    .filter(s => this.isChecked(s.id))
                    .map(s => ({
                        id: s.id,
                        notice_pajak: Number(s.input_notice) || 0
                    }));

                const payload = {
                    tanggal: this.form.tanggal,
                    items: payloadItems,
                    tambahans: this.form.tambahans,
                    total_pajak: this.totalPajak,
                    total_adm: this.totalAdm,
                    total_tambahan: this.totalTambahan,
                    grand_total: this.grandTotal,
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                };

                fetch(`/pengajuan-stnk/${this.form.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json().then(data => {
                    if (!res.ok) throw data;
                    return data;
                }))
                .then(data => {
                    Swal.fire({
                        icon: 'success', title: 'Berhasil!', text: data.message,
                        timer: 1500, showConfirmButton: false
                    }).then(() => window.location.reload());
                })
                .catch(err => {
                    this.isSubmitting = false;
                    let msg = 'Terjadi kesalahan.';
                    if(err.errors) msg = Object.values(err.errors)[0][0];
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                });
            }
        }
    }

    function confirmDeleteAjax(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Data akan dihapus permanen dan dokumen dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Kembalikan Data',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengajuan-stnk/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success', title: 'Dibatalkan!', text: data.message,
                        timer: 1500, showConfirmButton: false
                    }).then(() => window.location.reload());
                });
            }
        });
    }
</script>
@endsection
