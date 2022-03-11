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
        //Employees, users and emergency contacts
         DB::table('employees')->insert([
            'first_name' => 'Hitesh',
            'last_name' => 'karki',
            'date_of_birth' => '1990-01-13',
            'marital_status' => 'married',
            'gender' => 'male',
            'mobile' => '9841000000',
            'alter_email' => 'deenasitikhu@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2011-11-18',
            'service_type' => '1',
            'designation_id' => '3',
            'organization_id' => '1',
            'unit_id' => '1',
            'email' => 'deena.sitikhu@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '1',
            'role_id' => '1',
            'username' => 'hr',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => '1',
            'first_name' => 'Smriti',
            'last_name' => 'Mathema',
            'relationship'=>'Wife',
            'phone_no'=>'9841081923',
        ]);
        //Hitesh sir as hr

        DB::table('employees')->insert([
            'first_name' => 'Sagar',
            'last_name' => 'Shrestha',
            'date_of_birth' => '1990-01-21',
            'marital_status' => 'single',
            'gender' => 'male',
            'mobile' => '9841000000',
            'alter_email' => 'deenasitikhu@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '4',
            'organization_id' => '1',
            'unit_id' => '1',
            'email' => 'deenasitikhu@gmail.com',
            'shift_id' => '1'
        ]);
      
        DB::table('users')->insert([
            'employee_id' => '2',
            'role_id' => '2',
            'username' => 'manager',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => '2',
            'first_name' => 'Sujan',
            'last_name' => 'Shrestha',
            'relationship'=>'Uncle',
            'phone_no'=>'9841621923',
        ]);

        DB::table('managers')->insert([
            'employee_id' => '2',
            'is_active' => 'active',
        ]);
        //Sagar dai as manager

        DB::table('employees')->insert([
            'first_name' => 'Sataydeep',
            'last_name' => 'Neupane',
            'date_of_birth' => '1990-01-12',
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
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '1',
            'organization_id' => '1',
            'manager_id'=>'2',
            'unit_id' => '1',
            'email' => 'satyadeep.neupane@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '3',
            'role_id' => '3',
            'username' => 'satyadeep.neupane',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => '3',
            'first_name' => 'Ram',
            'last_name' => 'Sharma',
            'relationship'=>'Uncle',
            'phone_no'=>'9841021923',
        ]);
        //Satyadeep as employee

        DB::table('employees')->insert([
            'first_name' => 'Deena',
            'last_name' => 'Sitikhu',
            'date_of_birth' => '1990-01-10',
            'marital_status' => 'single',
            'gender' => 'female',
            'mobile' => '9841000000',
            'alter_email' => 'deena@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '1',
            'organization_id' => '1',
            'manager_id' => '2',
            'unit_id' => '1',
            'email' => 'deena.sitikhu@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '4',
            'role_id' => '3',
            'username' => 'deena.sitikhu',
            'password' => \Hash::make('newDrm3@')
        ]);

        DB::table('emergency_contacts')->insert([
            'employee_id' => '4',
            'first_name' => 'Dhana',
            'middle_name' => 'Ram',
            'last_name' => 'Sitikhu',
            'relationship'=>'Father',
            'phone_no'=>'9841301057',
        ]);
        //Deena as Employee

        DB::table('employees')->insert([
            'first_name' => 'Sahil',
            'last_name' => 'Lodha',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'gender' => 'male',
            'mobile' => '9841000000',
            'alter_email' => 'lodha@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '1',
            'organization_id' => '1',
            'manager_id'=>'2',
            'unit_id' => '1',
            'email' => 'sahil.lodha@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '5',
            'role_id' => '3',
            'username' => 'sahil.lodha',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => '5',
            'first_name' => 'Suyog',
            'last_name' => 'Lodha',
            'relationship'=>'Uncle',
            'phone_no'=>'9841021923',
        ]);
        //sahil as employee

        DB::table('employees')->insert([
            'first_name' => 'Pradeepti',
            'last_name' => 'Aryal',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'gender' => 'female',
            'mobile' => '9841000000',
            'alter_email' => 'aryal@gmail.com',
            'country' => 'Nepal',
            'permanent_address' => '3',
            'permanent_district' => '28',
            'permanent_municipality' => 'Baneshwor',
            'permanent_ward_no' => '12',
            'permanent_tole' => 'old baneshowr',
            'temp_add_same_as_per_add' => '1',
            'join_date' => '2021-11-18',
            'service_type' => '1',
            'designation_id' => '2',
            'organization_id' => '1',
            'manager_id'=>'2',
            'unit_id' => '1',
            'email' => 'deena.sitikhu@deerwalk.edu.np',
            'shift_id' => '1'
        ]);

        DB::table('users')->insert([
            'employee_id' => '6',
            'role_id' => '3',
            'username' => 'pradeepti.aryal',
            'password' => \Hash::make('newDrm3@')
        ]);
        DB::table('emergency_contacts')->insert([
            'employee_id' => '5',
            'first_name' => 'Prayush',
            'last_name' => 'Aryal',
            'relationship'=>'Uncle',
            'phone_no'=>'9841021923',
        ]);
            
    }
}
