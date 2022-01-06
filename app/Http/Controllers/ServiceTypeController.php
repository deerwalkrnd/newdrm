<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ServiceType;
use App\Http\Requests\ServiceTypeRequest;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $serviceTypes = ServiceType::select('id', 'service_type_name')->get();
        return view('admin.serviceType.index')->with(compact('serviceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.serviceType.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceTypeRequest $request)
    {
        ServiceType::create($request->validated());
        $res = [
            'title' => 'Service Type Created',
            'message' => 'Service Type has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/serviceType')->with(compact('res'));
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
        $serviceType = ServiceType::select('id', 'service_type_name', 'date_required')->findOrFail($id);
        return view('admin.serviceType.edit')->with(compact('serviceType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceTypeRequest $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $serviceType->update($input);
        $res = [
            'title' => 'Service Type Updated',
            'message' => 'Service Type has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/serviceType')->with(compact('res'));
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
            $serviceType = ServiceType::findOrFail($id);
            $serviceType->delete();
            return redirect('/serviceType');
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
