<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role;
use App\Http\Requests\RoleRequest;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::select('id', 'authority')->get();
        return view('admin.role.index')->with(compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        Role::create($request->validated());
        $res = [
            'title' => 'Role Created',
            'message' => 'Role has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/role')->with(compact('res'));
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
        $role = Role::select('id', 'authority')->findOrFail($id);
        return view('admin.role.edit')->with(compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $role->update($input);
        $res = [
            'title' => 'Role Updated',
            'message' => 'Role has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/role')->with(compact('res'));
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
            $role = Role::findOrFail($id);
            $role->delete();
            return redirect('/role');
        }catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
