<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProgrammeCancel extends Notification
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
            $emailContent = 'This is to notify you that the programme '.
            $this->programme->title.' ('.$this->church->name_of_church.') has been cancelled. '.
            'You may reachout to '.$this->organizer->full_name.', '.$contact.
            ' for more info. Sorry for any inconviences.';
        }

        $emailContent = $this->programme->message;

        return (new MailMessage)
        ->subject('Concerning '.$this->programme->title.' ('.$this->church->name_of_church.')')
        ->greeting('Hi '.$this->user->first_name.' '.$this->user->last_name)
        ->line($emailContent)
        ->line('Sorry for any inconviences. God bless you!');
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
