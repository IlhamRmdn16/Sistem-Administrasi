<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
   private $permissionGroups = [
        'Modul Master Data' => [
            'akses-master-motor' => 'Master Motor',
            'akses-master-sales' => 'Master Sales/POP',
            'akses-master-leasing' => 'Master Leasing',
            'akses-master-pdiman' => 'Master PDI Man',
            'akses-master-rekening' => 'Master Rekening',
            'akses-master-biaya' => 'Master Biaya Administrasi',
        ],
        'Modul Transaksi & SPK' => [
            'akses-registrasi-unit' => 'Registrasi Unit Masuk',
            'akses-spk' => 'Pembuatan SPK / GPK',
            'akses-kontrol-harga' => 'Kontrol Harga Penjualan',
            'akses-surat-jalan' => 'Surat Jalan (SJK / SJG)',
            'akses-kuitansi-konsumen' => 'Kuitansi Konsumen (TTK)',
            'akses-kuitansi-lain' => 'Kuitansi Lain-Lain (Booking)',
        ],
        'Modul Manajemen Mutasi' => [
            'akses-mutasi-antar-gudang' => 'Mutasi Antar Gudang',
            'akses-mutasi-ke-showroom' => 'Mutasi Ke Showroom Pusat',
            'akses-mutasi-dari-showroom' => 'Mutasi Dari Showroom Pusat',
            'akses-mutasi-ke-pop' => 'Mutasi Ke POP',
            'akses-mutasi-dari-pop' => 'Mutasi Dari POP',
            'akses-mutasi-ke-gp' => 'Mutasi Ke GP (Motor Masuk)',
            'akses-mutasi-dari-gp' => 'Mutasi Dari GP (Motor Keluar)',
        ],
        'Modul Leasing & Pencairan' => [
            'akses-penagihan-leasing' => 'Penagihan Leasing (BTL)',
            'akses-pencairan-leasing' => 'Pencairan Leasing Pokok',
            'akses-penyerahan-bpkb' => 'Penyerahan BPKB ke Leasing',
        ],
        'Modul STNK & Samsat' => [
            'akses-pengajuan-stnk' => 'Pengajuan STNK',
            'akses-samsat' => 'Penerimaan STNK / BPKB',
            'akses-pajak-progresif' => 'Pajak Progresif',
            'akses-penyerahan-stnk' => 'Penyerahan STNK / BPKB ke Konsumen',
            'akses-cetak-blanko-samsat' => 'Cetak Blanko Samsat',
        ],
        'Pengaturan Sistem' => [
            'akses-manajemen-role' => 'Manajemen Hak Akses (Role)',
            'akses-manajemen-user' => 'Manajemen Karyawan (User)',
        ],
    ];

    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(10);
        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->permissionGroups;
        return view('settings.roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ], [
            'name.unique' => 'Nama Jabatan (Role) ini sudah ada di sistem.'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat role: ' . $e->getMessage()]);
        }
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Hak akses Super Admin tidak boleh diubah!']);
        }

        $permissionGroups = $this->permissionGroups;
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('settings.roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Super Admin tidak boleh diubah!']);
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role dan hak akses berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui role: ' . $e->getMessage()]);
        }
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Super Admin tidak boleh dihapus!']);
        }

        $role->delete();
        return back()->with('success', 'Role berhasil dihapus!');
    }
}
