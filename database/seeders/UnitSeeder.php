<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Units 
        //All Required
        DB::table('units')->insert([
            'unit_name' => 'DWIT',
            'organization_id' => '1'
        ]);
        DB::table('units')->insert([
            'unit_name' => 'DSS',
            'organization_id' => '1'
        ]);
    }
}
