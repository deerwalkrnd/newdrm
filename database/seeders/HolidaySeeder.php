<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //Not Required Any

        DB::table('holidays')->insert([
            'name' => 'Christmas for DWIT',
            'unit_id'=>'1',
            'date' => '2022-01-10',
            'female_only' => '0'
        ]);
         DB::table('holidays')->insert([
            'unit_id'=>'1',
            'name' => 'Teej for DWIT',
            'date' => '2022-03-11',
            'female_only' => '1'
        ]);

        DB::table('holidays')->insert([
            'name' => 'Christmas for DSS',
            'unit_id'=>'2',
            'date' => '2022-01-12',
            'female_only' => '0'
        ]);
         DB::table('holidays')->insert([
            'unit_id'=>'2',
            'name' => 'Teej for DSS',
            'date' => '2022-09-09',
            'female_only' => '1'
        ]);

        DB::table('holidays')->insert([
            'name' => 'Sahid Diwas for All',
            'date' => '2022-01-14',
            'female_only' => '0'
        ]);

    }
}
