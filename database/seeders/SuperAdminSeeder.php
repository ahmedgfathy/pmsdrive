<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ahmed.fathy@pms.eg'],
            [
                'name' => 'Ahmed Fathy',
                'password' => Hash::make('ZeroCall20!@H'),
                'is_admin' => true,
                'approved' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
