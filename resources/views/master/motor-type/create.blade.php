@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-none border border-gray-200 shadow-sm">
    <h2 class="text-2xl font-bold mb-6 border-l-4 border-honda-red pl-3">Input Master Data Motor</h2>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('motor-type.store') }}" method="POST" x-data="motorForm()">
        @csrf

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Type (Katalog)</label>
                <input type="text" name="kode_type" placeholder="Contoh: 00288" required
                    class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Type</label>
                <input type="text" name="nama_type" placeholder="Contoh: Scoopy Fashion" required
                    class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga OTR (Rp)</label>
                <input type="number" name="otr" placeholder="Contoh: 21000000" required
                    class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-3 border-b pb-2">
                <h3 class="text-lg font-semibold text-gray-800">Varian Warna</h3>
                <button type="button" @click="addColor()" class="text-sm bg-honda-dark text-white px-3 py-1 rounded-none hover:bg-gray-700 transition">
                    + Tambah Warna
                </button>
            </div>

            <template x-for="(color, index) in colors" :key="index">
                <div class="flex gap-4 mb-3 items-start">
                    <div class="flex-1">
                        <input type="text" x-model="color.warna" @input="color.kode_warna = generateCode(color.warna)" :name="`colors[${index}][warna]`" placeholder="Ketik warna (Contoh: Black Current)" required
                            class="w-full border-gray-300 focus:border-honda-red focus:ring-0 rounded-none shadow-sm py-2 px-3 border outline-none transition-colors">
                    </div>
                    <div class="w-32">
                        <input type="text" x-model="color.kode_warna" :name="`colors[${index}][kode_warna]`" readonly placeholder="Kode" required
                            class="w-full bg-gray-100 border-gray-300 text-gray-500 rounded-none shadow-sm py-2 px-3 border outline-none text-center font-bold cursor-not-allowed">
                    </div>
                    <button type="button" @click="removeColor(index)" x-show="colors.length > 1" class="px-3 py-2 bg-red-100 text-red-600 hover:bg-red-200 border border-red-200 rounded-none transition">
                        X
                    </button>
                </div>
            </template>
        </div>

        <div class="flex justify-end pt-4 mt-8 border-t">
            <button type="submit" class="bg-honda-red text-white font-bold py-2 px-8 rounded-none shadow hover:bg-red-700 transition">
                Simpan Master Data
            </button>
        </div>
    </form>
</div>

<script>
    function motorForm() {
        return {
            colors: [{ warna: '', kode_warna: '' }],
            
            addColor() {
                this.colors.push({ warna: '', kode_warna: '' });
            },
            
            removeColor(index) {
                this.colors.splice(index, 1);
            },
            
            generateCode(str) {
                if (!str) return '';
                
                let words = str.trim().split(/\s+/);
                
                if (words.length === 1) {
                    let word = words[0];
                    if (word.length === 1) return word.toUpperCase();
                    return (word.charAt(0) + word.slice(-1)).toUpperCase();
                } else {
                    return words.map(w => w.charAt(0)).join('').toUpperCase();
                }
            }
        }
    }
</script>
@endsection