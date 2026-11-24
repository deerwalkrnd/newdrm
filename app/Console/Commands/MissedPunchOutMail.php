<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MailHelper;

class MissedPunchOutMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missedPunchOut:mail';

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
        $result = MailHelper::sendMissedPunchOutMail();
        $line = sprintf(
            'missedPunchOut:mail flagged=%d sent=%d skipped=%d failed=%d',
            $result['flagged'],
            $result['sent'],
            $result['skipped'],
            $result['failed']
        );

        if ($result['failed'] > 0) {
            $this->error($line);
            return 1;
        }

        $this->info($line);
        return 0;
    }
}
