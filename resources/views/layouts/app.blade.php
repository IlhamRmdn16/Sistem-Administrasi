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
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>

                    @canany(['akses-master-motor', 'akses-master-sales', 'akses-master-leasing', 'akses-master-pdiman', 'akses-master-rekening', 'akses-master-biaya'])
                    <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            Master Data
                            <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute left-0 mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                            @can('akses-master-motor') <a href="{{ route('motor-type.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Motor</a> @endcan
                            @can('akses-master-sales') <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Sales/POP</a> @endcan
                            @can('akses-master-leasing') <a href="{{ route('leasing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Leasing</a> @endcan
                            @can('akses-master-pdiman') <a href="{{ route('pdiman.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">PDI Man</a> @endcan
                            @can('akses-master-rekening') <a href="{{ route('rekening.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Rekening</a> @endcan
                            @can('akses-master-biaya')
                                <hr class="border-gray-200 my-1">
                                <a href="{{ route('biaya-administrasi.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Biaya Administrasi</a>
                            @endcan
                        </div>
                    </div>
                    @endcanany

                    @canany(['akses-registrasi-unit', 'akses-mutasi-ke-showroom', 'akses-mutasi-dari-showroom', 'akses-mutasi-ke-pop', 'akses-mutasi-dari-pop', 'akses-mutasi-ke-gp', 'akses-mutasi-dari-gp', 'akses-spk', 'akses-kontrol-harga', 'akses-surat-jalan', 'akses-kuitansi-konsumen', 'akses-penagihan-leasing', 'akses-pengajuan-stnk', 'akses-samsat', 'akses-pajak-progresif', 'akses-penyerahan-stnk', 'akses-penyerahan-bpkb', 'akses-pencairan-leasing', 'akses-kuitansi-lain', 'akses-cetak-blanko-samsat'])
                    <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Transaksi
                            <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute left-0 mt-1 w-64 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50 max-h-[70vh] overflow-y-auto">
                            @can('akses-registrasi-unit') <a href="{{ route('motor-unit.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Registrasi Unit</a> @endcan

                            @if(Auth::user()->hasRole('Admin GP'))
                                @can('akses-mutasi-ke-gp') <a href="{{ route('mutasi.index', 'ke-gp') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Motor Masuk</a> @endcan
                                @can('akses-mutasi-dari-gp') <a href="{{ route('mutasi.index', 'dari-gp') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Motor Keluar</a> @endcan
                            @else
                                @canany(['akses-mutasi-ke-showroom', 'akses-mutasi-dari-showroom', 'akses-mutasi-ke-pop', 'akses-mutasi-dari-pop', 'akses-mutasi-ke-gp', 'akses-mutasi-dari-gp', 'akses-mutasi-antar-gudang'])
                                    <div x-data="{ mutasiOpen: false }" class="relative">
                                        <button @click.prevent="mutasiOpen = !mutasiOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red focus:outline-none">
                                            <span>Manajemen Mutasi</span>
                                            <svg :class="mutasiOpen ? 'rotate-180' : ''" class="w-3 h-3 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="mutasiOpen" style="display: none;" class="pl-4 py-1 bg-gray-50 border-l-2 border-red-100 text-xs">
                                            @can('akses-mutasi-antar-gudang') <a href="{{ route('mutasi.index', 'antar-gudang') }}" class="block px-4 py-1.5 font-bold text-gray-800 hover:text-honda-red border-b border-gray-200 mb-1">Antar Gudang</a> @endcan
                                            @can('akses-mutasi-ke-showroom') <a href="{{ route('mutasi.index', 'ke-showroom') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Ke Showroom</a> @endcan
                                            @can('akses-mutasi-dari-showroom') <a href="{{ route('mutasi.index', 'dari-showroom') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Dari Showroom</a> @endcan
                                            @can('akses-mutasi-ke-pop') <a href="{{ route('mutasi.index', 'ke-pop') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Ke POP</a> @endcan
                                            @can('akses-mutasi-dari-pop') <a href="{{ route('mutasi.index', 'dari-pop') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Dari POP</a> @endcan
                                            @can('akses-mutasi-ke-gp') <a href="{{ route('mutasi.index', 'ke-gp') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Ke GP</a> @endcan
                                            @can('akses-mutasi-dari-gp') <a href="{{ route('mutasi.index', 'dari-gp') }}" class="block px-4 py-1.5 text-gray-600 hover:text-honda-red">Dari GP</a> @endcan
                                        </div>
                                    </div>
                                @endcanany
                            @endif

                            @canany(['akses-spk', 'akses-kontrol-harga', 'akses-surat-jalan', 'akses-kuitansi-konsumen'])
                            <hr class="border-gray-200 my-1">
                            @endcanany

                            @can('akses-spk') <a href="{{ route('spk.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">SPK</a> @endcan
                            @can('akses-kontrol-harga') <a href="{{ route('kontrol-harga.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Kontrol Harga Penjualan</a> @endcan
                            @can('akses-surat-jalan') <a href="{{ route('suratjalan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Surat Jalan</a> @endcan
                            @can('akses-kuitansi-konsumen') <a href="{{ route('kuitansi-konsumen.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Kuitansi Konsumen</a> @endcan

                            @can('akses-penagihan-leasing')
                            <hr class="border-gray-200 my-1">
                            <a href="{{ route('penagihan-leasing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Penagihan Leasing</a>
                            @endcan

                            @canany(['akses-pengajuan-stnk', 'akses-samsat', 'akses-pajak-progresif', 'akses-penyerahan-stnk', 'akses-penyerahan-bpkb', 'akses-pencairan-leasing', 'akses-cetak-blanko-samsat'])
                            <hr class="border-gray-200 my-1">
                            @endcanany

                            @can('akses-cetak-blanko-samsat') <a href="{{ route('cetak-blanko-samsat.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Cetak Blanko Samsat</a> @endcan
                            @can('akses-pengajuan-stnk') <a href="{{ route('pengajuan-stnk.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Pengajuan STNK</a> @endcan
                            @can('akses-samsat') <a href="{{ route('samsat.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Penerimaan STNK / BPKB</a> @endcan
                            @can('akses-pajak-progresif') <a href="{{ route('realisasi-pajak.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Cetak Realisasi Pajak Progresif</a> @endcan
                            @can('akses-penyerahan-stnk') <a href="{{ route('penyerahan-stnk-bpkb.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Penyerahan STNK / BPKB</a> @endcan
                            @can('akses-penyerahan-bpkb') <a href="{{ route('penyerahan-bpkb-leasing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Penyerahan BPKB ke Leasing</a> @endcan
                            @can('akses-pajak-progresif') <a href="{{ route('kwitansi-progresif.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Kwitansi Pajak Progresif</a> @endcan
                            @can('akses-pencairan-leasing') <a href="{{ route('pencairan-leasing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Pencairan Leasing Pokok</a> @endcan

                            @can('akses-kuitansi-lain')
                            <hr class="border-gray-200 my-1">
                            <a href="{{ route('kuitansi-lain.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Kuitansi Lain-Lain</a>
                            @endcan
                        </div>
                    </div>
                    @endcanany

                    @can('akses-laporan-stok')
                    <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Laporan
                            <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute left-0 mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">

                            <div x-data="{ subOpen: false }" class="relative" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                <button class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">
                                    Stok Unit
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                <div x-show="subOpen" style="display: none;" class="absolute left-full top-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-2 ml-1">
                                    <a href="{{ route('laporan.stok.global') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Global</a>
                                    <a href="{{ route('laporan.stok.warna') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Warna</a>
                                </div>

                            </div>

                        </div>
                    </div>
                    @endcan

                    @canany(['akses-manajemen-role', 'akses-manajemen-user'])
                    <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" @mouseleave="dropdownOpen = false" @mouseenter="dropdownOpen = true" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Pengaturan Akses
                            <svg :class="dropdownOpen ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute right-0 mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                            @can('akses-manajemen-role')
                                <a href="{{ route('roles.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Manajemen Role</a>
                            @endcan
                            @can('akses-manajemen-user')
                                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-honda-red">Manajemen User</a>
                            @endcan
                        </div>
                    </div>
                    @endcanany

                </nav>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 pl-4">

                <div x-data="{ profileOpen: false }" @click.away="profileOpen = false" class="relative hidden sm:block">
                    <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 hover:opacity-80 transition-opacity focus:outline-none">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="text-xs text-gray-500 mt-1">{{ Auth::user()->roles->pluck('name')->first() ?? 'Super Admin' }}</span>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-honda-red border-2 border-white shadow-sm flex items-center justify-center text-white font-bold text-base overflow-hidden relative">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="profileOpen" x-transition style="display: none;" class="absolute right-0 mt-3 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 font-bold hover:bg-red-50 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-honda-red focus:outline-none transition-colors ml-1">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="mobileMenuOpen" style="display:none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-gray-100 bg-white absolute w-full shadow-lg" style="display: none;">
        <nav class="px-4 pt-2 pb-6 space-y-1 max-h-[80vh] overflow-y-auto">

            <a href="{{ route('dashboard') }}" class="block px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg">
                Dashboard
            </a>

            @canany(['akses-master-motor', 'akses-master-sales', 'akses-master-leasing', 'akses-master-pdiman', 'akses-master-rekening', 'akses-master-biaya'])
            <div x-data="{ subOpen: false }">
                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                    <span>Master Data</span>
                    <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                    @can('akses-master-motor') <a href="{{ route('motor-type.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Motor</a> @endcan
                    @can('akses-master-sales') <a href="{{ route('sales.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Sales</a> @endcan
                    @can('akses-master-leasing') <a href="{{ route('leasing.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Leasing</a> @endcan
                    @can('akses-master-pdiman') <a href="{{ route('pdiman.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">PDI Man</a> @endcan
                    @can('akses-master-rekening') <a href="{{ route('rekening.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Rekening</a> @endcan
                    @can('akses-master-biaya') <a href="{{ route('biaya-administrasi.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Biaya Administrasi</a> @endcan
                </div>
            </div>
            @endcanany

            @canany(['akses-registrasi-unit', 'akses-mutasi-ke-showroom', 'akses-mutasi-dari-showroom', 'akses-mutasi-ke-pop', 'akses-mutasi-dari-pop', 'akses-mutasi-ke-gp', 'akses-mutasi-dari-gp', 'akses-spk', 'akses-kontrol-harga', 'akses-surat-jalan', 'akses-kuitansi-konsumen', 'akses-penagihan-leasing', 'akses-pengajuan-stnk', 'akses-samsat', 'akses-pajak-progresif', 'akses-penyerahan-stnk', 'akses-penyerahan-bpkb', 'akses-pencairan-leasing', 'akses-kuitansi-lain', 'akses-cetak-blanko-samsat'])
            <div x-data="{ subOpen: false }">
                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                    <span>Transaksi</span>
                    <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                    @can('akses-registrasi-unit') <a href="{{ route('motor-unit.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Registrasi Unit</a> @endcan

                    @if(Auth::user()->hasRole('Admin GP'))
                        @can('akses-mutasi-ke-gp') <a href="{{ route('mutasi.index', 'ke-gp') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Motor Masuk</a> @endcan
                        @can('akses-mutasi-dari-gp') <a href="{{ route('mutasi.index', 'dari-gp') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Motor Keluar</a> @endcan
                    @else
                        @canany(['akses-mutasi-ke-showroom', 'akses-mutasi-dari-showroom', 'akses-mutasi-ke-pop', 'akses-mutasi-dari-pop', 'akses-mutasi-ke-gp', 'akses-mutasi-dari-gp', 'akses-mutasi-antar-gudang'])
                            <div x-data="{ mutasiOpen: false }">
                                <button @click.prevent="mutasiOpen = !mutasiOpen" class="w-full flex items-center justify-between px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                                    <span>Manajemen Mutasi</span>
                                    <svg :class="mutasiOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="mutasiOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                                    @can('akses-mutasi-antar-gudang') <a href="{{ route('mutasi.index', 'antar-gudang') }}" class="block px-3 py-2 text-sm font-bold text-gray-800 hover:text-honda-red border-b border-gray-100">Antar Gudang</a> @endcan
                                    @can('akses-mutasi-ke-showroom') <a href="{{ route('mutasi.index', 'ke-showroom') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Ke Showroom</a> @endcan
                                    @can('akses-mutasi-dari-showroom') <a href="{{ route('mutasi.index', 'dari-showroom') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Dari Showroom</a> @endcan
                                    @can('akses-mutasi-ke-pop') <a href="{{ route('mutasi.index', 'ke-pop') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Ke POP</a> @endcan
                                    @can('akses-mutasi-dari-pop') <a href="{{ route('mutasi.index', 'dari-pop') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Dari POP</a> @endcan
                                    @can('akses-mutasi-ke-gp') <a href="{{ route('mutasi.index', 'ke-gp') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Ke GP</a> @endcan
                                    @can('akses-mutasi-dari-gp') <a href="{{ route('mutasi.index', 'dari-gp') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Dari GP</a> @endcan
                                </div>
                            </div>
                        @endcanany
                    @endif

                    @can('akses-spk') <a href="{{ route('spk.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">SPK</a> @endcan
                    @can('akses-kontrol-harga') <a href="{{ route('kontrol-harga.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Kontrol Harga Penjualan</a> @endcan
                    @can('akses-surat-jalan') <a href="{{ route('suratjalan.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Surat Jalan</a> @endcan
                    @can('akses-kuitansi-konsumen') <a href="{{ route('kuitansi-konsumen.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Kuitansi Konsumen</a> @endcan
                    @can('akses-penagihan-leasing') <a href="{{ route('penagihan-leasing.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Penagihan Leasing</a> @endcan
                    @can('akses-cetak-blanko-samsat') <a href="{{ route('cetak-blanko-samsat.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Cetak Blanko Samsat</a> @endcan
                    @can('akses-pengajuan-stnk') <a href="{{ route('pengajuan-stnk.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Pengajuan STNK</a> @endcan
                    @can('akses-samsat') <a href="{{ route('samsat.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Penerimaan STNK / BPKB</a> @endcan
                    @can('akses-pajak-progresif') <a href="{{ route('realisasi-pajak.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Cetak Realisasi Pajak Progresif</a> @endcan
                    @can('akses-penyerahan-stnk') <a href="{{ route('penyerahan-stnk-bpkb.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Penyerahan STNK / BPKB</a> @endcan
                    @can('akses-penyerahan-bpkb') <a href="{{ route('penyerahan-bpkb-leasing.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Penyerahan BPKB ke Leasing</a> @endcan
                    @can('akses-pajak-progresif') <a href="{{ route('kwitansi-progresif.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Kwitansi Pajak Progresif</a> @endcan
                    @can('akses-pencairan-leasing') <a href="{{ route('pencairan-leasing.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Pencairan Leasing Pokok</a> @endcan
                    @can('akses-kuitansi-lain') <a href="{{ route('kuitansi-lain.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Kuitansi Lain-Lain</a> @endcan
                </div>
            </div>
            @endcanany

            @can('akses-laporan-stok')
                <div x-data="{ subOpen: false }">
                    <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                        <span>Laporan</span>
                        <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                        <div x-data="{ stokOpen: false }">
                            <button @click.prevent="stokOpen = !stokOpen" class="w-full flex items-center justify-between px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                                <span>Stok Unit</span>
                                <svg :class="stokOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="stokOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
    <a href="{{ route('laporan.stok.global') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Global</a>
    <a href="{{ route('laporan.stok.warna') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Warna</a>
</div>
                        </div>
                    </div>
                </div>
            @endcan

            @canany(['akses-manajemen-role', 'akses-manajemen-user'])
            <div x-data="{ subOpen: false }">
                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-3 text-base font-medium text-gray-800 hover:bg-red-50 hover:text-honda-red rounded-lg focus:outline-none">
                    <span>Pengaturan Akses</span>
                    <svg :class="subOpen ? 'rotate-180 text-honda-red' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="subOpen" style="display: none;" class="pl-4 mt-1 space-y-1 border-l-2 border-red-100 ml-3">
                    @can('akses-manajemen-role')
                        <a href="{{ route('roles.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Manajemen Role</a>
                    @endcan
                    @can('akses-manajemen-user')
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-honda-red">Manajemen User</a>
                    @endcan
                </div>
            </div>
            @endcanany

            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3 px-3">
                <div class="h-10 w-10 rounded-full bg-honda-red flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="text-xs text-gray-500">{{ Auth::user()->roles->pluck('name')->first() ?? 'Super Admin' }}</div>
                </div>
            </div>

            <div class="mt-4 px-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-red-600 font-bold py-2 hover:opacity-80">
                        Keluar Sistem
                    </button>
                </form>
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
            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true,
                    customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error', title: 'Terjadi Kesalahan!',
                    html: `
                        <ul class="text-left text-sm text-gray-600 mt-2 space-y-1 pl-4 list-disc">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    `,
                    customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
                });
            @endif

            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (form.method.toUpperCase() === 'GET') return;
                    Swal.fire({
                        title: 'Mohon Tunggu...', html: 'Sistem sedang memproses data Anda.',
                        allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                });
            });
        });

        function confirmDelete(event, formElement) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?', text: "Data yang dihapus tidak dapat dikembalikan!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, hapus data!', cancelButtonText: 'Batal', reverseButtons: true,
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-lg px-6 py-2.5 font-bold', cancelButton: 'rounded-lg px-6 py-2.5 font-bold' }
            }).then((result) => {
                if (result.isConfirmed) { formElement.submit(); }
            });
        }
    </script>
</body>
</html>
