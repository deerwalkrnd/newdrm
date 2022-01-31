<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearlyLeavesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //Yearly Leaves For Unit 1

        DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '1',
            'days' => '13',
            'status' => 'active',
            'year' => '2078'
        ]);//Required

         DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '3',
            'days' => '12',
            'status' => 'active',
            'year' => '2078'
        ]);
        DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '4',
            'days' => '60',
            'status' => 'active',
            'year' => '2078'
        ]);
         DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '5',
            'days' => '15',
            'status' => 'active',
            'year' => '2078'
        ]);

        //Yearly Leaves For Unit 2
       
        DB::table('yearly_leaves')->insert([
            'unit_id' => '2',
            'leave_type_id' => '1',
            'days' => '13',
            'status' => 'active',
            'year' => '2078'
        ]); //Required

         DB::table('yearly_leaves')->insert([
            'unit_id' => '2',
            'leave_type_id' => '3',
            'days' => '12',
            'status' => 'active',
            'year' => '2078'
        ]);
        DB::table('yearly_leaves')->insert([
            'unit_id' => '2',
            'leave_type_id' => '4',
            'days' => '60',
            'status' => 'active',
            'year' => '2078'
        ]);
         DB::table('yearly_leaves')->insert([
            'unit_id' => '2',
            'leave_type_id' => '5',
            'days' => '15',
            'status' => 'active',
            'year' => '2078'
        ]);
        // Yearly Leave for All Unit
        DB::table('yearly_leaves')->insert([
            'leave_type_id' => '6',
            'days' => '12',
            'status' => 'active',
            'year' => '2078'
        ]);
        
    }
}
