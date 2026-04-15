<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Queue Channels (Public)
|--------------------------------------------------------------------------
| These are public channels — no auth required.
| TV displays, ticket status pages, and join pages listen on these.
| Public channels in Laravel are defined as Channel (not PrivateChannel),
| which are set in the event's broadcastOn() method.
| No Broadcast::channel() registration is needed for public channels.
|--------------------------------------------------------------------------
*/
