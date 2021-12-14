<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_types')->insert([
            'service_type_name' => 'Permanent'
        ]);

        DB::table('shifts')->insert([
            'name' => 'Normal'
        ]);

        DB::table('designations')->insert([
            'job_title_name' => 'Developer',
            'job_description' => 'Develop Web Site'
        ]);

        DB::table('organizations')->insert([
            'name' => 'DWIT',
            'code' => 'DEG-001'
        ]);

        DB::table('units')->insert([
            'unit_name' => 'DMT',
            'organization_id' => '1'
        ]);

        DB::table('roles')->insert([
            'authority' => 'hr'
        ]);
        DB::table('leave_types')->insert([
            'name' => 'Personal',
            'gender' => 'All',
            'paid_unpaid' => '0'
        ]);
        DB::table('yearly_leaves')->insert([
            'organization_id' => '1',
            'leave_type_id' => '1',
            'days' => '2',
            'status' => 'active',
            'leave_year' => '2021'
        ]);
        DB::table('holidays')->insert([
            'name' => 'Christmas',
            'date' => '2021-12-25',
            'female_only' => '0'
        ]);

        DB::table('employees')->insert([
            // 'employee_id' => '1',
            'first_name' => 'Ram',
            'last_name' => 'Sharma',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'gender' => 'male',
            'mobile' => '9841000000',
            'alter_email' => 'satya@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '0',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '1',
            'organization_id' => '1',
            'unit_id' => '1',
            'email' => 'satyadeep.neupane@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '1',
            'role_id' => '1',
            'username' => 'satyadeep.neupane',
            'password' => \Hash::make('Deerwa1k@DRM')
        ]);
    }
}
