<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\MailHelper;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;


class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // protected $name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->name = $name;
        // dd($this->name);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd("erer");
        // $name = "deena";
        MailHelper::testMail2();
        return true;
    //    Mail::to('deena.sitikhu@deerwalk.edu.np')
    //             ->cc(['satyadeep.neupane@deerwalk.edu.np','samil.shrestha@deerwalk.edu.np'])
    //             ->queue(new SendMail($name));
    //     return true;
    }
}
