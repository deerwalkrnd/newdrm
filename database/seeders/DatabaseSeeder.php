<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // call this to seed initially
        $this->call([
        ProvinceDistrictTableSeeder::class,
        OrganizationSeeder::class,
        // UnitSeeder::class, Not Required
        // DesignationSeeder::class,   //Not Required
        // ServiceTypesSeeder::class,  //Not Required
        RoleSeeder::class,
        // ShiftSeeder::class, //Not Required
        // HolidaySeeder::class,       //Not Required
        LeaveTypesSeeder::class,
        // YearlyLeavesSeeder::class,      //Not Required
        // DefaultEmployeeSeeder::class,   //Not Required
        // CarryOverLeaveSeeder::class,    //Not Required
        // ContactSeeder::class,   //Not Required
        // LeaveRequestSeeder::class,  //Not Required
        MailSettingSeeder::class,   
        TimeSettingSeeder::class,   
        ]);
        // \App\Models\User::factory(10)->create();

        //call this to creade user account for dmt
        // $this->call([
        //     HrEmployeeSeeder::class,   //Not Required
        // ]);
    }
}
