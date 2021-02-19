<?php

namespace App\Models;

use App\DatabaseNotification;
use Illuminate\Notifications\Notifiable as BaseNotifiable;

trait Notifiable
{
    use BaseNotifiable;

    /**
     * Get the entity's notifications.
     */
    // public function notifications()
    // {
    //     return $this->morphMany(DatabaseNotification::class, 'notifiable')
    //                         ->orderBy('created_at', 'desc');
    // }
}