<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\LeaveType;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\EmployeeController;
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
        array_push($columns,'Unpaid Total Leave Taken','Exceeded Total Leave Days');

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
                $row['Exceeded Total Leave Days'] = $record['exceeded_leave_days'];
                $data = array($row['Employee'], $row['Year'], $row['Unit']);

                foreach($leaveTypes as $leaveType){
                    array_push($data,$row[$leaveType->name.' Acquired'],$row[$leaveType->name.' Allowed'],$row[$leaveType->name.' Taken'],$row[$leaveType->name.' Balance']);
                }               
                array_push($data,$row['Unpaid Total Leave Taken']);
                array_push($data,$row['Exceeded Total Leave Days']);
                fputcsv($file, $data);
            }

            fclose($file);
        };
        // $callback();
        return response()->stream($callback, 200, $headers);
        
    }

    public function exportEmployeeList(Request $request){
        $employeeController = new EmployeeController();
        $employeeDetails = $employeeController->index($request,$download=1);
        $employees = $employeeDetails[0];
        $join_year = $employeeDetails[1];

        $fileName = 'EmployeeList.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('Employee Name', 'Manager', 'Organization','Unit', 'Department', 'Internship/Traineeship Date','Join Date','Since Year','Status','Position','Date of Birth');

        $callback = function() use($employees, $columns,$join_year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $row=[];
            $i=0;
            foreach ($employees as $employee) {
                // dd($employee->unit->name);
                $row['Employee Name']  = $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name;
                
                if($employee->manager != NULL)
                    $row['Manager'] = $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name;
                else
                    $row['Manager'] = 'N/A';
                
                $row['Organization'] = $employee->organization->name;
                $row['Unit'] = $employee->unit->unit_name;
                $row['Department'] = $employee->department->name;
                $row['Internship/Traineeship Date'] = $employee->intern_trainee_ship_date;
                $row['Join Date'] = $employee->join_date;
                $row['Since Year'] = $join_year[$i];
                $row['Status'] = $employee->serviceType->service_type_name;
                $row['Position'] = $employee->designation->job_title_name;
                $row['Date of Birth'] = $employee->date_of_birth;
                $i++; 
                $data = array($row['Employee Name'], $row['Manager'], $row['Organization'],$row['Unit'], $row['Department'], $row['Internship/Traineeship Date'], $row['Join Date'], $row['Since Year'], $row['Status'], $row['Position'],$row['Date of Birth']);
                fputcsv($file, $data);
            }
            fclose($file);
        };
        // $callback();
        return response()->stream($callback, 200, $headers);
    }

    public function exportTerminatedEmployeeList(Request $request){
        $employeeController = new EmployeeController();
        $employees = $employeeController->terminated($request,$download=1);
        // dd($employees);
        // $employees = $employeeDetails[0];
        // $join_year = $employeeDetails[1];

        $fileName = 'TerminatedEmployeeList.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('Employee Name', 'Position', 'Manager','Join Date','Terminated Date');

        $callback = function() use($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $row=[];
            $i=0;
            foreach ($employees as $employee) {
                // dd($employee->unit->name);
                $row['Employee Name']  = $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name;
                $row['Position'] = $employee->designation->job_title_name;
                
                if($employee->manager != NULL)
                    $row['Manager'] = $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name;
                else
                    $row['Manager'] = 'N/A';
                
                $row['Join Date'] = $employee->join_date;
                
                $row['Terminated Date'] = $employee->terminated_date;
                
                $data = array($row['Employee Name'],$row['Position'], $row['Manager'], $row['Join Date'], $row['Terminated Date']);
                fputcsv($file, $data);
            }
            fclose($file);
        };
        // $callback();
        return response()->stream($callback, 200, $headers);
    }
}
