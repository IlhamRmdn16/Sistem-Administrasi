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
        // 1. Bersihkan cache Spatie terlebih dahulu
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar isi Permission sesuai gembok menu di sidebar layouts/app
        $permissions = [
            // Kelompok Master Data
            'akses-master-motor',
            'akses-master-sales',
            'akses-master-leasing',
            'akses-master-pdiman',
            'akses-master-rekening',
            'akses-master-biaya',

            // Kelompok Transaksi
            'akses-registrasi-unit',
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

            // Kelompok Pengaturan Sistem
            'akses-manajemen-role',
            'akses-manajemen-user',
        ];

        // 3. Simpan semua permission ke database
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 4. Buat Role "Super Admin" dan berikan SEMUA permission di atas
        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // 5. Buat Akun User pertama untuk login Master Owner / Developer
        $user = User::updateOrCreate(
            ['email' => 'superadmin@dealer.com'], // Email untuk login
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Password untuk login
            ]
        );

        // 6. Pasangkan Role Super Admin ke user tersebut
        $user->assignRole($superAdminRole);
    }
}
