<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLastLoginAt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $is_logged_in_today = date('Y-m-d',strtotime($event->user->last_login)) == date('Y-m-d')? 1 : 0;
        $event->user->is_logged_in_today = $is_logged_in_today;     
        $event->user->last_login = date('Y-m-d H:i:s');
        $event->user->save();
    }
}
