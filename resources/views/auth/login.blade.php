<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Administrasi Surya Wijaya</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 font-sans antialiased text-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        <div class="bg-gray-900 px-8 py-10 text-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-honda-red rounded-full opacity-20 blur-2xl"></div>

            <div class="w-16 h-16 bg-gradient-to-br from-honda-red to-red-700 rounded-2xl shadow-lg mx-auto flex items-center justify-center mb-4 relative z-10">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight relative z-10">DealerSys</h1>
            <p class="text-sm text-gray-400 mt-1 relative z-10">Sistem Administrasi Surya Wijaya</p>
        </div>

        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Masuk ke Akun Anda</h2>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm mb-5 font-medium text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@dealer.com" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-honda-red focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-honda-red focus:bg-white transition-all">
                </div>

                <button type="submit" class="w-full bg-honda-red hover:bg-red-800 text-white font-bold py-3.5 rounded-xl shadow-md transition-colors flex justify-center items-center gap-2 mt-2">
                    Masuk Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Surya Wijaya. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>
