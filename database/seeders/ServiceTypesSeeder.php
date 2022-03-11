<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //Service Types
        DB::table('service_types')->insert([
            'service_type_name' => 'Permanent'
        ]);
        DB::table('service_types')->insert([
            'service_type_name' => 'Probation'
        ]);
        DB::table('service_types')->insert([
            'service_type_name' => 'Contract'
        ]);
    }
}
