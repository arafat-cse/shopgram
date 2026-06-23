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
            ['email' => 'admin@shopgram.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '01700000000',
                'password' => Hash::make('password'),
                'status'   => 'active',
            ]
        );

        $admin->assignRole('Super Admin');
    }
}
