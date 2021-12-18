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
        $fileCategories = FileCategory::select('id', 'category_name','status')->paginate(10);
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
        return redirect('/file-category');
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
        return redirect('/file-category');
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
            return redirect('/file-category');
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
