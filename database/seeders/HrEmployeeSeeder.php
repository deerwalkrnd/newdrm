<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {              
        //Employees, users and emergency contacts
        $emp = DB::table('employees')->insertGetId([
            'first_name' => 'DMT',
            'last_name' => 'Developer',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'gender' => 'male',
            'mobile' => '9841000000',
            'alter_email' => 'deenasitikhu@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Sifal',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'Sifal',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2011-11-18',
            'service_type' => '1',
            'department_id' => '1',
            'designation_id' => '3',
            'organization_id' => '1',
            'unit_id' => '1',
            'email' => 'dmt@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => $emp,
            'role_id' => '1',
            'username' => 'dmt',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => $emp,
            'first_name' => 'R&D',
            'last_name' => 'Team',
            'relationship'=>'common',
            'phone_no'=>'980101928344',
        ]);
        //DMT as hr 
    }
}
