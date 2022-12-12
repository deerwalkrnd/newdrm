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
        $fileUploads = FileUpload::select('id','file_category_id','file_name','uploaded_by','employee_id')
                                ->with('fileCategory:id,category_name')
                                ->with('uploader:id,first_name,middle_name,last_name')
                                ->with('employee:id,first_name,middle_name,last_name')
                                ->orderBy('file_category_id')
                                ->orderBy('file_name')
                                ->get();
        return view('admin.fileUpload.index')->with(compact('fileUploads'));       
    }

    public function myFileIndex()
    {
        $fileUploads = FileUpload::select('id','file_category_id','file_name','uploaded_by','employee_id')
                        ->where('employee_id', \Auth::user()->employee_id)
                        ->with('fileCategory:id,category_name')
                        ->with('uploader:id,first_name,middle_name,last_name')
                        ->with('employee:id,first_name,middle_name,last_name')
                        ->orderBy('file_category_id')
                        ->orderBy('file_name')
                        ->get();

        return view('admin.fileUpload.myIndex')->with(compact('fileUploads'));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fileCategories = FileCategory::select('id','category_name','status')->where('status','active')->get();
    
        $employee = Employee::select('id','first_name','middle_name','last_name')
                                ->where('contract_status','active')
                                ->where('id',\Auth::user()->employee_id)
                                ->first();
        // dd($employee->id);
        // if(\Auth::user()->role->authority != "hr")
        //     $employees = $employees->where('id',\Auth::user()->employee_id);
                                
        // $employees = $employees->get();
        return view('admin.fileUpload.create')->with(compact('fileCategories','employee'));
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
        $category_name = FileCategory::findOrFail($input['file_category_id'])->category_name;

        $input['uploaded_by'] = \Auth::user()->employee_id;
        $input['file_name'] = $input['file']->store($category_name);
        unset($input['file']);
        
        FileUpload::create($input);

        if((\Auth::user()->role->authority == "hr") && ($input['employee_id']  != \Auth::user()->employee_id ))
        {
            $res = [
            'title' => 'File  Uploaded ',
            'message' => 'File  has been successfully Uploaded ',
            'icon' => 'success'
            ];
           return redirect('/file-upload')->with(compact('res'));
        }else{
            $res = [
            'title' => 'File  Uploaded ',
            'message' => 'File  has been successfully Uploaded ',
            'icon' => 'success'
            ];
           return redirect('/my-file-upload')->with(compact('res'));
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
        //no edit
        return abort(404);
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
        //no update
        return abort(404);
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
        $res=[];

        if((\Auth::user()->role->authority == "hr") || ($employee_id  == \Auth::user()->employee_id ))
        {
            if(Storage::exists($path))
            {
                Storage::delete($path);
            }
            $fileUpload->delete();
            $res = [
                'title' => 'File Deleted ',
                'message' => 'File  has been successfully Deleted ',
                'icon' => 'success'
                ];
        }
        if((\Auth::user()->role->authority == "hr") )
        {
           return redirect()->back()->with(compact('res'));
        }else{
            return redirect('/my-file-upload')->with(compact('res'));
        }        
    }

    public function download($id){
        $fileUpload = FileUpload::findOrFail($id);
        $path = $fileUpload['file_name'];
        $uploaded_by = $fileUpload['uploaded_by'];
        $employee_id = $fileUpload['employee_id'];

        try{
            $newFileName = $this->getNewFileName($fileUpload);
            if(strtolower(\Auth::user()->role->authority) == 'hr' || $employee_id  == \Auth::user()->employee_id ){
                if(Storage::exists($path))
                {
                    return Storage::download($path, $newFileName);    
                }else{
                    $res = [
                        'title' => 'File Not Found',
                        'message' => 'File not found in the server.',
                        'icon' => 'error'
                        ];
                    return redirect('/file-upload')->with(compact('res'));
                }
            }else{
               return redirect('/file-upload');
            }
        }catch(Exception $e){
            $res = [
                'title' => 'File Name Invalid',
                'message' => 'Filename contains some error.',
                'icon' => 'success'
                ];

            return redirect('/file-upload')->with(compact($res));
        }
    }

    private function fileStore($input){
        $input['file_name'] = $input['file']->store('pan');
        unset($input['file']);
        FileUpload::create($input);
        return redirect('/file-upload'); 
    }

    private function getNewFileName($fileUpload)
    {
        $ext = pathinfo($fileUpload->file_name, PATHINFO_EXTENSION);
        $fileCategory = $fileUpload->fileCategory->category_name;
        $fileCategory = str_replace(' ', '', $fileCategory);
        $file_name = $fileCategory.'_'.$fileUpload->employee->first_name.'_'.$fileUpload->employee->last_name.'.'.$ext;
        
        return $file_name;
    }
    
}
