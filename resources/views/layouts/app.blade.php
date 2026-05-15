<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Administrasi Dealer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-honda-light text-gray-800 font-sans antialiased selection:bg-honda-red selection:text-white">

    <div x-data="{ sidebarOpen: false, isPinned: true, isHovered: false }" class="flex h-screen overflow-hidden">

        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 backdrop-blur-sm lg:hidden"
             @click="sidebarOpen = false"
             style="display: none;"></div>

        <aside
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
            :class="[(isPinned || isHovered) ? 'w-64 shadow-xl' : 'w-20 shadow-sm', sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
            class="fixed inset-y-0 left-0 z-30 flex flex-col bg-white border-r border-gray-100 transition-all duration-300 ease-in-out transform lg:static lg:translate-x-0">

            <div class="flex items-center justify-between h-16 border-b border-gray-100 px-4">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 bg-gradient-to-br from-honda-red to-red-700 rounded-lg shadow-sm shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span x-show="isPinned || isHovered"
                          x-transition:enter="transition ease-out duration-200 delay-100"
                          x-transition:enter-start="opacity-0 translate-x-2"
                          x-transition:enter-end="opacity-100 translate-x-0"
                          class="font-extrabold text-xl tracking-tight text-gray-900 whitespace-nowrap">DealerSys</span>
                </div>

                <button @click="isPinned = !isPinned" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-honda-red focus:outline-none shrink-0 transition-colors">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="isPinned ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 overflow-x-hidden custom-scrollbar">

                <p x-show="isPinned || isHovered" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>

                <a href="#" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-red-50 hover:text-honda-red rounded-lg transition-all duration-200 group">
                    <svg class="w-5 h-5 shrink-0 transition-colors group-hover:text-honda-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span x-show="isPinned || isHovered" class="ml-3 font-medium whitespace-nowrap">Dashboard</span>
                </a>

                <div x-data="{ open: false }" class="pt-1">
                    <button @click="open = !open; if(!isPinned && !isHovered) isPinned = true" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-600 hover:bg-red-50 hover:text-honda-red rounded-lg transition-all duration-200 focus:outline-none group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 shrink-0 transition-colors group-hover:text-honda-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            <span x-show="isPinned || isHovered" class="ml-3 font-medium whitespace-nowrap">Master Data</span>
                        </div>
                        <svg x-show="isPinned || isHovered" :class="{'rotate-180 text-honda-red': open}" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open && (isPinned || isHovered)"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                         class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 space-y-1">
                        <a href="{{ route('motor-type.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">Tipe Motor & Warna</a>
                        <a href="{{ route('sales.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg">Data Sales</a>
                        <a href="{{ route('leasing.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg">Data Leasing</a>
                    </div>
                </div>

                <div x-data="{ open: false }" class="pt-1">
                    <button @click="open = !open; if(!isPinned && !isHovered) isPinned = true" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-600 hover:bg-red-50 hover:text-honda-red rounded-lg transition-all duration-200 focus:outline-none group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 shrink-0 transition-colors group-hover:text-honda-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span x-show="isPinned || isHovered" class="ml-3 font-medium whitespace-nowrap">Transaksi</span>
                        </div>
                        <svg x-show="isPinned || isHovered" :class="{'rotate-180 text-honda-red': open}" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open && (isPinned || isHovered)"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                         class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 space-y-1">
                        <a href="{{ route('motor-unit.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">Registrasi Unit</a>
                        <a href="#" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">Faktur Kendaraan</a>
                        <a href="#" class="block px-3 py-2 text-sm font-medium text-gray-500 hover:text-honda-red hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">Surat Jalan</a>
                    </div>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="bg-white border-b border-gray-100 shadow-sm z-10">
                <div class="bg-gray-900 text-gray-300 text-xs py-1.5 px-6 hidden sm:block">
                    <div class="flex justify-between items-center max-w-full">
                        <span class="tracking-wider font-medium">SISTEM ADMINISTRASI <span class="text-honda-red mx-1">•</span> AHASS SURYA WIJAYA</span>
                        <span class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none lg:hidden mr-2 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <div class="hidden lg:block text-gray-400 text-sm">
                            <span class="font-medium text-gray-800">Selamat datang,</span> mari selesaikan pekerjaan hari ini.
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pl-4">
                        <button class="p-2 text-gray-400 hover:text-honda-red hover:bg-red-50 rounded-full transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-honda-red ring-2 ring-white"></span>
                        </button>

                        <div class="h-8 w-px bg-gray-200 mx-1"></div>

                        <button class="flex items-center gap-3 hover:opacity-80 transition-opacity focus:outline-none">
                            <div class="flex flex-col items-end hidden sm:flex">
                                <span class="text-sm font-bold text-gray-800 leading-none">Administrator</span>
                                <span class="text-xs text-gray-500 mt-1">Head Office</span>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-honda-red border-2 border-white shadow-sm flex items-center justify-center text-white font-bold text-lg overflow-hidden relative">
                                A
                            </div>
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>
</html>
