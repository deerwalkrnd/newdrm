<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\Helpers\MailHelper;
use App\Helpers\NepaliCalendarHelper;
use App\Mail\MissedPunchOutMail;

use App\Models\Attendance;
use App\Models\LeaveRequest;



class MissedPunchOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punchOut:missed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $thisYear = $this->getNepaliYear(date('Y-m-d'));

        Attendance::where('punch_out_time',NULL)->whereDate('punch_in_time',date('Y-m-d'))->update(['missed_punch_out'=>'1','punch_out_time'=>date('Y-m-d H:i:s')]);
        
        $attendances = Attendance::where('missed_punch_out','1')
                                ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                                ->whereDate('punch_in_time',date('Y-m-d'))        //for cron job
                                ->get();

        foreach($attendances as $attendance){
             try{             
                $leaveRequest = LeaveRequest::create([
                    'employee_id' => $attendance->employee_id,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                    'days' => '1',
                    'year' => $thisYear,
                    'leave_type_id' => '1',
                    'full_leave' => '0',
                    'half_leave' => 'second',
                    'reason' => 'Forced (System) Missed Punch Out',
                    'acceptance' => 'accepted',
                    'requested_by' => $attendance->employee_id,
                    'accepted_by' => NULL
                ]);

            }catch(\Exception $e){
                \Log::debug($e);
            }
        return true;
        }
    }

    public function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            \Log::debug($nepaliDateArray);
            return $nepaliDateArray[0];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
}