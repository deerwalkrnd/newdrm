<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //All Required
        DB::table('times')->insert([
            'name'=>'Punch in Maximum Time',
            'time' => '09:20:00',
        ]);
        DB::table('times')->insert([
            'name'=>'First Half Leave Maximum Punch in Time',
            'time' => '13:30:00',
        ]);
        DB::table('times')->insert([
            'name'=>'Punch Out Minimum Time',
            'time' => '18:00:00',
        ]);
        DB::table('times')->insert([
            'name'=>'Second Half Leave Minimum Punch Out Time',
            'time' => '13:30:00',
        ]);
    }
}
