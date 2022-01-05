<?php

namespace App\Http\Controllers;
use App\Mail\SendMail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    // private $hr = 'satyadeep.neupane@deerwalk.edu.np';
    public $details = [];

    public function sendMail($to, $name, $subject, $message, $cc = false, $bcc = false)
    {
        $details = [
            'to' => $to,
            'name'=>$name,
            'subject' =>$subject,
            'body' => $message,
            'bcc' =>$bcc,
            'cc' => $cc,
        ];
     
        \Mail::send('admin.emails.sendMail',$details, function($message) use ($details) {
            $message->to($details['to']);
            $message->subject($details['subject']);
            $message->from('DRM System');    //from HR
            if($details['cc'])
                $message->cc($details['cc']);
            if($details['bcc'])
                $message->bcc($details['bcc']);
        });

        return true;
    }

    public function punchOutMail(){
        //receive employee_id
        //fetch_manager_email 

        //employee by mnager_id to hr , cc manager, bcc employee
        
        $to = 'deenasitikhu123@gmail.com';
        $cc = ['satyadeep.neupane@deerwalk.edu.np'];
        $bcc = ['deenasitikhu@gmail.com'];
        $name = 'deena';
        $message = 'still not punch out';
        $regards ='HR';
        $subject = 'Punch In Subject';
        // $receiver_mail = ['deenasitikhu@gmail.com','apurba.thapaliya@deerwalk.edu.np'];
        return $this->sendMail($to, $name, $subject, $message, $cc, $bcc);
    }
}
