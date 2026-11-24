<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\MissedPunchOutHelper;

class MissedPunchOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punchOut:missed';

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
        $result = MissedPunchOutHelper::createMissedPunchOut();
        $line = sprintf(
            'punchOut:missed date=%s open=%d flagged=%d created=%d skipped=%d failed=%d',
            $result['date'],
            $result['open'],
            $result['flagged'],
            $result['created'],
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