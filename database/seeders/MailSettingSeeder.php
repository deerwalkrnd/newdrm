<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //All Mail Settings Required
        DB::table('mail_controls')->insert([
            'name' => 'Leave Request',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Pending Leave Request',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Timing Change',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Late Punch In',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Missed Punch Out',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Subordinate Leave',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Employee Credentials',
            'send_mail'=> '1'
        ]);
        DB::table('mail_controls')->insert([
            'name' => 'Early Punch Out',
            'send_mail'=> '1'
        ]);
    }
}
