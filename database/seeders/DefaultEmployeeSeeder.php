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
        DB::table('service_types')->insert([
            'service_type_name' => 'Probation'
        ]);
        DB::table('service_types')->insert([
            'service_type_name' => 'Contract'
        ]);


        DB::table('shifts')->insert([
            'name' => 'Normal'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Morning'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Evening'
        ]);
        DB::table('shifts')->insert([
            'name' => 'Custom'
        ]);

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
        
        DB::table('organizations')->insert([
            'name' => 'DEG',
            'code' => 'DEG-001'
        ]);

        DB::table('units')->insert([
            'unit_name' => 'DWIT',
            'organization_id' => '1'
        ]);
        DB::table('units')->insert([
            'unit_name' => 'DSS',
            'organization_id' => '1'
        ]);

        DB::table('roles')->insert([
            'authority' => 'hr'
        ]);
         DB::table('roles')->insert([
            'authority' => 'manager'
        ]);
        DB::table('roles')->insert([
            'authority' => 'employee'
        ]);
       

        DB::table('leave_types')->insert([
            'name' => 'Personal',
            'gender' => 'All',
            'paid_unpaid' => '0'
        ]);

        DB::table('leave_types')->insert([
            'name' => 'Carry Over',
            'gender' => 'All',
            'paid_unpaid' => '0'
        ]);

        DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '1',
            'days' => '10',
            'status' => 'active',
            'year' => '2022'
        ]);
         DB::table('yearly_leaves')->insert([
            'unit_id' => '1',
            'leave_type_id' => '2',
            'days' => '8',
            'status' => 'active',
            'year' => '2022'
        ]);
        DB::table('holidays')->insert([
            'name' => 'Christmas',
            'date' => '2022-12-25',
            'female_only' => '0'
        ]);
         DB::table('holidays')->insert([
            'name' => 'Holi',
            'date' => '2022-03-17',
            'female_only' => '0'
        ]);
         DB::table('employees')->insert([
            'first_name' => 'Hitesh',
            'last_name' => 'karki',
            'date_of_birth' => '1990-01-01',
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
            'phone_no'=>'9841021923',
        ]);
        
        //Hitesh sir as hr

        DB::table('employees')->insert([
            'first_name' => 'Sagar',
            'last_name' => 'Shrestha',
            'date_of_birth' => '1990-01-01',
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
            'phone_no'=>'9841021923',
        ]);

        DB::table('managers')->insert([
            'employee_id' => '2',
            'is_active' => 'active',
        ]);
        //Sagar dai as manager



        DB::table('employees')->insert([
            'first_name' => 'Sataydeep',
            'last_name' => 'Neupane',
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
            'date_of_birth' => '1990-01-01',
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
            'gender' => 'male',
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

        //Contacts
         DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Hitesh Karki',
            'number'=>'9841021923',
        ]);
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Sagar Shrestha',
            'number'=>'9841021923',
        ]);
       
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Deena Sitikhu',
            'number'=>'9841021923',
        ]);
        DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Sahil Lodha',
            'number'=>'9841021923',
        ]);
       DB::table('contacts')->insert([
            'version'=>'0.0',
            'name' => 'Pradeepti Aryal',
            'number'=>'9841728131',
        ]);
        //CarryOver Leaves

        //Hitesh Karki
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'1',
            'year' => '2021',
            'days'=>'8',
        ]);
        //Sagar Dai
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'2',
            'year' => '2021',
            'days'=>'8',
        ]);
        //Satyadeep
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'3',
            'year' => '2021',
            'days'=>'8',
        ]);
        //Deena
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'4',
            'year' => '2021',
            'days'=>'8',
        ]);
        //Sahil
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'5',
            'year' => '2021',
            'days'=>'5',
        ]);
        
        DB::table('carry_over_leaves')->insert([
            'employee_id'=>'6',
            'year' => '2021',
            'days'=>'7',
        ]);

        //Leave Requests
        DB::table('leave_requests')->insert([
            'employee_id'=>'3',
            'start_date' => '2022-1-5',
            'end_date' => '2022-1-6',
            'days'=>'2',
            'year'=>'2022',
            'leave_type_id'=>'1',
            'full_leave'=>'1',
            'acceptance' => 'pending',
            'reason'=>'sick',
            'requested_by'=>'3',
        ]);      
        
        DB::table('leave_requests')->insert([
            'employee_id'=>'4',
            'start_date' => '2022-1-4',
            'end_date' => '2022-1-4',
            'days'=>'1',
            'year'=>'2022',
            'leave_type_id'=>'1',
            'half_leave'=>'first',
            'acceptance' => 'pending',
            'reason'=>'sick',
            'requested_by'=>'4',
        ]);      
        DB::table('leave_requests')->insert([
            'employee_id'=>'5',
            'start_date' => '2022-1-5',
            'end_date' => '2022-1-5',
            'days'=>'1',
            'year'=>'2022',
            'leave_type_id'=>'1',
            'full_leave'=>'1',
            'reason'=>'sick',
            'acceptance' => 'pending',
            'requested_by'=>'5',
        ]);      
        
    }
}
