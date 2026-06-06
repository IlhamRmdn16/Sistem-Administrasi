@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-honda-red rounded-full"></div>
                Manajemen Akses (Role)
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">Kelola nama jabatan dan hak akses modul sistem.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Role Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 text-[10px] uppercase text-gray-500 border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Jabatan (Role)</th>
                        <th class="py-3 px-4 text-center">Jumlah Izin Akses</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($roles as $index => $r)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-400 text-xs">{{ $roles->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">{{ $r->name }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($r->name === 'Super Admin')
                                    <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-xs">Akses Penuh (All)</span>
                                @else
                                    <span class="bg-blue-50 border border-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full text-xs">
                                        {{ $r->permissions->count() }} Fitur
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($r->name !== 'Super Admin')
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('roles.edit', $r->id) }}" class="bg-blue-50 text-blue-600 p-2 rounded hover:bg-blue-100 transition-colors" title="Edit Akses">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('roles.destroy', $r->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(event, this.closest('form'))" class="bg-red-50 text-red-600 p-2 rounded hover:bg-red-100 transition-colors" title="Hapus Role">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <div class="text-center text-xs text-gray-400 italic">Protected</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 italic">Belum ada role yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100 bg-slate-50/30">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection