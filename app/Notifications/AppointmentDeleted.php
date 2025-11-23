<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AppointmentDeleted extends Notification
{
    use Queueable;

    protected $appointment;
    protected $userName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($appointment, $userName)
    {
        $this->appointment = $appointment;
        $this->userName = $userName;
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Appointment Deleted 🗑️',
            'message' => "The appointment for **{$this->appointment->patient}** with Dr. **{$this->appointment->doctor}** on **{$this->appointment->date}** was cancelled by **{$this->userName}**.",
            'type' => 'appointment_deleted',
            'id' => $this->appointment->id,
        ];
    }
}