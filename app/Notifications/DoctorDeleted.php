<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DoctorDeleted extends Notification
{
    use Queueable;

    protected $doctorName; // Since the model is deleted, pass the name only
    protected $userName;

    public function __construct($doctorName, $userName)
    {
        $this->doctorName = $doctorName;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Doctor Removed 🛑',
            'message' => "The record for Dr. **{$this->doctorName}** was permanently removed by **{$this->userName}**.",
            'type' => 'doctor_deleted',
        ];
    }
}