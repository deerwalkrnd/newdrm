<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarryOverLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Not Required Any

        //Hitesh Karki
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'1',
            'year' => '2077',
            'days'=>'8',
        ]);
        //Sagar Dai
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'2',
            'year' => '2077',
            'days'=>'8',
        ]);
        //Satyadeep
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'3',
            'year' => '2077',
            'days'=>'8',
        ]);
        //Deena
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'4',
            'year' => '2077',
            'days'=>'8',
        ]);
        //Sahil
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'5',
            'year' => '2077',
            'days'=>'5',
        ]);
        
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'6',
            'year' => '2077',
            'days'=>'7',
        ]);
        
    }
}
