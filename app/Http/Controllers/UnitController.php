<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Unit;
use App\Models\Organization;
use App\Http\Requests\UnitRequest;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::select('id', 'unit_name', 'organization_id')
                        ->with('organization:id,name')
                        ->orderBy('organization_id')
                        ->orderBy('unit_name')
                        ->get();
        return view('admin.unit.index')->with(compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = Organization::select('id','name')->get();
        $res = [
            'title' => 'Unit Created',
            'message' => 'Unit has been successfully created',
            'icon' => 'success'
        ];
        return view('admin.unit.create')->with(compact('organizations','res'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitRequest $request)
    {
        Unit::create($request->validated());
        return redirect('/unit');
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
        $unit = Unit::select('id', 'unit_name', 'organization_id')->findOrFail($id);
        $organizations = Organization::select('id','name')->get();
        return view('admin.unit.edit')->with(compact('unit','organizations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UnitRequest $request, $id)
    {
        $unit = Unit::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $unit->update($input);
        $res = [
            'title' => 'Unit Updated',
            'message' => 'Unit has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/unit')->with(compact('res'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        $res = [
            'title' => 'Unit Deleted',
            'message' => 'Unit has been successfully Deleted',
            'icon' => 'success'
        ];
        return redirect('/unit')->with(compact('res'));
    }
}
