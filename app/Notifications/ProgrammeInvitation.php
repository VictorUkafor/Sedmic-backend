<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProgrammeInvitation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->user = $mail['user'];
        $this->programme = $mail['programme'];
        $this->church = $mail['church'];
        $this->organizer = User::find($this->programme->created_by);

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
        $emailContent = '';

        $contact = $this->organizer->phone ? 
        $this->organizer->phone : $this->organizer->email;

        if(!$this->programme->message){
            $emailContent = 'You\'ve been invited to attend '.$this->programme->title.
            ' ('.$this->church->name_of_church.') taking place at '.
            $this->programme->venue.' on '.$this->programme->date.' by '.
            $this->programme->time_starting.'. You can reachout to '.
            $this->organizer->full_name.', '.$contact.' for more info.';
        }

        $emailContent = $this->programme->message;

        return (new MailMessage)
            ->subject('Invitation to '.$this->programme->title.' ('.$this->church->name_of_church.')')
            ->greeting('Hi '.$this->user->first_name.' '.$this->user->last_name)
            ->line($emailContent)
            ->line('We\'ll be expecting you. God bless you!');
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
