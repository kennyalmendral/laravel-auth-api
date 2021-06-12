<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

use App\Events\UserRegisteredEvent;
use App\Mailables\User\UserRegisteredMailable;

class SendUserVerificationListener
{
    public function __construct()
    {
        // Nothing to do here...
    }

    public function handle(UserRegisteredEvent $event)
    {
        $user = $event->user;

        Mail::to($user->email)->queue(new UserRegisteredMailable($user));
    }
}