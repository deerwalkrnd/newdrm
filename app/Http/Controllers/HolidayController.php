<?php

namespace App\Http\Controllers;
use App\Models\Holiday;
use App\Models\Unit;

use Illuminate\Http\Request;

use App\Http\Requests\HolidayRequest;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = Holiday::select('id','name','date','female_only','unit_id')->orderBy('name')->get();
        return view('admin.holiday.index')->with(compact('holidays'));
    }

    public function myHoliday()
    {
        $holidays = Holiday::select('id','name','date','female_only','unit_id')
                            ->where(function($query){
                                $query->where('unit_id',\Auth::user()->employee->unit_id)
                                        ->orWhere('unit_id',null);
                            });
                            
        if(\Auth::user()->employee->gender != 'female')
        {
            $holidays = $holidays->where('female_only','0');
        }
                            
        $holidays = $holidays->orderBy('name')->get();
        return view('admin.holiday.index')->with(compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units = Unit::select('id','unit_name')->get();
        return view('admin.holiday.create')->with(compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HolidayRequest $request)
    {
        // dd($request);
        $input = $request->validated();
        Holiday::create($input);
        $res = [
            'title' => 'Holiday Created',
            'message' => 'Holiday has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/holiday')->with(compact('res'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);
        $units = Unit::select('id','unit_name')->get();
        return view('admin.holiday.edit')->with(compact('holiday','units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HolidayRequest $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        $input = $request->validated();
        
        $holiday->update($input);
        $res = [
            'title' => 'Holiday Updated',
            'message' => 'Holiday has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/holiday')->with(compact('res'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $holiday = Holiday::findOrFail($id);
            $holiday->delete();
            return redirect('/holiday');

        }catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
