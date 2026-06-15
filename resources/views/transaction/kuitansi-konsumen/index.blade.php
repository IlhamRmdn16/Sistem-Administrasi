@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
            <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
            Kuitansi Konsumen (TTK)
        </h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex justify-between items-center font-bold">
            <span>{{ session('success') }}</span>
            @if(session('print_id'))
                <a href="{{ route('kuitansi-konsumen.print', session('print_id')) }}" target="_blank" class="bg-green-700 text-white px-4 py-1.5 rounded text-sm hover:bg-green-800">Print Sekarang</a>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="mb-5 relative">
                <label class="block text-sm font-bold text-gray-700 mb-2">Cari Konsumen (No {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }} / Nama)</label>
                <input type="text" id="searchInput" class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-sm" placeholder="Ketik nama atau No {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}..." autocomplete="off">
                <div id="searchDropdown" class="absolute w-full bg-white border border-gray-200 shadow-lg rounded-lg mt-1 hidden z-50 max-h-60 overflow-y-auto">
                </div>
            </div>

            <form action="{{ route('kuitansi-konsumen.store') }}" method="POST" id="ttkForm" class="hidden">
                @csrf
                <input type="hidden" name="spk_id" id="form_spk_id">

                <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg mb-5 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-blue-600 font-bold uppercase mb-1">Status Tagihan</div>
                        <div id="statusBadge" class="text-lg font-black text-gray-800"></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-blue-600 font-bold uppercase mb-1">Total Tagihan (Nett)</div>
                        <div id="tagihanNett" class="text-lg font-black text-blue-700">Rp 0</div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Kuitansi</label>
                        <input type="text" value="(Dibuat Otomatis saat Disimpan)" readonly class="w-full bg-gray-50 border border-gray-200 rounded p-2 text-sm text-gray-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bayar Kontan (Rp)</label>
                            <input type="number" name="bayar_kontan" id="inputKontan" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red font-bold" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bayar Transfer (Rp)</label>
                            <input type="number" name="bayar_transfer" id="inputTransfer" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red font-bold" placeholder="0">
                        </div>
                    </div>

                    <div id="rekeningBox" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Rekening Tujuan</label>
                        <select name="rekening_id" class="w-full border border-gray-300 rounded p-2 text-sm outline-none focus:border-honda-red">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($rekenings as $rek)
                                <option value="{{ $rek->id }}">{{ $rek->nama_rekening }} - {{ $rek->nomor_rekening }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Sisa Tagihan / Kurang:</span>
                            <span id="textSisa" class="text-sm font-black text-red-600">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 uppercase">Kelebihan (Refund):</span>
                            <span id="textRefund" class="text-sm font-black text-green-600">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="w-full bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-gray-900 shadow-md uppercase tracking-wider text-sm">Simpan Kuitansi</button>
                </div>
            </form>

            <div id="emptyState" class="py-10 text-center text-gray-400 italic">
                Cari dan pilih konsumen terlebih dahulu untuk membuka form pembayaran.
            </div>
        </div>

        <div class="lg:col-span-7 space-y-6 hidden" id="detailPanel">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-black text-gray-800 uppercase border-b border-gray-100 pb-3 mb-4 flex justify-between">
                    Informasi {{ Auth::user()->hasRole('Admin GP') ? 'GPK' : 'SPK' }}
                    <span id="detNoSpk" class="text-honda-red"></span>
                </h3>

                <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-xs">
                    <div><span class="text-gray-500 block">Nama Pemohon:</span> <b id="detPemohon" class="text-gray-800 uppercase"></b></div>
                    <div><span class="text-gray-500 block">Nama STNK:</span> <b id="detStnk" class="text-gray-800 uppercase"></b></div>
                    <div class="col-span-2"><span class="text-gray-500 block">Alamat Lengkap:</span> <b id="detAlamat" class="text-gray-800 uppercase"></b></div>
                    <div><span class="text-gray-500 block">No. Telepon:</span> <b id="detTelp" class="text-gray-800"></b></div>
                    <div><span class="text-gray-500 block">Nama Sales:</span> <b id="detSales" class="text-gray-800 uppercase"></b></div>
                    <div class="col-span-2 border-t border-gray-100 my-1"></div>
                    <div><span class="text-gray-500 block">Tipe Motor:</span> <b id="detMotor" class="text-gray-800 uppercase"></b></div>
                    <div><span class="text-gray-500 block">Tahun:</span> <b id="detTahun" class="text-gray-800"></b></div>
                    <div><span class="text-gray-500 block">Harga OTR:</span> <b id="detOtr" class="text-gray-800"></b></div>
                    <div><span class="text-gray-500 block">Uang Muka / Tanda Jadi:</span> <b id="detDpTj" class="text-gray-800"></b></div>
                    <div><span class="text-gray-500 block">Leasing (Tenor):</span> <b id="detLeasing" class="text-gray-800 uppercase"></b></div>
                    <div><span class="text-gray-500 block">No. Surat Jalan:</span> <b id="detSjk" class="text-gray-800 uppercase"></b></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-gray-100 font-black text-gray-800 uppercase text-sm">Riwayat Cetak Kuitansi</div>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">No. Kuitansi</th>
                            <th class="p-3">Total Bayar</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable" class="divide-y divide-gray-100 text-xs">
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchDropdown = document.getElementById('searchDropdown');
        const ttkForm = document.getElementById('ttkForm');
        const emptyState = document.getElementById('emptyState');
        const detailPanel = document.getElementById('detailPanel');

        let currentData = null;

        searchInput.addEventListener('input', function() {
            let q = this.value;
            if(q.length < 2) {
                searchDropdown.classList.add('hidden');
                return;
            }

            fetch(`{{ route('kuitansi-konsumen.search-api') }}?q=${q}`)
                .then(res => res.json())
                .then(data => {
                    searchDropdown.innerHTML = '';
                    if(data.length === 0) {
                        searchDropdown.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Tidak ditemukan</div>';
                    } else {
                        data.forEach(item => {
                            let div = document.createElement('div');
                            div.className = "p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100";
                            div.innerHTML = `<div class="font-bold text-sm text-gray-800 uppercase">${item.nama_pemohon} - ${item.no_spk}</div><div class="text-xs text-gray-500">${item.motor}</div>`;
                            div.onclick = () => selectSpk(item);
                            searchDropdown.appendChild(div);
                        });
                    }
                    searchDropdown.classList.remove('hidden');
                });
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });

        function formatRp(num) {
            return 'Rp ' + parseInt(num).toLocaleString('id-ID');
        }

        function selectSpk(item) {
            currentData = item;
            searchInput.value = item.nama_pemohon + ' - ' + item.no_spk;
            searchDropdown.classList.add('hidden');

            emptyState.classList.add('hidden');
            ttkForm.classList.remove('hidden');
            detailPanel.classList.remove('hidden');

            document.getElementById('form_spk_id').value = item.id;
            document.getElementById('inputKontan').value = '';
            document.getElementById('inputTransfer').value = '';
            document.getElementById('rekeningBox').classList.add('hidden');

            document.getElementById('tagihanNett').innerText = formatRp(item.target_tagihan);
            updateStatusLabel(item.sisa, item.is_lunas);
            calculateSisa();

            document.getElementById('detNoSpk').innerText = item.no_spk;
            document.getElementById('detPemohon').innerText = item.nama_pemohon;
            document.getElementById('detStnk').innerText = item.nama_stnk;
            document.getElementById('detAlamat').innerText = item.alamat;
            document.getElementById('detTelp').innerText = item.telepon;
            document.getElementById('detSales').innerText = item.sales;
            document.getElementById('detMotor').innerText = item.motor;
            document.getElementById('detTahun').innerText = item.tahun;
            document.getElementById('detOtr').innerText = formatRp(item.harga_otr);
            document.getElementById('detDpTj').innerText = formatRp(item.uang_muka) + ' / ' + formatRp(item.tanda_jadi);
            document.getElementById('detLeasing').innerText = item.leasing !== '-' ? `${item.leasing} (${item.tenor} Bln)` : 'CASH / TUNAI';
            document.getElementById('detSjk').innerText = item.sjk;

            const histTbody = document.getElementById('historyTable');
            histTbody.innerHTML = '';
            if(item.history.length === 0) {
                histTbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-500 italic">Belum ada riwayat kuitansi.</td></tr>';
            } else {
                item.history.forEach(h => {
                    histTbody.innerHTML += `
                        <tr class="hover:bg-blue-50/50">
                            <td class="p-3 font-bold">${h.tanggal}</td>
                            <td class="p-3">${h.no_kuitansi}</td>
                            <td class="p-3 font-bold text-gray-800">${formatRp(h.total)}</td>
                            <td class="p-3 text-center">
                                <a href="${h.url_print}" target="_blank" class="text-blue-600 font-bold hover:underline">Print</a>
                            </td>
                        </tr>
                    `;
                });
            }
        }

        function updateStatusLabel(sisaAsli, lunas) {
            const badge = document.getElementById('statusBadge');
            const btn = document.getElementById('btnSubmit');
            if(lunas) {
                badge.innerText = "LUNAS";
                badge.className = "text-xl font-black text-green-600";
                btn.disabled = true;
                btn.className = "w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-lg uppercase tracking-wider text-sm cursor-not-allowed";
                btn.innerText = "DOKUMEN SUDAH LUNAS";
            } else {
                badge.innerText = "BELUM LUNAS (Sisa: " + formatRp(sisaAsli) + ")";
                badge.className = "text-sm font-black text-red-600 mt-1";
                btn.disabled = false;
                btn.className = "w-full bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-gray-900 shadow-md uppercase tracking-wider text-sm";
                btn.innerText = "Simpan Kuitansi";
            }
        }

        function calculateSisa() {
            if(!currentData) return;

            let valKontan = parseInt(document.getElementById('inputKontan').value) || 0;
            let valTransfer = parseInt(document.getElementById('inputTransfer').value) || 0;

            const rekBox = document.getElementById('rekeningBox');
            if(valTransfer > 0) rekBox.classList.remove('hidden');
            else rekBox.classList.add('hidden');

            let sisaAwal = currentData.sisa;
            let totalBayarInput = valKontan + valTransfer;

            let currentSisa = sisaAwal - totalBayarInput;

            let textSisa = document.getElementById('textSisa');
            let textRefund = document.getElementById('textRefund');

            if(currentSisa > 0) {
                textSisa.innerText = formatRp(currentSisa);
                textRefund.innerText = formatRp(0);
            } else {
                textSisa.innerText = formatRp(0);
                textRefund.innerText = formatRp(Math.abs(currentSisa));
            }
        }

        document.getElementById('inputKontan').addEventListener('input', calculateSisa);
        document.getElementById('inputTransfer').addEventListener('input', calculateSisa);
    });
</script>
@endsection
