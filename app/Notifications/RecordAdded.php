<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordAdded extends Notification
{
    use Queueable;

    protected $record;
    protected $userName;

    public function __construct($record, $userName)
    {
        $this->record = $record;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Record Created 📄',
            'message' => "A new record for patient **{$this->record->patient_name}** (ID: {$this->record->id}) was added by **{$this->userName}**.",
            'type' => 'record_added',
            'id' => $this->record->id,
        ];
    }
}