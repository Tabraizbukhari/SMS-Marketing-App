<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Auth;

class CustomerRegisterNotification extends Notification
{
    use Queueable;

    public $user_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user_id = Auth::id();
    }

    public function getUserId()
    {
        return $this->user_id;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Welcome To Taskshift',
            'message' => 'Your email has been verified successfully !',
            'user_id' => $this->user_id,
        ];
    }
}
