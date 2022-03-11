<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailControl;
use App\Http\Requests\MailControlRequest;

class MailControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mails = MailControl::select('id','name','send_mail')->get();
        // dd($mails);
        return view('admin.mailSetting.index')->with(compact('mails'));
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MailControlRequest $request,$id)
    {
        $input = $request->validated();
        $send_mail = MailControl::findOrFail($id);
        $send_mail->update($input);
        $res = [
            'title' => 'Send Mail Updated',
            'message' => 'Send Mail has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/mail')->with(compact('res'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
