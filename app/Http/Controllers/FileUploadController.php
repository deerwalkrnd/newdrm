<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use App\Models\FileCategory;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = \Auth::user()->role->authority;
        
        if($role == 'hr'){
            $fileUploads = FileUpload::select('id','file_category_id','file_name','uploaded_by','employee_id')
                        ->with('fileCategory:id,category_name')
                        ->with('employee:id,first_name,middle_name,last_name')
                        ->orderBy('file_category_id')
                        ->orderBy('file_name')
                        ->get();
            // $employees = Employee::select('id','first_name','middle_name','last_name')->get();
            // dd($fileUploads);
            return view('admin.fileUpload.index')->with(compact('fileUploads'));
        }elseif($role == 'employee'){
            $fileUploads = FileUpload::select('id','file_category_id','file_name','uploaded_by','employee_id')
                        ->where('employee_id', \Auth::user()->employee_id)
                        ->with('fileCategory:id,category_name')
                        ->orderBy('file_category_id')
                        ->orderBy('file_name')
                        ->paginate(10);
            return view('admin.fileUpload.index')->with(compact('fileUploads'));
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fileCategories = FileCategory::select('id','category_name')->get();
        $role = \Auth::user()->role->authority;
        if($role == 'employee'){
            $employees = Employee::select('id','first_name','middle_name','last_name')->where('id',\Auth::user()->employee_id)->get();
        }elseif($role == 'hr'){
            $employees = Employee::select('id','first_name','middle_name','last_name')->get();
        }
        return view('admin.fileUpload.create')->with(compact('fileCategories','employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadRequest $request)
    {
        $input = $request->validated();
        $input['uploaded_by'] = \Auth::user()->employee_id;

        if(\Auth::user()->role->authority == 'employee'){
            $fileUpload_count = FileUpload::where('file_category_id',$input['file_category_id'])->where('employee_id',\Auth::user()->employee_id)->get()->count();
            if(!$fileUpload_count){
                return $this->fileStore($input);
            }else{
                return redirect('/file-upload/create'); 
            }
        }else{
            $fileUpload_count = FileUpload::where('file_category_id',$input['file_category_id'])->where('employee_id',$input['employee_id'])->get()->count();
            if(!$fileUpload_count){ 
                return $this->fileStore($input);
            }else{
                return redirect('/file-upload/create'); 
            }
        }
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
        $fileUpload = FileUpload::select('id', 'file_name','uploaded_by','file_category_id')->findOrFail($id);
        $fileCategories = FileCategory::select('id','category_name')->get();
        return view('admin.fileUpload.edit')->with(compact('fileUpload','fileCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FileUploadRequest $request, $id)
    {
        $fileUpload = FileUpload::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        // $input['version'] = DB::raw('version+1');

        $fileUpload->update($input);
        return redirect('/file-upload');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fileUpload = FileUpload::findOrFail($id);
        $path = $fileUpload['file_name'];
        $uploaded_by = $fileUpload['uploaded_by'];
        $employee_id = $fileUpload['employee_id'];

        if(strtolower(\Auth::user()->role->authority) == 'hr' || $employee_id  == \Auth::user()->employee_id ){
            // return Storage::download($path);
            Storage::delete($path);
            $fileUpload->delete();
            return redirect('/file-upload');
        }else{
           return redirect('/file-upload');
        }
        
    }

    public function download($id){
        // dd(\Auth::user()->employee_id);
        
        $fileUpload = FileUpload::findOrFail($id);
        $path = $fileUpload['file_name'];
        $uploaded_by = $fileUpload['uploaded_by'];
        $employee_id = $fileUpload['employee_id'];
        // dd($uploaded_by == \Auth::user()->employee_id );
        if(strtolower(\Auth::user()->role->authority) == 'hr' || $employee_id  == \Auth::user()->employee_id ){
            return Storage::download($path);
        }else{
           return redirect('/file-upload');
        }
       
    }

    private function fileStore($input){
        $input['file_name'] = $input['file']->store('pan');
        unset($input['file']);
        FileUpload::create($input);
        return redirect('/file-upload'); 
    }
    
}
