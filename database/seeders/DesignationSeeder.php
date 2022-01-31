<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //Designations

        // All Not Required
        DB::table('designations')->insert([
            'job_title_name' => 'Developer',
            'job_description' => 'Develop Web Site'
        ]);
        DB::table('designations')->insert([
            'job_title_name' => 'Creative Graphic Designer',
            'job_description' => 'Create Graphic Designs'
        ]);
        DB::table('designations')->insert([
            'job_title_name' => 'Chief Academic Officer',
            'job_description' => 'Manage Overall Academic Activities'
        ]);
        DB::table('designations')->insert([
            'job_title_name' => 'Creative Tech Lead',
            'job_description' => 'Lead Digital Media'
        ]);
    }
}
