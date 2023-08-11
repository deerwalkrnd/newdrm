<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\TestJob;
class SendMailJobController extends Controller
{
    public function sendMail()
    {   
        // $details['to'] = 'harsukh21@gmail.com';
        // $details['name'] = 'Receiver Name';
        // $details['subject'] = 'Hello Laravelcode';
        // $details['message'] = 'Here goes all message body.';
        // $name = "Deena Sitikhu";
        // dd(TestJob::dispatch($name)->delay(now()->addSeconds(5)));
        $done =TestJob::dispatchNow();
        if($done ==true)
            return response('Email sent successfully!');
        else
            return response('Email not sent');
    }
}
