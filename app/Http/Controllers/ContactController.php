<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Contact;
use App\Http\Requests\ContactRequest;


class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::select('id', 'name', 'number')->get();
        return view('admin.contact.index')->with(compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $res = [
            'title' => 'Contact Created ',
            'message' => 'Contact has been successfully Created ',
            'icon' => 'success'
        ];
        return view('admin.contact.create')->with(compact('res'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        Contact::create($request->validated());
        return redirect('/contact');
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
        $contact = Contact::select('id', 'name', 'number')->findOrFail($id);
        return view('admin.contact.edit')->with(compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request, $id)
    {
        $contact = Contact::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $contact->update($input);
        $res = [
            'title' => 'Contact Updated ',
            'message' => 'Contact has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/contact')->with(compact('res'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        $res = [
            'title' => 'Contact Deleted',
            'message' => 'Contact has been successfully Deleted',
            'icon' => 'success'
        ];
        return redirect('/contact')->with(compact('res'));
    }
}
