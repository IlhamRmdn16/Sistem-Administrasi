<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
  public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'akses-master-motor',
            'akses-master-sales',
            'akses-master-leasing',
            'akses-master-pdiman',
            'akses-master-rekening',
            'akses-master-biaya',

            'akses-registrasi-unit',
            'akses-mutasi-ke-showroom',
            'akses-mutasi-dari-showroom',
            'akses-mutasi-ke-pop',
            'akses-mutasi-dari-pop',
            'akses-mutasi-ke-gp',
            'akses-mutasi-dari-gp',
            'akses-mutasi-antar-gudang',
            'akses-spk',
            'akses-kontrol-harga',
            'akses-surat-jalan',
            'akses-kuitansi-konsumen',
            'akses-penagihan-leasing',
            'akses-pengajuan-stnk',
            'akses-samsat',
            'akses-pajak-progresif',
            'akses-penyerahan-stnk',
            'akses-penyerahan-bpkb',
            'akses-pencairan-leasing',
            'akses-kuitansi-lain',
            'akses-cetak-blanko-samsat',
            
            'akses-laporan-stok',
            'akses-laporan-penjualan',
            'akses-laporan-accu',
            'akses-laporan-motor-masuk',
            'akses-laporan-mutasi-showroom',
            
            'akses-manajemen-role',
            'akses-manajemen-user',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());

        $adminGpRole = Role::findOrCreate('Admin GP');
        $adminGpRole->givePermissionTo([
            'akses-mutasi-ke-gp',
            'akses-mutasi-dari-gp',
            'akses-spk',
            'akses-surat-jalan',
            'akses-kuitansi-konsumen',
            'akses-laporan-stok',
            'akses-laporan-penjualan',
            'akses-laporan-accu',
            'akses-laporan-motor-masuk',
            'akses-laporan-mutasi-showroom'
        ]);

        $user = User::updateOrCreate(
            ['email' => 'superadmin@dealer.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        $user->assignRole($superAdminRole);
    }
}
