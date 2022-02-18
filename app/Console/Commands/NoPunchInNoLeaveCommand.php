<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NoPunchInNoLeave;
use App\Models\Holiday;
use App\Models\Employee;
use App\Http\Controllers\NoPunchInNoLeaveController;


class NoPunchInNoLeaveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'no:punchInLeave';

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
        $noPunchInNoLeave = new NoPunchInNoLeaveController;
        $create = $noPunchInNoLeave->create();
        $remove = $noPunchInNoLeave->remove();
        return true;
    }
}
