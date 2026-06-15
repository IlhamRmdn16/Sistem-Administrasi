@extends('layouts.app')

@section('content')
<div x-data="suratJalanManager()" @keydown.escape.window="isCreateOpen = false; isEditOpen = false">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                {{ Auth::user()->hasRole('Admin GP') ? 'Surat Jalan GP (SJG)' : 'Surat Jalan Konsumen (SJK)' }}
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola dan cetak dokumen pengiriman unit kendaraan kepada konsumen.</p>
        </div>

        <button @click="isCreateOpen = true" class="bg-honda-red text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:bg-red-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat {{ Auth::user()->hasRole('Admin GP') ? 'SJG' : 'SJK' }}
        </button>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form action="{{ route('suratjalan.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-honda-red">
            </div>

            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Bukti, {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}, STCK..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 outline-none focus:border-honda-red text-sm">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-sm outline-none bg-white focus:border-honda-red">
                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            <a href="{{ route('suratjalan.index') }}" class="text-center bg-gray-100 text-gray-600 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors w-full sm:w-auto">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold">No. Bukti / Tgl</th>
                        <th class="py-4 px-6 font-semibold">{{ Auth::user()->hasRole('Admin GP') ? 'No. GPK / Pemohon' : 'No. SPK / Pemohon' }}</th>
                        <th class="py-4 px-6 font-semibold">Tipe & Kunci</th>
                        <th class="py-4 px-6 font-semibold">PDI Man & STCK</th>
                        <th class="py-4 px-6 text-center w-36 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($suratJalans as $sj)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800">{{ $sj->no_bukti }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($sj->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800 uppercase">{{ $sj->spk->nama_pemohon ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $sj->spk->no_spk ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-700">
                                <div class="font-semibold truncate max-w-[150px]">{{ $sj->motorUnit->type->nama_type ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">Kunci: <span class="font-bold text-gray-800">{{ $sj->motorUnit->no_kunci ?? '-' }}</span></div>
                                <div class="text-[9px] inline-block mt-1 px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-600 font-bold uppercase">
                                    Lokasi: {{ $sj->motorUnit->posisi_stok ?? '-' }}
                                    {{ ($sj->motorUnit->posisi_stok === 'POP' && $sj->motorUnit->lokasiPop) ? '('.$sj->motorUnit->lokasiPop->nama_sales.')' : '' }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-semibold text-gray-700 uppercase">{{ $sj->pdiMan->nama_pdi_man ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">STCK: <span class="font-bold text-gray-800">{{ $sj->no_stck ?? '-' }}</span></div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('suratjalan.print', $sj->id) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Cetak Surat Jalan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <button @click="openEditModal({{ $sj }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="confirmDeleteAjax({{ $sj->id }}, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">Data Surat Jalan tidak ditemukan.</td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $suratJalans->links() }}
        </div>
    </div>

    <div x-show="isCreateOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isCreateOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isCreateOpen = false"></div>
            <div x-show="isCreateOpen" x-transition class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-4xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Buat {{ Auth::user()->hasRole('Admin GP') ? 'SJG Baru' : 'SJK Baru' }}</h3>
                    <button type="button" @click="isCreateOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form @submit.prevent="submitCreate" class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Bukti (Otomatis)</label>
                            <input type="text" name="no_bukti" value="{{ $autoNoBukti }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Dokumen</label>
                            <input type="date" name="tanggal" x-model="cTanggal" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="relative z-40">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Data {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }} <span class="text-red-500">*</span></label>
                            <div @click.away="openSpkDropdown = false">
                                <div @click="openSpkDropdown = !openSpkDropdown" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-honda-red">
                                    <span x-text="selectedSpkName || 'Pilih / Cari {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}...'"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div x-show="openSpkDropdown" class="absolute w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                                    <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                        <input type="text" x-model="searchSpk" placeholder="Ketik nama atau No Dokumen..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                    </div>
                                    <template x-for="s in availableSpks.filter(x => x.nama_pemohon.toLowerCase().includes(searchSpk.toLowerCase()) || x.no_spk.toLowerCase().includes(searchSpk.toLowerCase()))" :key="s.id">
                                        <div @click="selectSpk(s)" class="p-3 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50">
                                            <div class="font-bold text-gray-800" x-text="s.nama_pemohon + ' - ' + s.no_spk"></div>
                                            <div class="text-[10px] text-gray-500 mt-0.5" x-text="'Motor: ' + (s.motor_unit ? s.motor_unit.type.nama_type : '-')"></div>
                                            <div class="text-[9px] inline-block mt-1 px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-600 font-bold uppercase"
                                                 x-text="'Lokasi: ' + (s.motor_unit ? s.motor_unit.posisi_stok : '-') + (s.motor_unit && s.motor_unit.posisi_stok === 'POP' && s.motor_unit.lokasi_pop ? ' ('+s.motor_unit.lokasi_pop.nama_sales+')' : '')"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="spk_id" :value="cSpkId" required>
                        </div>

                        <div class="relative z-30">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PDI Man <span class="text-red-500">*</span></label>
                            <div @click.away="openPdiDropdown = false">
                                <div @click="openPdiDropdown = !openPdiDropdown" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-honda-red">
                                    <span x-text="selectedPdiName || 'Pilih PDI Man...'"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div x-show="openPdiDropdown" class="absolute w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                        <input type="text" x-model="searchPdi" placeholder="Cari nama PDI Man..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-honda-red">
                                    </div>
                                    <template x-for="p in pdiMans.filter(x => x.nama_pdi_man.toLowerCase().includes(searchPdi.toLowerCase()))" :key="p.id">
                                        <div @click="selectPdi(p)" class="p-2.5 hover:bg-red-50 cursor-pointer text-sm border-b border-gray-50" x-text="p.nama_pdi_man"></div>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="pdi_man_id" :value="cPdiId" required>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-5 relative z-0">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Nama Pemohon</label>
                            <input type="text" :value="cNamaPemohon" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">No. {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}</label>
                            <input type="text" :value="cNoSpk" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tipe Motor</label>
                            <input type="text" :value="cTipeMotor" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Warna Motor</label>
                            <input type="text" :value="cWarnaMotor" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-honda-red mb-1">No. Kunci Unit Tersedia</label>
                            <input type="text" :value="cNoKunci" readonly class="w-full bg-red-50 border border-red-200 rounded-lg p-2.5 font-mono font-bold text-honda-red cursor-not-allowed">
                            <input type="hidden" name="motor_unit_id" :value="cUnitId" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-honda-red mb-1">No. Accu</label>
                            <input type="text" :value="cNoAccu" readonly class="w-full bg-red-50 border border-red-200 rounded-lg p-2.5 font-mono font-bold text-honda-red cursor-not-allowed">
                        </div>

                        <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. STCK</label>
                                <input type="text" name="no_stck" x-model="cNoStck" placeholder="Masukkan STCK" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. Registrasi</label>
                                <input type="text" name="no_registrasi" x-model="cNoRegistrasi" placeholder="Masukkan Registrasi" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Berlaku s/d</label>
                                <input type="date" name="berlaku_sd" x-model="cBerlakuSd" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-honda-red">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 mt-2 border-t border-gray-200">
                        <button type="button" @click="isCreateOpen = false" class="px-6 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50" :disabled="isSubmitting">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-honda-red text-white rounded-lg font-bold shadow hover:bg-red-700 flex items-center gap-2" :disabled="isSubmitting">
                            <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Surat Jalan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="isEditOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isEditOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isEditOpen = false"></div>
            <div x-show="isEditOpen" x-transition class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-4xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Surat Jalan</h3>
                    <button type="button" @click="isEditOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form @submit.prevent="submitEdit" class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Bukti</label>
                            <input type="text" name="no_bukti" x-model="eNoBukti" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 font-bold text-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Dokumen</label>
                            <input type="date" name="tanggal" x-model="eTanggal" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div class="relative z-30">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PDI Man <span class="text-red-500">*</span></label>
                        <div @click.away="eOpenPdiDropdown = false">
                            <div @click="eOpenPdiDropdown = !eOpenPdiDropdown" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white cursor-pointer flex justify-between items-center focus:border-blue-500">
                                <span x-text="eSelectedPdiName || 'Pilih PDI Man...'"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div x-show="eOpenPdiDropdown" class="absolute w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <div class="sticky top-0 bg-gray-50 p-2 border-b">
                                    <input type="text" x-model="eSearchPdi" placeholder="Cari nama PDI Man..." class="w-full border border-gray-300 rounded p-1.5 text-sm outline-none focus:border-blue-500">
                                </div>
                                <template x-for="p in pdiMans.filter(x => x.nama_pdi_man.toLowerCase().includes(eSearchPdi.toLowerCase()))" :key="p.id">
                                    <div @click="selectEditPdi(p)" class="p-2.5 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-50" x-text="p.nama_pdi_man"></div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="pdi_man_id" :value="ePdiId" required>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-5 relative z-0">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Nama Pemohon</label>
                            <input type="text" :value="eNamaPemohon" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">No. {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}</label>
                            <input type="text" :value="eNoSpk" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tipe Motor</label>
                            <input type="text" :value="eTipeMotor" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Warna Motor</label>
                            <input type="text" :value="eWarnaMotor" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">No. Kunci</label>
                            <input type="text" :value="eNoKunci" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">No. Accu</label>
                            <input type="text" :value="eNoAccu" readonly class="w-full bg-white border border-gray-200 rounded-lg p-2.5 font-bold text-gray-600 cursor-not-allowed">
                        </div>

                        <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. STCK</label>
                                <input type="text" name="no_stck" x-model="eNoStck" placeholder="Masukkan STCK" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. Registrasi</label>
                                <input type="text" name="no_registrasi" x-model="eNoRegistrasi" placeholder="Masukkan Registrasi" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Berlaku s/d</label>
                                <input type="date" name="berlaku_sd" x-model="eBerlakuSd" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="spk_id" :value="eSpkId">
                    <input type="hidden" name="motor_unit_id" :value="eUnitId">

                    <div class="flex justify-end gap-3 pt-4 mt-2 border-t border-gray-200">
                        <button type="button" @click="isEditOpen = false" class="px-6 py-2.5 border border-gray-300 rounded-lg font-bold text-gray-700 hover:bg-gray-50" :disabled="isEditing">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700 flex items-center gap-2" :disabled="isEditing">
                            <svg x-show="!isEditing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <svg x-show="isEditing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isEditing ? 'Memperbarui...' : 'Perbarui Surat Jalan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function suratJalanManager() {
        return {
            availableSpks: @json($availableSpks),
            pdiMans: @json($pdiMans),

            isCreateOpen: false,
            isSubmitting: false,
            cTanggal: new Date().toISOString().split('T')[0],

            openSpkDropdown: false,
            searchSpk: '',
            selectedSpkName: '',
            cSpkId: '',
            cNamaPemohon: '',
            cNoSpk: '',
            cNamaStnk: '',
            cTipeMotor: '',
            cWarnaMotor: '',
            cNoKunci: '',
            cNoAccu: '',
            cUnitId: '',
            cNoStck: '',
            cNoRegistrasi: '',
            cBerlakuSd: '',

            openPdiDropdown: false,
            searchPdi: '',
            selectedPdiName: '',
            cPdiId: '',

            isEditOpen: false,
            isEditing: false,
            eId: '',
            eNoBukti: '',
            eTanggal: '',
            eNamaPemohon: '',
            eNoSpk: '',
            eNamaStnk: '',
            eTipeMotor: '',
            eWarnaMotor: '',
            eNoKunci: '',
            eNoAccu: '',
            eSpkId: '',
            eUnitId: '',
            eNoStck: '',
            eNoRegistrasi: '',
            eBerlakuSd: '',

            eOpenPdiDropdown: false,
            eSearchPdi: '',
            eSelectedPdiName: '',
            ePdiId: '',

            selectSpk(s) {
                this.cSpkId = s.id;
                this.selectedSpkName = s.nama_pemohon + ' - ' + s.no_spk;
                this.cNamaPemohon = s.nama_pemohon;
                this.cNoSpk = s.no_spk;

                if (s.motor_unit) {
                    this.cTipeMotor = s.motor_unit.type.nama_type;
                    this.cWarnaMotor = s.motor_unit.color.warna;
                    this.cNoKunci = s.motor_unit.no_kunci || '-';
                    this.cNoAccu = s.motor_unit.no_accu || '-';
                    this.cUnitId = s.motor_unit.id;
                } else {
                    this.cTipeMotor = '-';
                    this.cWarnaMotor = '-';
                    this.cNoKunci = '-';
                    this.cNoAccu = '-';
                    this.cUnitId = '';
                }
                this.openSpkDropdown = false;
            },

            selectPdi(p) {
                this.cPdiId = p.id;
                this.selectedPdiName = p.nama_pdi_man;
                this.openPdiDropdown = false;
            },

            submitCreate(event) {
                this.isSubmitting = true;
                let formData = new FormData(event.target);

                fetch('{{ route("suratjalan.store") }}', {
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
                        this.isCreateOpen = false;
                        this.isSubmitting = false;
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
            },

            selectEditPdi(p) {
                this.ePdiId = p.id;
                this.eSelectedPdiName = p.nama_pdi_man;
                this.eOpenPdiDropdown = false;
            },

            openEditModal(sj) {
                this.eId = sj.id;
                this.eNoBukti = sj.no_bukti;
                this.eTanggal = sj.tanggal;
                this.eSpkId = sj.spk_id;
                this.eUnitId = sj.motor_unit_id;

                this.ePdiId = sj.pdi_man_id;
                if(sj.pdi_man) {
                    this.eSelectedPdiName = sj.pdi_man.nama_pdi_man;
                }

                this.eNamaPemohon = sj.spk.nama_pemohon;
                this.eNoSpk = sj.spk.no_spk;

                if (sj.motor_unit) {
                    this.eTipeMotor = sj.motor_unit.type.nama_type;
                    this.eWarnaMotor = sj.motor_unit.color.warna;
                    this.eNoKunci = sj.motor_unit.no_kunci || '-';
                    this.eNoAccu = sj.motor_unit.no_accu || '-';
                } else {
                    this.eTipeMotor = '-';
                    this.eWarnaMotor = '-';
                    this.eNoKunci = '-';
                    this.eNoAccu = '-';
                }

                this.eNoStck = sj.no_stck || '';
                this.eNoRegistrasi = sj.no_registrasi || '';
                this.eBerlakuSd = sj.berlaku_sd || '';

                this.isEditOpen = true;
            },

            submitEdit(event) {
                this.isEditing = true;
                let formData = new FormData(event.target);

                fetch('/transaction/suratjalan/' + this.eId, {
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
                        this.isEditOpen = false;
                        this.isEditing = false;
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
                    this.isEditing = false;
                    let errorMsg = 'Terjadi kesalahan sistem.';
                    if(error.errors) {
                        errorMsg = Object.values(error.errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memperbarui',
                        text: errorMsg
                    });
                });
            }
        }
    }

    function confirmDeleteAjax(id, button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Dokumen Surat Jalan ini akan dihapus permanen! Status motor akan kembali menjadi Tersedia.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus & Kembalikan Stok!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/transaction/suratjalan/${id}`, {
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
                            timer: 3000,
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
