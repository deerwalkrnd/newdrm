<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Province;


class SearchController extends Controller
{
    public function searchDistrict(Request $request)
    {
        $districts = [];
       
        if($request->has('p') && $request->p != NULL){
            $province_id=(int)$request->p;
            if($request->has('q'))
            {
                $keyword = $request->q;
                $districts = District::select('id','district_name','province_id')->where('province_id',(int)$request->p)->where('district_name','like',"%$keyword%")->take(5)->get();
            }else{
                $districts = District::select('id','district_name','province_id')->where('province_id',$province_id)->take(5)->get();
            }
        }
        
        return response()->json($districts);
    }
     /**
     * Search the specified resource from storage
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */ 
    public function searchEmployee(Request $request)
    {
        $employees = Employee::take(50)->get();
        if($request->has('q'))
        {
            $keyword = $request->q;
            $employees = Employee::select('id','first_name','middle_name','last_name')->where('contract_status','active')->where(DB::raw('CONCAT_WS(" ", first_name, middle_name, last_name)'),'like',"%$keyword%")->take(20)->get();
        }

        return response()->json($employees);
    }

    public function searchDepartment(Request $request)
    {
        $departments = [];
       
        if($request->has('p') && $request->p != NULL){
            $unit_id=(int)$request->p;
            if($request->has('q'))
            {
                $keyword = $request->q;
                $departments = Department::select('id','name','unit_id')->where('unit_id',(int)$request->p)->where('name','like',"%$keyword%")->take(5)->get();
            }else{
                $departments = Department::select('id','name','unit_id')->where('unit_id',$unit_id)->take(5)->get();
            }
        }
        
        return response()->json($departments);
    }
}
