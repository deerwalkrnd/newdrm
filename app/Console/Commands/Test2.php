<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\Mail;

class Test2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        MailHelper::testMail2();
        return true;
    }
}
