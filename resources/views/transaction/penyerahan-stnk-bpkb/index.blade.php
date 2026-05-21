@extends('layouts.app')

@section('content')
<div x-data="penyerahanManager()" @keydown.escape.window="isModalOpen = false; isDetailOpen = false; stopCamera()">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Penyerahan STNK / BPKB
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Serah terima fisik dokumen kendaraan kepada konsumen.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex flex-col lg:flex-row justify-between items-center gap-4">
            <form action="{{ route('penyerahan-stnk-bpkb.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama / No SJK..." class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-4 text-sm outline-none focus:border-honda-red">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-2 px-3 text-sm outline-none bg-white focus:border-honda-red w-full">
                        <option value="">Semua Status Serah</option>
                        <option value="belum" {{ $status == 'belum' ? 'selected' : '' }}>Belum Diserahkan</option>
                        <option value="stnk" {{ $status == 'stnk' ? 'selected' : '' }}>STNK Diserahkan (BPKB Belum)</option>
                        <option value="lengkap" {{ $status == 'lengkap' ? 'selected' : '' }}>Selesai / Lengkap</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-gray-900 transition-colors w-full sm:w-auto">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-semibold w-12 text-center">No</th>
                        <th class="py-4 px-6 font-semibold">Data SPK / STNK</th>
                        <th class="py-4 px-6 font-semibold">Tipe & Kunci</th>
                        <th class="py-4 px-6 font-semibold text-center">Status STNK</th>
                        <th class="py-4 px-6 font-semibold text-center">Status BPKB</th>
                        <th class="py-4 px-6 text-center w-40 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dokumens as $index => $doc)
                        @php
                            $stnkReady = $doc->samsat && !empty($doc->samsat->no_stnk);
                            $bpkbReady = $doc->samsat && !empty($doc->samsat->no_bpkb);

                            $stnkDiserahkan = $doc->penyerahanStnkBpkb && $doc->penyerahanStnkBpkb->tgl_serah_stnk;
                            $bpkbDiserahkan = $doc->penyerahanStnkBpkb && $doc->penyerahanStnkBpkb->tgl_serah_bpkb;

                            $isKredit = $doc->spk->leasing_id ? true : false;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-sm text-center text-gray-500 font-bold">{{ $dokumens->firstItem() + $index }}</td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-bold text-gray-800 uppercase">{{ $doc->spk->nama_stnk ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $doc->no_bukti }}</div>
                                @if($isKredit)
                                    <div class="text-[10px] font-bold mt-1 px-1.5 py-0.5 inline-block rounded bg-purple-100 text-purple-700">KREDIT</div>
                                @else
                                    <div class="text-[10px] font-bold mt-1 px-1.5 py-0.5 inline-block rounded bg-emerald-100 text-emerald-700">CASH</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="font-semibold text-gray-700 truncate max-w-[150px]">{{ $doc->spk->motorType->nama_type ?? '-' }}</div>
                                <div class="text-xs font-mono font-bold text-gray-500 mt-0.5">Kunci: {{ $doc->motorUnit->no_kunci ?? '-' }}</div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                @if($stnkDiserahkan)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-700">Diserahkan</span><br>
                                    <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($doc->penyerahanStnkBpkb->tgl_serah_stnk)->format('d/m/Y') }}</span>
                                @elseif($stnkReady)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-amber-100 text-amber-700">Tersedia</span>
                                @else
                                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-gray-100 text-gray-500">Belum Jadi</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-center">
                                @if($isKredit)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-purple-100 text-purple-700">Oleh Leasing</span>
                                @else
                                    @if($bpkbDiserahkan)
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-700">Diserahkan</span><br>
                                        <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($doc->penyerahanStnkBpkb->tgl_serah_bpkb)->format('d/m/Y') }}</span>
                                    @elseif($bpkbReady)
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-amber-100 text-amber-700">Tersedia</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-gray-100 text-gray-500">Belum Jadi</span>
                                    @endif
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button @click="openDetail({{ $doc }}, {{ $isKredit ? 'true' : 'false' }})" class="text-gray-500 hover:text-gray-800" title="Lihat Detail Penyerahan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    @if($stnkDiserahkan || $bpkbDiserahkan)
                                    <a href="{{ route('penyerahan-stnk-bpkb.print', $doc->id) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700" title="Cetak Tanda Terima">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    @endif

                                    <button @click="openModal({{ $doc }}, {{ $stnkReady ? 'true' : 'false' }}, {{ $bpkbReady ? 'true' : 'false' }}, {{ $isKredit ? 'true' : 'false' }})" class="text-blue-500 hover:text-blue-700" title="Proses Serah Terima">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                    @endempty
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $dokumens->links() }}</div>
    </div>

    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-40 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isModalOpen = false"></div>
            <div x-show="isModalOpen" class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-5xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Form Serah Terima <span x-show="mIsKredit" class="text-sm bg-purple-100 text-purple-700 px-2 py-0.5 ml-2 rounded">(KREDIT)</span></h3>
                        <p class="text-xs text-gray-500 uppercase" x-text="mNamaStnk + ' - ' + mMotor"></p>
                    </div>
                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form @submit.prevent="submitData" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="border rounded-xl p-4" :class="mStnkReady ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200 bg-gray-50 opacity-60'">
                            <h4 class="font-bold text-sm mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="mStnkReady ? 'bg-blue-500' : 'bg-gray-400'"></span>
                                DATA PENYERAHAN STNK
                            </h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Tgl Serah STNK</label>
                                        <input type="date" name="tgl_serah_stnk" x-model="fTglStnk" :disabled="!mStnkReady" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-blue-500 disabled:bg-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Penerima</label>
                                        <input type="text" name="penerima_stnk" x-model="fPenerimaStnk" :disabled="!mStnkReady" class="w-full border border-gray-300 rounded p-2 text-sm uppercase outline-none focus:border-blue-500 disabled:bg-gray-100">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Hubungan</label>
                                    <select name="hubungan_stnk" x-model="fHubunganStnk" :disabled="!mStnkReady" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-blue-500 disabled:bg-gray-100">
                                        <option value="">Pilih Hubungan...</option>
                                        <option value="Pemilik">Pemilik Kendaraan</option>
                                        <option value="Suami/Istri">Suami / Istri</option>
                                        <option value="Anak">Anak</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                </div>

                                <div x-show="fHubunganStnk === 'Lainnya'">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Sebutkan Hubungan Lainnya</label>
                                    <input type="text" name="keterangan_stnk" x-model="fKetStnk" :disabled="!mStnkReady" placeholder="Ket. Lainnya..." class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Alamat Penerima</label>
                                    <textarea name="alamat_penerima_stnk" x-model="fAlamatStnk" :disabled="!mStnkReady" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-blue-500 disabled:bg-gray-100"></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1 flex items-center justify-between">
                                        <span>Ambil Foto Bukti STNK <span class="text-red-500">*</span></span>
                                        <span x-show="fFotoStnkExist && !fFotoStnkBase64" class="text-emerald-500 text-[10px]">Tersimpan di Server ✓</span>
                                    </label>

                                    <template x-if="fFotoStnkBase64">
                                        <div class="relative mb-2 w-full h-32 rounded-lg overflow-hidden border border-gray-300">
                                            <img :src="fFotoStnkBase64" class="w-full h-full object-cover">
                                            <button type="button" @click="fFotoStnkBase64 = null" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <button type="button" x-show="!fFotoStnkBase64" @click="startCamera('stnk')" :disabled="!mStnkReady" class="w-full py-2 border-2 border-dashed border-blue-300 rounded-lg text-blue-600 font-bold text-sm bg-blue-50 hover:bg-blue-100 transition-colors flex justify-center items-center gap-2 disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Buka Kamera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="!mIsKredit" class="border rounded-xl p-4" :class="mBpkbReady ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200 bg-gray-50 opacity-60'">
                            <h4 class="font-bold text-sm mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="mBpkbReady ? 'bg-amber-500' : 'bg-gray-400'"></span>
                                DATA PENYERAHAN BPKB
                            </h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Tgl Serah BPKB</label>
                                        <input type="date" name="tgl_serah_bpkb" x-model="fTglBpkb" :disabled="!mBpkbReady" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-amber-500 disabled:bg-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Penerima</label>
                                        <input type="text" name="penerima_bpkb" x-model="fPenerimaBpkb" :disabled="!mBpkbReady" class="w-full border border-gray-300 rounded p-2 text-sm uppercase outline-none focus:border-amber-500 disabled:bg-gray-100">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Hubungan</label>
                                    <select name="hubungan_bpkb" x-model="fHubunganBpkb" :disabled="!mBpkbReady" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-amber-500 disabled:bg-gray-100">
                                        <option value="">Pilih Hubungan...</option>
                                        <option value="Pemilik">Pemilik Kendaraan</option>
                                        <option value="Suami/Istri">Suami / Istri</option>
                                        <option value="Anak">Anak</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                </div>

                                <div x-show="fHubunganBpkb === 'Lainnya'">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Sebutkan Hubungan Lainnya</label>
                                    <input type="text" name="keterangan_bpkb" x-model="fKetBpkb" :disabled="!mBpkbReady" placeholder="Ket. Lainnya..." class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-amber-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Alamat Penerima</label>
                                    <textarea name="alamat_penerima_bpkb" x-model="fAlamatBpkb" :disabled="!mBpkbReady" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-amber-500 disabled:bg-gray-100"></textarea>
                                </div>

                                <div x-show="fHubunganBpkb && fHubunganBpkb !== 'Pemilik'">
                                    <label class="block text-xs font-bold text-gray-600 mb-1 flex items-center justify-between">
                                        <span>Ambil Foto Bukti Perwakilan <span class="text-red-500">*</span></span>
                                        <span x-show="fFotoBpkbExist && !fFotoBpkbBase64" class="text-emerald-500 text-[10px]">Tersimpan di Server ✓</span>
                                    </label>

                                    <template x-if="fFotoBpkbBase64">
                                        <div class="relative mb-2 w-full h-32 rounded-lg overflow-hidden border border-gray-300">
                                            <img :src="fFotoBpkbBase64" class="w-full h-full object-cover">
                                            <button type="button" @click="fFotoBpkbBase64 = null" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <button type="button" x-show="!fFotoBpkbBase64" @click="startCamera('bpkb')" :disabled="!mBpkbReady" class="w-full py-2 border-2 border-dashed border-amber-300 rounded-lg text-amber-600 font-bold text-sm bg-amber-50 hover:bg-amber-100 transition-colors flex justify-center items-center gap-2 disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Buka Kamera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="mIsKredit" class="border border-purple-200 bg-purple-50 rounded-xl p-6 flex flex-col items-center justify-center text-center">
                            <svg class="w-12 h-12 text-purple-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <h4 class="font-bold text-purple-800">Unit Pembayaran Kredit</h4>
                            <p class="text-xs text-purple-600 mt-2">Buku BPKB akan diserahkan oleh pihak Leasing kepada konsumen setelah pelunasan. Dealer tidak menerima/menyerahkan BPKB untuk unit ini.</p>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" @click="isModalOpen = false" class="px-6 py-2 border border-gray-300 rounded-lg font-bold text-gray-700">Batal</button>
                        <button type="submit" class="px-8 py-2 bg-gray-800 text-white rounded-lg font-bold shadow hover:bg-gray-900" :disabled="isSubmitting">
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="isDetailOpen" style="display: none;" class="fixed inset-0 z-40 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="isDetailOpen" class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="isDetailOpen = false"></div>
            <div x-show="isDetailOpen" class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-4xl overflow-hidden relative z-10">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-gray-900">Detail Penyerahan Dokumen</h3>
                    <button @click="isDetailOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                        <h4 class="font-bold text-blue-800 text-sm mb-3 border-b border-gray-200 pb-2">STNK</h4>
                        <template x-if="dStnkDate">
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-500 text-xs block">Tgl Serah</span><span class="font-bold text-gray-800" x-text="dStnkDate"></span></div>
                                <div><span class="text-gray-500 text-xs block">Penerima</span><span class="font-bold uppercase text-gray-800" x-text="dStnkPenerima"></span></div>
                                <div><span class="text-gray-500 text-xs block">Status Hubungan</span><span class="font-semibold text-gray-800" x-text="dStnkHubungan"></span></div>

                                <div class="mt-4 pt-3 border-t border-gray-200">
                                    <span class="text-gray-500 text-xs block mb-2">Foto Dokumentasi</span>
                                    <template x-if="dStnkFoto">
                                        <img :src="'/storage/' + dStnkFoto" class="w-full h-48 object-cover rounded-lg border border-gray-300 shadow-sm" alt="Foto STNK">
                                    </template>
                                    <template x-if="!dStnkFoto">
                                        <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-400 text-xs italic">Tidak ada foto</div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!dStnkDate">
                            <div class="text-center py-8 text-gray-400 italic text-sm">STNK belum diserahkan.</div>
                        </template>
                    </div>

                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                        <h4 class="font-bold text-amber-800 text-sm mb-3 border-b border-gray-200 pb-2">BPKB <span x-show="mIsKredit" class="text-xs bg-purple-100 text-purple-700 font-normal px-2 py-0.5 rounded ml-2">(KREDIT)</span></h4>
                        <template x-if="!mIsKredit && dBpkbDate">
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-500 text-xs block">Tgl Serah</span><span class="font-bold text-gray-800" x-text="dBpkbDate"></span></div>
                                <div><span class="text-gray-500 text-xs block">Penerima</span><span class="font-bold uppercase text-gray-800" x-text="dBpkbPenerima"></span></div>
                                <div><span class="text-gray-500 text-xs block">Status Hubungan</span><span class="font-semibold text-gray-800" x-text="dBpkbHubungan"></span></div>

                                <div class="mt-4 pt-3 border-t border-gray-200">
                                    <span class="text-gray-500 text-xs block mb-2">Foto Dokumentasi</span>
                                    <template x-if="dBpkbFoto">
                                        <img :src="'/storage/' + dBpkbFoto" class="w-full h-48 object-cover rounded-lg border border-gray-300 shadow-sm" alt="Foto BPKB">
                                    </template>
                                    <template x-if="!dBpkbFoto">
                                        <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-400 text-xs italic">Diambil Sendiri (Tanpa Foto)</div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!mIsKredit && !dBpkbDate">
                            <div class="text-center py-8 text-gray-400 italic text-sm">BPKB belum diserahkan.</div>
                        </template>
                        <template x-if="mIsKredit">
                            <div class="text-center py-12 px-6">
                                <svg class="w-8 h-8 text-purple-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <p class="text-gray-500 text-xs">BPKB dikelola oleh Leasing.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="isCameraOpen" style="display: none;" class="fixed inset-0 z-[60] bg-black flex flex-col">
        <div class="p-4 flex justify-between items-center text-white bg-gradient-to-b from-black/80 to-transparent absolute top-0 w-full z-10">
            <span class="font-bold uppercase tracking-wider text-sm" x-text="'Kamera: ' + cameraTarget"></span>
            <button @click="stopCamera()" class="p-2 bg-white/20 hover:bg-white/40 rounded-full backdrop-blur transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 relative bg-gray-900 flex items-center justify-center overflow-hidden">
            <video x-ref="videoElement" autoplay playsinline class="w-full h-full object-contain"></video>
            <canvas x-ref="canvasElement" class="hidden"></canvas>
        </div>

        <div class="h-32 bg-black flex items-center justify-center pb-6 z-20 relative">
            <button @click="takeSnapshot()" class="w-16 h-16 rounded-full bg-white border-4 border-gray-300 focus:outline-none hover:bg-gray-200 hover:scale-105 transition-all shadow-[0_0_15px_rgba(255,255,255,0.4)]"></button>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // FUNGSI KONVERSI: Mengubah Base64 hasil kamera menjadi File utuh agar Controller tidak error
    function dataURItoBlob(dataURI) {
        var byteString = atob(dataURI.split(',')[1]);
        var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        var ab = new ArrayBuffer(byteString.length);
        var dw = new DataView(ab);
        for(var i = 0; i < byteString.length; i++) { dw.setUint8(i, byteString.charCodeAt(i)); }
        return new Blob([ab], {type: mimeString});
    }

    function penyerahanManager() {
        return {
            isModalOpen: false, isDetailOpen: false, isSubmitting: false,
            mId: '', mNamaStnk: '', mMotor: '',
            mStnkReady: false, mBpkbReady: false, mIsKredit: false,

            // Kamera Logic
            isCameraOpen: false, cameraTarget: '', stream: null,
            fFotoStnkBase64: null, fFotoBpkbBase64: null,

            fTglStnk: '', fPenerimaStnk: '', fAlamatStnk: '',
            fHubunganStnk: '', fKetStnk: '', fFotoStnkExist: false,

            fTglBpkb: '', fPenerimaBpkb: '', fAlamatBpkb: '',
            fHubunganBpkb: '', fKetBpkb: '', fFotoBpkbExist: false,

            dStnkDate: '', dStnkPenerima: '', dStnkHubungan: '', dStnkFoto: '',
            dBpkbDate: '', dBpkbPenerima: '', dBpkbHubungan: '', dBpkbFoto: '',

            openModal(doc, stnkReady, bpkbReady, isKredit) {
                this.mId = doc.id;
                this.mNamaStnk = doc.spk ? doc.spk.nama_stnk : '';
                this.mMotor = doc.spk && doc.spk.motor_type ? doc.spk.motor_type.nama_type : '';
                this.mStnkReady = stnkReady;
                this.mBpkbReady = bpkbReady;
                this.mIsKredit = isKredit;
                this.fFotoStnkBase64 = null; // reset preview
                this.fFotoBpkbBase64 = null; // reset preview

                let p = doc.penyerahan_stnk_bpkb || {};

                this.fTglStnk = p.tgl_serah_stnk || '';
                this.fPenerimaStnk = p.penerima_stnk || '';
                this.fAlamatStnk = p.alamat_penerima_stnk || '';
                this.fHubunganStnk = p.hubungan_stnk || '';
                this.fKetStnk = p.keterangan_stnk || '';
                this.fFotoStnkExist = p.foto_serah_stnk ? true : false;

                this.fTglBpkb = p.tgl_serah_bpkb || '';
                this.fPenerimaBpkb = p.penerima_bpkb || '';
                this.fAlamatBpkb = p.alamat_penerima_bpkb || '';
                this.fHubunganBpkb = p.hubungan_bpkb || '';
                this.fKetBpkb = p.keterangan_bpkb || '';
                this.fFotoBpkbExist = p.foto_serah_bpkb ? true : false;

                this.isModalOpen = true;
            },

            openDetail(doc, isKredit) {
                this.mIsKredit = isKredit;
                let p = doc.penyerahan_stnk_bpkb || {};

                this.dStnkDate = p.tgl_serah_stnk || '';
                this.dStnkPenerima = p.penerima_stnk || '';
                this.dStnkFoto = p.foto_serah_stnk || '';
                this.dStnkHubungan = p.hubungan_stnk === 'Lainnya' ? `Lainnya (${p.keterangan_stnk})` : p.hubungan_stnk || '-';

                this.dBpkbDate = p.tgl_serah_bpkb || '';
                this.dBpkbPenerima = p.penerima_bpkb || '';
                this.dBpkbFoto = p.foto_serah_bpkb || '';
                this.dBpkbHubungan = p.hubungan_bpkb === 'Lainnya' ? `Lainnya (${p.keterangan_bpkb})` : p.hubungan_bpkb || '-';

                this.isDetailOpen = true;
            },

            // --- WEBCAM API METHODS ---
            startCamera(target) {
                this.cameraTarget = target;
                this.isCameraOpen = true;

                // Minta resolusi tinggi, browser/perangkat akan otomatis menyesuaikan rasionya
                navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1920 },
                        height: { ideal: 1080 }
                    }
                })
                .then(stream => {
                    this.stream = stream;
                    this.$refs.videoElement.srcObject = stream;
                })
                .catch(err => {
                    this.isCameraOpen = false;
                    Swal.fire('Kamera Gagal', 'Pastikan Anda memberikan izin kamera dan menggunakan HTTPS.', 'error');
                    console.error("Camera Error:", err);
                });
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
                this.isCameraOpen = false;
            },

            takeSnapshot() {
                const video = this.$refs.videoElement;
                const canvas = this.$refs.canvasElement;
                const ctx = canvas.getContext('2d');

                // Ukuran canvas mengikuti rasio ASLI dari kamera perangkat (HP/PC)
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Gambar full frame tanpa potongan
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Konversi gambar dengan kompresi 80% agar ringan diserver
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);

                if(this.cameraTarget === 'stnk') {
                    this.fFotoStnkBase64 = dataUrl;
                } else if(this.cameraTarget === 'bpkb') {
                    this.fFotoBpkbBase64 = dataUrl;
                }

                this.stopCamera();
            },

            submitData(e) {
                // Validasi STNK
                if(this.fTglStnk && !this.fFotoStnkExist && !this.fFotoStnkBase64) {
                    Swal.fire({ icon: 'warning', title: 'Oops', text: 'Foto bukti penyerahan STNK wajib diambil!' });
                    return;
                }

                // Validasi BPKB
                if(this.fTglBpkb && this.fHubunganBpkb && this.fHubunganBpkb !== 'Pemilik' && !this.fFotoBpkbExist && !this.fFotoBpkbBase64) {
                    Swal.fire({ icon: 'warning', title: 'Oops', text: 'Karena BPKB diambil oleh perwakilan, foto bukti wajib diambil!' });
                    return;
                }

                this.isSubmitting = true;
                let formData = new FormData(e.target);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');

                // Menyisipkan gambar Base64 menjadi bentuk File yang sah untuk Controller
                if(this.fFotoStnkBase64) {
                    formData.append('foto_stnk', dataURItoBlob(this.fFotoStnkBase64), 'foto_stnk.jpg');
                }
                if(this.fFotoBpkbBase64) {
                    formData.append('foto_bpkb', dataURItoBlob(this.fFotoBpkbBase64), 'foto_bpkb.jpg');
                }

                fetch('/penyerahan-stnk-bpkb/' + this.mId, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    this.isSubmitting = false;
                    if(data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, showConfirmButton: false, timer: 1500 })
                        .then(() => window.location.reload());
                    }
                })
                .catch(() => {
                    this.isSubmitting = false;
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menyimpan data.' });
                });
            }
        }
    }
</script>
@endsection
