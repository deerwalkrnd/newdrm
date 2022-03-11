<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //Not Required any
         DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Hitesh Karki',
            'number'=>'9841021923',
        ]);
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Sagar Shrestha',
            'number'=>'9841021023',
        ]);
       
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Deena Sitikhu',
            'number'=>'9841022923',
        ]);
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Sahil Lodha',
            'number'=>'9821021923',
        ]);
       DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Pradeepti Aryal',
            'number'=>'9841728131',
        ]);
        
    }
}
