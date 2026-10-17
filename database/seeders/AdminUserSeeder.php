<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@tech.mysatra.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '01700000000',
                'password' => Hash::make('tech@1234'),
                'status'   => 'active',
            ]
        );
        $admin->assignRole('Super Admin');

        $arafat = User::firstOrCreate(
            ['email' => 'arafat.dev61@gmail.com'],
            [
                'name'     => 'Arafat',
                'phone'    => '01700000001',
                'password' => Hash::make('tech@1234'),
                'status'   => 'active',
            ]
        );
        $arafat->assignRole('Super Admin');
    }
}
