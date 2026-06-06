<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('settings.users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Menggunakan string validasi murni agar tidak error PasswordBroker
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Karyawan berhasil didaftarkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // Proteksi Super Admin
        if ($user->email === 'superadmin@dealer.com' && $authUser->email !== 'superadmin@dealer.com') {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk mengedit akun ini.']);
        }

        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name')->first();
        
        return view('settings.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Nullable karena opsional saat edit
            'role' => 'required|string'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Jika form password diisi, maka perbarui password-nya
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Jangan izinkan siapapun mengubah role milik Super Admin utama
        if ($user->email !== 'superadmin@dealer.com') {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('users.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->email === 'superadmin@dealer.com') {
            return back()->withErrors(['error' => 'Akun Super Admin Utama tidak boleh dihapus!']);
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri saat sedang aktif login!']);
        }

        $user->delete();
        
        return back()->with('success', 'Karyawan berhasil dihapus dari sistem!');
    }
}
