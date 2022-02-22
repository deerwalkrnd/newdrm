<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MissedPunchOutController;
class CreateMissedPunchOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:missedPunchOut';

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
        $missed_punch_out = new MissedPunchOutController;
        $missed_punch_out->index();
        return true;
    }
}
