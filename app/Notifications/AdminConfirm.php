<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminConfirm extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $admin)
    {
        $this->user = $user;
        $this->admin = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/api/v1/auth/signup/confirm/'.$notifiable->activation_token);

        return (new MailMessage)
            ->subject('Please confirm your account')
            ->greeting('Dear Beloved!')
            ->line($this->admin->full_name.' has created an account for you at Sedmic. 
            We would want you to confirm your account to completed the process.')
            ->line('Your Username: '.$this->user->username)
            ->action('Click here to confirm your account', url($url))
            ->line('Thank you so much for using Sedmic. God bless you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
