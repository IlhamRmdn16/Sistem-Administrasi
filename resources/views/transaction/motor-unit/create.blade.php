@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-none border border-gray-200 shadow-sm" x-data="unitForm()">
    <h2 class="text-2xl font-bold mb-6 border-l-4 border-honda-red pl-3">Registrasi Penerimaan Unit</h2>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('motor-unit.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. DO</label>
                <input type="text" name="no_do" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. SP</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="no_sp" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
                    <span class="text-gray-500 font-semibold bg-gray-100 px-3 py-2 border border-gray-300 rounded-none w-32 text-center" x-text="'/ ' + todayDate"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 border-t pt-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Motor</label>
                <select name="motor_type_id" x-model="selectedType" @change="updateTypeData()" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
                    <option value="">Pilih Tipe</option>
                    <template x-for="type in types" :key="type.id">
                        <option :value="type.id" x-text="type.nama_type"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Type</label>
                <input type="text" x-model="kodeType" readonly class="w-full bg-gray-100 border-gray-300 text-gray-500 rounded-none shadow-sm py-2 px-3 border outline-none font-bold cursor-not-allowed">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Warna</label>
                <select name="motor_color_id" x-model="selectedColor" @change="updateColorData()" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
                    <option value="">Pilih Warna</option>
                    <template x-for="color in availableColors" :key="color.id">
                        <option :value="color.id" x-text="color.warna + ' - ' + color.kode_warna"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Warna</label>
                <input type="text" x-model="kodeWarna" readonly class="w-full bg-gray-100 border-gray-300 text-gray-500 rounded-none shadow-sm py-2 px-3 border outline-none font-bold cursor-not-allowed">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 border-t pt-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Mesin</label>
                <input type="text" name="no_mesin" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Rangka</label>
                <input type="text" name="no_rangka" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Seri Kunci</label>
                <input type="text" name="no_seri_kunci" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Kunci</label>
                <input type="text" name="no_kunci" x-model="noKunci" @input="processKeyNumber()" required class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Pembuatan</label>
                <input type="number" name="tahun_pembuatan" x-model="tahunPembuatan" readonly class="w-full bg-gray-100 border-gray-300 text-gray-500 rounded-none shadow-sm py-2 px-3 border outline-none font-bold cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Accu</label>
                <input type="text" name="no_accu" x-model="noAccu" readonly class="w-full bg-gray-100 border-gray-300 text-gray-500 rounded-none shadow-sm py-2 px-3 border outline-none font-bold cursor-not-allowed">
            </div>
        </div>

        <div class="flex justify-end pt-4 mt-8 border-t">
            <button type="submit" class="bg-honda-red text-white font-bold py-2 px-8 rounded-none shadow hover:bg-red-700 transition">
                Simpan Data Registrasi
            </button>
        </div>
    </form>
</div>

<script>
    function unitForm() {
        return {
            types: @json($types),
            todayDate: new Date().toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}).replace(/\//g, '/'),
            selectedType: '',
            kodeType: '',
            availableColors: [],
            selectedColor: '',
            kodeWarna: '',
            noKunci: '',
            tahunPembuatan: '',
            noAccu: '',

            updateTypeData() {
                let type = this.types.find(t => t.id == this.selectedType);
                if (type) {
                    this.kodeType = type.kode_type;
                    this.availableColors = type.colors;
                } else {
                    this.kodeType = '';
                    this.availableColors = [];
                }
                this.selectedColor = '';
                this.kodeWarna = '';
            },

            updateColorData() {
                let color = this.availableColors.find(c => c.id == this.selectedColor);
                if (color) {
                    this.kodeWarna = color.kode_warna;
                } else {
                    this.kodeWarna = '';
                }
            },

            processKeyNumber() {
                let key = this.noKunci.trim();
                
                if (key.length >= 4) {
                    this.tahunPembuatan = key.substring(0, 4);
                } else {
                    this.tahunPembuatan = '';
                }

                if (key.length >= 5) {
                    let prefix = key.substring(0, 4);
                    let letter = key.charAt(4).toUpperCase();
                    let suffix = key.substring(5);

                    let monthMap = {
                        'A': '01', 'B': '02', 'C': '03', 'D': '04', 'E': '05', 'F': '06',
                        'G': '07', 'H': '08', 'I': '09', 'J': '10', 'K': '11', 'L': '12'
                    };

                    let monthCode = monthMap[letter] || letter;
                    this.noAccu = prefix + monthCode + suffix;
                } else {
                    this.noAccu = '';
                }
            }
        }
    }
</script>
@endsection