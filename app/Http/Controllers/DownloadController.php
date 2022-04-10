<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\LeaveType;
use App\Http\Controllers\LeaveReportController;
class DownloadController extends Controller
{
    
    public static function exportCsv()
    {
        
        $fileName = 'tasks.csv';
        $tasks = Unit::all();
        $tasks->toArray();
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('unit_name','organization_id');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['unit_name']  = $task['unit_name'];
                $row['oragnization_id']    = $task['organization_id'];
                

                fputcsv($file, array($row['unit_name'],$row['oragnization_id']));
            }

            fclose($file);
        };

            return response()->stream($callback, 200, $headers);
    }



    public function exportLeaveBalanceReport(Request $request){
        $leaveReportController = new LeaveReportController();
        $records = $leaveReportController->getLeaveBalanceRecords($request,$download=1);
        $leaveTypes = LeaveType::select('name','id','gender')->where('status','active')->get();

        $fileName = 'LeaveBalanceReport.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        
        $columns = array('Employee', 'Year', 'Unit');
        foreach($leaveTypes as $leaveType){
            array_push($columns,$leaveType->name.' Acquired',$leaveType->name.' Allowed',$leaveType->name.' Taken',$leaveType->name.' Balance');
        }               
        array_push($columns,'Unpaid Total Leave Taken');

        $callback = function() use($records, $columns,$leaveTypes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $row=[];
            foreach ($records as $record) {
                $row['Employee']  = $record['name'];
                $row['Year'] = $record['leaves']['year'];
                $row['Unit'] = $record['unit'];
                foreach($leaveTypes as $leaveType){
                    if(array_key_exists($leaveType->name,$record['leaves'])){
                            $row[$leaveType->name.' Acquired'] = $record['leaves'][$leaveType->name]['accrued'];
                            $row[$leaveType->name.' Allowed'] = $record['leaves'][$leaveType->name]['allowed'];
                            $row[$leaveType->name.' Taken'] = $record['leaves'][$leaveType->name]['taken'];
                            $row[$leaveType->name.' Balance'] = $record['leaves'][$leaveType->name]['balance'];
                    }else{
                        $row[$leaveType->name.' Acquired'] = 0;
                        $row[$leaveType->name.' Allowed'] = 0;
                        $row[$leaveType->name.' Taken'] = 0;
                        $row[$leaveType->name.' Balance'] = 0;
                    }
                }
                $row['Unpaid Total Leave Taken']  = $record['total_unpaid_leaves'];
                $data = array($row['Employee'], $row['Year'], $row['Unit']);

                foreach($leaveTypes as $leaveType){
                    array_push($data,$row[$leaveType->name.' Acquired'],$row[$leaveType->name.' Allowed'],$row[$leaveType->name.' Taken'],$row[$leaveType->name.' Balance']);
                }               
                array_push($data,$row['Unpaid Total Leave Taken']);
                fputcsv($file, $data);
            }

            fclose($file);
        };
        // $callback();
        return response()->stream($callback, 200, $headers);
        
    }
}
