<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run(): void
    {
        \DB::table('leave_types')->insert([
            'code' => 'SL',
            'name' => 'SICK LEAVE' ,
            'desc' => 'SICK LEAVE',
            'default_credit' => '7',
            'status' => '1',
            'is_visible' => '1' 
        ]);

        \DB::table('leave_types')->insert([
            'code' => 'EL',
            'name' => 'EMERGENCY LEAVE' ,
            'desc' => 'EMERGENCY LEAVE',
            'default_credit' => '5',
            'status' => '1',
            'is_visible' => '1' 
        ]);

        \DB::table('leave_types')->insert([
            'code' => 'VL',
            'name' => 'VACATION LEAVE' ,
            'desc' => 'VACATION LEAVE',
            'default_credit' => '7',
            'status' => '1',
            'is_visible' => '1' 
        ]);
    }
}
