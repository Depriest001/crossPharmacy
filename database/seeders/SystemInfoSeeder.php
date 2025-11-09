<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemInfo;

class SystemInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemInfo::create([
            'system_name' => 'The Cross Pharmacy',
            'email'       => 'info@crosspharmacy.com',
            'phone'       => '+2348012345678',
            'address'     => '123 Main Street, Lagos, Nigeria',
            'currency'    => '₦',
            'logo'        => 'logo.png',       // store in public/images or storage
            'favicon'     => 'favicon.ico',    // store in public/images or storage
        ]);
    }
}

