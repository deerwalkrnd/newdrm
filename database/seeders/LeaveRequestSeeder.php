<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Leave Requests

        // Not Required Any

        DB::table('leave_requests')->insert([
            'employee_id'=>'3',
            'start_date' => '2022-1-5',
            'end_date' => '2022-1-6',
            'days'=>'2',
            'year'=>'2078',
            'leave_type_id'=>'1',
            'full_leave'=>'1',
            'acceptance' => 'pending',
            'reason'=>'sick',
            'requested_by'=>'3',
        ]);      
        
        DB::table('leave_requests')->insert([
            'employee_id'=>'4',
            'start_date' => '2022-1-4',
            'end_date' => '2022-1-4',
            'days'=>'1',
            'year'=>'2078',
            'leave_type_id'=>'1',
            'half_leave'=>'first',
            'acceptance' => 'pending',
            'reason'=>'sick',
            'requested_by'=>'4',
        ]);      
        DB::table('leave_requests')->insert([
            'employee_id'=>'5',
            'start_date' => '2022-1-5',
            'end_date' => '2022-1-5',
            'days'=>'1',
            'year'=>'2078',
            'leave_type_id'=>'1',
            'full_leave'=>'1',
            'reason'=>'sick',
            'acceptance' => 'pending',
            'requested_by'=>'5',
        ]);    
    }
}
