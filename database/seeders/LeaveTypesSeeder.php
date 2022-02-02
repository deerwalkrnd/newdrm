<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //leave types
        DB::table('leave_types')->insert([
            'name' => 'Personal',
            'gender' => 'All',
            'paid_unpaid' => '0'
        ]);//Required
        
        DB::table('leave_types')->insert([
            'name' => 'Carry Over',
            'gender' => 'All',
            'paid_unpaid' => '0'
        ]);//Required

        // DB::table('leave_types')->insert([
        //     'name' => 'Sick',
        //     'gender' => 'All',
        //     'paid_unpaid' => '0'
        // ]);

        //  DB::table('leave_types')->insert([
        //     'name' => 'Maternity',
        //     'gender' => 'Female',
        //     'paid_unpaid' => '0',
        //     'include_holiday' =>'1'

        // ]);

        // DB::table('leave_types')->insert([
        //     'name' => 'Paternity',
        //     'gender' => 'Male',
        //     'paid_unpaid' => '0',
        //     'include_holiday' =>'1'
        // ]);
        
        // DB::table('leave_types')->insert([
        //     'name' => 'Floating',
        //     'gender' => 'All',
        //     'paid_unpaid' => '0'
        // ]);
    }
}
