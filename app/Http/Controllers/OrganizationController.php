<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Organization;
use App\Models\Unit;
use App\Http\Requests\OrganizationRequest;
class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organizations = Organization::select('id', 'name', 'code')->get();
        return view('admin.organization.index')->with(compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.organization.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {
        Organization::create($request->validated());

        $res = [
            'title' => 'Organization Created',
            'message' => 'Organization has been successfully created',
            'icon' => 'success'
        ];

        return redirect('/organization')->with(compact('res'));
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
        $organization = Organization::select('id', 'name', 'code')->findOrFail($id);
        return view('admin.organization.edit')->with(compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest $request, $id)
    {
        $organization = Organization::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $organization->update($input);

        $res = [
            'title' => 'Organization Updated',
            'message' => 'Organization has been successfully updated',
            'icon' => 'success'
        ];

        return redirect('/organization')->with(compact('res'));
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
            $organization = Organization::findOrFail($id);
            $organization->delete();
            $res = [
                'title' => 'Organization Deleted',
                'message' => 'Organization has been successfully Deleted',
                'icon' => 'success'
            ];
            return redirect('/organization')->with(compact('res'));
        }catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                $res = [
                'title' => 'Organization Deletion Failed',
                'message' => 'Organization cannot be deleted as it is in Use.',
                'icon' => 'warning'
                ];
                return redirect()->back()->with(compact('res'));
            }
        }
    }  

    public function structure()
    {
        $organizations = Organization::select('id','name','code')
                        ->with('unit:id,unit_name,organization_id')
                        ->get();

        return view('admin.organization.structure')->with(compact('organizations'));
    }
}
