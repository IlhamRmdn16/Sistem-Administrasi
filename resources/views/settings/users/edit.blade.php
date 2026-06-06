@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-honda-red transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Edit Akun: <span class="text-honda-red">{{ $user->name }}</span>
            </h2>
        </div>
    </div>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $user->name }}" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Jabatan (Role) <span class="text-red-500">*</span></label>
                <select name="role" required {{ $user->email === 'superadmin@dealer.com' ? 'disabled' : '' }} class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800 font-bold uppercase">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ $userRole === $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($user->email === 'superadmin@dealer.com')
                    <!-- Hidden input jika select dalam kondisi disabled -->
                    <input type="hidden" name="role" value="{{ $userRole }}">
                @endif
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Login <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ $user->email }}" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red text-gray-800">
        </div>

        <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-xl mb-6">
            <p class="text-xs text-yellow-800 font-bold mb-3">Kosongkan kolom sandi di bawah ini jika tidak ingin mengubah kata sandi.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-md p-2 outline-none focus:border-honda-red text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-md p-2 outline-none focus:border-honda-red text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors">
                Perbarui Data Karyawan
            </button>
        </div>
    </form>

</div>
@endsection