<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DoctorAdded extends Notification
{
    use Queueable;

    protected $doctor;
    protected $userName;

    public function __construct($doctor, $userName)
    {
        $this->doctor = $doctor;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Doctor Added 👨‍⚕️',
            'message' => "Dr. **{$this->doctor->name}** ({$this->doctor->specialization}) was added to the system by **{$this->userName}**.",
            'type' => 'doctor_added',
            'id' => $this->doctor->id,
        ];
    }
}