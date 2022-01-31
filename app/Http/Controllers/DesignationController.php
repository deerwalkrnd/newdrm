<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Designation;
use App\Http\Requests\DesignationRequest;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designations = Designation::select('id', 'job_title_name', 'job_description')->get();
        return view('admin.designation.index')->with(compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.designation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DesignationRequest $request)
    {
        Designation::create($request->validated());
        $res = [
            'title' => 'Designation Created ',
            'message' => 'Designation has been successfully Created ',
            'icon' => 'success'
        ];
        return redirect('/designation')->with(compact('res'));
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
        $designation = Designation::select('id', 'job_title_name', 'job_description')->findOrFail($id);
        return view('admin.designation.edit')->with(compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DesignationRequest $request, $id)
    {
        $designation = Designation::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $designation->update($input);
        $res = [
            'title' => 'Designation Updated ',
            'message' => 'Designation has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/designation')->with(compact('res'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->delete();
        $designation->update($input);
        $res = [
            'title' => 'Designation Deleted ',
            'message' => 'Designation has been successfully Deleted ',
            'icon' => 'success'
        ];
        return redirect('/designation')->with(compact('res'));
    }
}
