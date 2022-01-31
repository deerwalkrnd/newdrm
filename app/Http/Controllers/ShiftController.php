<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Shift;
use App\Http\Requests\ShiftRequest;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::select('id', 'name')->get();
        return view('admin.shift.index')->with(compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shift.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShiftRequest $request)
    {
        Shift::create($request->validated());
        $res = [
            'title' => 'Shift Created',
            'message' => 'Shift has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/shift')->with(compact('res'));
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
        $shift = Shift::select('id', 'name', 'time_required')->findOrFail($id);
        return view('admin.shift.edit')->with(compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ShiftRequest $request, $id)
    {
        $shift = Shift::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $shift->update($input);
        $res = [
            'title' => 'Shift Updated',
            'message' => 'Shift has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/shift')->with(compact('res'));
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
            $shift = Shift::findOrFail($id);
            $shift->delete();
             $res = [
                'title' => 'Shift Deleted',
                'message' => 'Shift has been successfully Deleted',
                'icon' => 'success'
            ];
            return redirect('/shift')->with(compact('res'));
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                $res = [
                    'title' => 'Deletion Failed',
                    'message' => 'Shift is Being Used',
                    'icon' => 'error'
                ];

                return redirect('/shift')->with(compact('res'));
            }
        }
    }
}
