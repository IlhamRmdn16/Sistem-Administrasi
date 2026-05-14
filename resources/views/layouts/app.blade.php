<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Administrasi Dealer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-honda-light text-honda-dark font-sans antialiased">
    
    <div class="bg-honda-dark text-white text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span class="tracking-wider">SISTEM ADMINISTRASI - AHASS SURYA WIJAYA</span>
            <span>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <nav class="bg-white border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-2 mr-8">
                        <div class="w-8 h-8 bg-honda-red rounded-none"></div>
                        <span class="font-bold text-xl tracking-tight text-honda-dark">DealerSys</span>
                    </div>

                    <div class="hidden sm:flex sm:space-x-1">
                        <a href="#" class="border-transparent text-gray-600 hover:border-honda-red hover:text-honda-red inline-flex items-center px-3 border-b-2 text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        
                        <div class="relative flex" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="border-transparent text-gray-600 hover:border-honda-red hover:text-honda-red inline-flex items-center px-3 border-b-2 text-sm font-medium transition-colors focus:outline-none">
                                Master Data
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="absolute z-50 left-0 mt-16 w-48 rounded-none shadow-md bg-white border border-gray-100">
                                <div class="py-1">
                                    <a href="{{ route('motor-type.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Tipe Motor & Warna</a>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="border-transparent text-gray-600 hover:border-honda-red hover:text-honda-red inline-flex items-center px-3 border-b-2 text-sm font-medium transition-colors focus:outline-none">
                                Transaksi
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="absolute z-50 left-0 mt-16 w-56 rounded-none shadow-md bg-white border border-gray-100">
                                <div class="py-1">
                                    <a href="{{ route('motor-unit.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Registrasi Penerimaan Unit</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Faktur Kendaraan</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Surat Jalan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hidden sm:flex sm:items-center">
                    <div class="flex items-center gap-3 border-l pl-4 border-gray-200">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-800">Administrator</span>
                            <span class="text-xs text-gray-500">Head Office</span>
                        </div>
                        <div class="h-9 w-9 rounded-none bg-honda-red flex items-center justify-center text-white font-bold shadow-sm">
                            A
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

</body>
</html>