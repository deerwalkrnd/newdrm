<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TimeRequest;
use App\Models\Time;
use App\Models\MailControl;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\TimerChangeNotificationMail;

class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $times =  Time::select('id','name','time')->get();
        return view('admin.time.index')->with(compact('times'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TimeRequest $request,$id)
    {
        $input = $request->validated();
        $time = Time::findOrFail($id);
        $time->update($input);
        $send_mail = MailControl::select('send_mail')->where('name','Timing Change')->first()->send_mail;
        // dd($send_mail,env('GP_EMAIL'));

        if($send_mail)
        {
            // MailHelper::timeChangeMail($time);
            Mail::to(\Auth::user()->employee->email)
                // ->cc(env('GP_EMAIL'))
                ->cc('deena.sitikhu@deerwalk.edu.np')
                ->send(new TimerChangeNotificationMail($time));
        }
        $res = [
            'title' => 'Time Updated',
            'message' => 'Time has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/time')->with(compact('res'));
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
