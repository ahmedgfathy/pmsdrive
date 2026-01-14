<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing admin if exists
        User::where('email', 'ahmed.fathy@pms.eg')->delete();
        
        // Create fresh admin user
        User::create([
            'name' => 'Ahmed Fathy',
            'email' => 'ahmed.fathy@pms.eg',
            'password' => Hash::make('admin123'),
            'approved' => true,
            'is_admin' => true,
        ]);
        
        echo "✓ Admin user created: ahmed.fathy@pms.eg / admin123\n";
    }
}
