<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          //Organizations
        DB::table('organizations')->insert([
            'name' => 'DEG',
            'code' => 'DEG-001'
        ]);
    }
}
