<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Branch;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Get or create the Admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['role_type' => 'Admin'],
            ['description' => 'System Administrator']
        );

        // ✅ Get or create a default branch
        $mainBranch = Branch::firstOrCreate(
            ['name' => 'Main Branch'],
            ['status' => 'active', 'address' => 'Head Office']
        );

        // ✅ Create admin staff if not exists
        Staff::firstOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'full_name'    => 'System Administrator',
                'phone_number' => '08012345678',
                'address'      => 'Head Office',
                'role_id'      => $adminRole->id,
                'branch_id'    => $mainBranch->id,
                'password'     => 'Admin123', // 🔒 Default password
            ]
        );
    }
}
