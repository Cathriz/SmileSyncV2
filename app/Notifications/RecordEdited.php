<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordEdited extends Notification
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
            'title' => 'Record Updated ✏️',
            // 🎯 FIX: Changed ->patient_name to ->patient
            'message' => "The record for patient **{$this->record->patient}** (ID: {$this->record->id}) was updated by **{$this->userName}**.",
            'type' => 'record_edited',
            'id' => $this->record->id,
        ];
    }
}