<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //All Shifts Not Required
        DB::table('shifts')->insert([
            'name' => 'Normal'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Morning'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Evening'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Custom',
            'time_required'=>'1'
        ]);
    }
}
