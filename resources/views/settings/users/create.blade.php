@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-honda-red transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Pendaftaran Karyawan Baru
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-9">Buat akun untuk akses ke dalam sistem.</p>
        </div>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Jabatan (Role) <span class="text-red-500">*</span></label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800 font-bold uppercase">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ $role }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Login <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kata Sandi (Password) <span class="text-red-500">*</span></label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
                <span class="text-[10px] text-gray-400 mt-1 block">Minimal 8 Karakter</span>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-honda-red hover:bg-red-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors">
                Daftarkan Akun
            </button>
        </div>
    </form>

</div>
@endsection