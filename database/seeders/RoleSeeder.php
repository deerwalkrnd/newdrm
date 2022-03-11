<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        //Reruired All
        DB::table('roles')->insert([
            'authority' => 'hr'
        ]);
         DB::table('roles')->insert([
            'authority' => 'manager'
        ]);
        DB::table('roles')->insert([
            'authority' => 'employee'
        ]);
    }
}
