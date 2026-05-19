<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Administrasi Dealer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-gray-800 font-sans antialiased selection:bg-honda-red selection:text-white min-h-screen flex flex-col">

    <div x-data="{ mobileMenuOpen: false }">

        <div class="bg-gray-900 text-gray-300 text-xs py-1.5 px-4 sm:px-6 lg:px-8 relative z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <span class="tracking-wider font-medium hidden sm:inline-block">SISTEM ADMINISTRASI <span class="text-honda-red mx-1">•</span>SURYA WIJAYA</span>
                <span class="tracking-wider font-medium sm:hidden">SURYA WIJAYA</span>
                <span class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y') }}
                </span>
            </div>
        </div>

        <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-honda-red to-red-700 rounded-lg shadow-sm flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-gray-900">DealerSys</span>
                        </div>

                        <nav class="hidden md:flex items-center space-x-1">
                            <a href="#" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Dashboard
                            </a>

                            <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                    Master Data
                                    <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="dropdownOpen"
                                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                                     style="display: none;"
                                     class="absolute left-0 mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                                    <a href="{{ route('motor-type.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Motor</a>
                                    <a href="{{ route('sales.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Sales/POP</a>
                                    <a href="{{ route('leasing.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Leasing</a>
                                    <a href="{{ route('pdiman.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">PDI Man</a>
                                    <a href="{{ route('rekening.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Rekening</a>
                                    <hr class="border-gray-200 my-1">
                                    <a href="{{ route('biaya-administrasi.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Biaya Administrasi</a>
                                </div>
                            </div>

                            <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Transaksi
                                    <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="dropdownOpen"
                                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                                     style="display: none;"
                                     class="absolute left-0 mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                                    <a href="{{ route('motor-unit.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Registrasi Unit</a>
                                    <hr class="border-gray-200 my-1">
                                    <a href="{{ route('spk.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">SPK</a>
                                    <a href="{{ route('suratjalan.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">Surat Jalan</a>
                                    <hr class="border-gray-200 my-1">
                                    <a href="{{ route('samsat.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red transition-colors">STNK / BPKB</a>
                                </div>
                            </div>
                        </nav>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-4 pl-4">

                        <button class="p-2 text-gray-400 hover:text-honda-red hover:bg-red-50 rounded-full transition-colors relative focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-honda-red ring-2 ring-white"></span>
                        </button>

                        <div class="hidden sm:block h-6 w-px bg-gray-200 mx-1"></div>

                        <button class="hidden sm:flex items-center gap-3 hover:opacity-80 transition-opacity focus:outline-none">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-bold text-gray-800 leading-none">Administrator</span>
                                <span class="text-xs text-gray-500 mt-1">Head Office</span>
                            </div>
                            <div class="h-9 w-9 rounded-full bg-honda-red border-2 border-white shadow-sm flex items-center justify-center text-white font-bold text-base overflow-hidden relative">
                                A
                            </div>
                        </button>

                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-honda-red focus:outline-none transition-colors ml-1">
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            <svg x-show="mobileMenuOpen" style="display:none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                    </div>
                </div>
            </div>

            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                 style="display: none;"
                 class="md:hidden border-t border-gray-100 bg-white absolute w-full shadow-lg">
                <nav class="px-4 pt-2 pb-6 space-y-1 max-h-[80vh] overflow-y-auto">

                    <a href="#" class="block px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg">
                        Dashboard
                    </a>

                    <div x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                            <span>Master Data</span>
                            <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                            <a href="{{ route('motor-type.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Motor</a>
                            <a href="{{ route('sales.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Sales</a>
                            <a href="{{ route('leasing.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Leasing</a>
                            <a href="{{ route('pdiman.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">PDI Man</a>
                            <a href="{{ route('rekening.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Rekening</a>
                            <a href="{{ route('biaya-administrasi.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Biaya Administrasi</a>
                        </div>
                    </div>

                    <div x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                            <span>Transaksi</span>
                            <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                            <a href="{{ route('motor-unit.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Registrasi Unit</a>
                            <hr class="border-gray-200 my-1">
                            <a href="{{ route('spk.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">SPK</a>
                            <a href="{{ route('suratjalan.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Surat Jalan</a>
                            <hr class="border-gray-200 my-1">
                            <a href="{{ route('samsat.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">STNK / BPKB</a>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3 px-3">
                        <div class="h-10 w-10 rounded-full bg-honda-red flex items-center justify-center text-white font-bold text-lg">A</div>
                        <div>
                            <div class="text-sm font-bold text-gray-800">Administrator</div>
                            <div class="text-xs text-gray-500">Head Office</div>
                        </div>
                    </div>

                </nav>
            </div>
        </header>

        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 backdrop-blur-sm md:hidden"
             @click="mobileMenuOpen = false"
             style="display: none;"></div>

    </div>

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Pop-up Sukses
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100'
                    }
                });
            @endif

            // 2. Pop-up Error Validasi
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    html: `
                        <ul class="text-left text-sm text-gray-600 mt-2 space-y-1 pl-4 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100'
                    }
                });
            @endif

            // 3. Loading Animasi Form Submit
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (form.method.toUpperCase() === 'GET') return; // Abaikan loading untuk form Search/GET

                    Swal.fire({
                        title: 'Mohon Tunggu...',
                        html: 'Sistem sedang memproses data Anda.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            });
        });

        // 4. Global Function untuk Konfirmasi Hapus
        function confirmDelete(event, formElement) {
            event.preventDefault(); // Hentikan submit langsung

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, hapus data!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl',
                    confirmButton: 'rounded-lg px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-lg px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit(); // Jika Ya, submit dan jalankan loading di atas
                }
            });
        }
    </script>
</body>
</html>
