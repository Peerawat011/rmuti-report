<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * สร้างบัญชีผู้ดูแลระบบเริ่มต้น
     * รันด้วย: php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rmuti.ac.th'],
            [
                'first_name' => 'ผู้ดูแล',
                'last_name'  => 'ระบบ',
                'position'   => 'ผู้ดูแลระบบ',
                'department' => 'งานพัฒนาบุคลากร',
                'faculty'    => 'สำนักงานวิทยาเขตขอนแก่น',
                'role'       => 'admin',
                'password'   => Hash::make('admin1234'),
            ]
        );
    }
}
