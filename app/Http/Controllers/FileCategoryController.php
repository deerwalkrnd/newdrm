<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileCategoryRequest;
use App\Models\FileCategory;

class FileCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fileCategories = FileCategory::select('id', 'category_name','status')->get();
        return view('admin.fileCategory.index')->with(compact('fileCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.fileCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileCategoryRequest $request)
    {
        
        // dd('here');
        FileCategory::create($request->validated());
        $res = [
            'title' => 'File Category Created ',
            'message' => 'File Category has been successfully Created ',
            'icon' => 'success'
        ];
        return redirect('/file-category')->with(compact('res'));
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
        $fileCategory = FileCategory::select('id', 'category_name','status')->findOrFail($id);
        return view('admin.fileCategory.edit')->with(compact('fileCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FileCategoryRequest $request, $id)
    {
        $fileCategory = FileCategory::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $fileCategory->update($input);
        $res = [
            'title' => 'File Category Updated ',
            'message' => 'File Category has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/file-category')->with(compact('res'));
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
            $fileCategory = FileCategory::findOrFail($id);
            $fileCategory->delete();
            $res = [
            'title' => 'File Category Deleted ',
            'message' => 'File Category has been successfully Deleted ',
            'icon' => 'success'
        ];
        return redirect('/file-category')->with(compact('res'));
        }
        catch(\Illuminate\Database\QueryException $e){
             if($e->getCode() == "23000"){
                $res = [
                    'title' => 'File Category Creation Fail',
                    'message' => 'File Category is being Used',
                    'icon' => 'error'
                ];
                return redirect('/file-category')->with(compact('res'));
            }
        }
    }
}
