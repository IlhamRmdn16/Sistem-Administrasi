<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            'akses-mutasi-stok',
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
            
            'akses-manajemen-role',
            'akses-manajemen-user',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());

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
