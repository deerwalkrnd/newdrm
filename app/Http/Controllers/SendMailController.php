<?php

namespace App\Http\Controllers;
use App\Mail\SendMail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    public function sendMail($name, $title,$subject, $message, $regards, $receiver_mail) {
        $details = [
            'name'=>$name,
            'title' => $title,
            'message' => $message,
            'regards' =>$regards,
            'receiver_mail' =>$receiver_mail,
            'subject' =>$subject
        ];
        if(count($details['receiver_mail']) > 1){
            \Mail::bcc($receiver_mail)->send(new SendMail($details));
        }else{
            \Mail::to($receiver_mail)->send(new SendMail($details));
        }
    
        // dd("Email is Sent.");
    }

    public function punchInMail(){
        $name = 'deena';
        $title = 'Punch In Mail';
        $message = 'Why late punch in';
        $regards ='Deena';
        $subject = 'Punch In Subject';
        $receiver_mail = ['deenasitikhu@gmail.com','apurba.thapaliya@deerwalk.edu.np'];
        return $this->sendMail($name, $title,$subject, $message, $regards, $receiver_mail);
    }
}
