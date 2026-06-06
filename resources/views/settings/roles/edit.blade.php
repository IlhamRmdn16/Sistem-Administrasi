@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-honda-red transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Edit Akses: <span class="text-honda-red">{{ $role->name }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-9">Perbarui hak akses fitur untuk jabatan ini.</p>
        </div>
    </div>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- INPUT NAMA ROLE -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Jabatan / Role <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ $role->name }}" required class="w-full md:w-1/2 border border-gray-300 rounded-lg p-3 outline-none focus:border-honda-red font-bold uppercase text-gray-800">
        </div>

        <h3 class="font-extrabold text-lg text-gray-900 mb-4 ml-1">Pilih Fitur yang Diizinkan (Permissions)</h3>

        <!-- GRID KELOMPOK CHECKBOX PER-MODUL -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($permissionGroups as $groupName => $permissions)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-gray-100 font-bold text-gray-800 text-sm">
                        {{ $groupName }}
                    </div>
                    <div class="p-4 space-y-3 h-full">
                        @foreach($permissions as $key => $label)
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="relative flex items-center mt-0.5">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                           class="peer w-5 h-5 cursor-pointer appearance-none rounded border-2 border-gray-300 checked:bg-honda-red checked:border-honda-red transition-all"
                                           {{ in_array($key, $rolePermissions) ? 'checked' : '' }}>
                                    <svg class="absolute w-5 h-5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors pt-0.5">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- TOMBOL SIMPAN -->
        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-honda-red hover:bg-red-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors flex items-center gap-2">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection