<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public $timestamps = false; // ✅ Disable automatic timestamps

    protected $fillable = [
        'user_id',
        'patient',
        'doctor',
        'date',
        'time',
        'status',
        'type',
        'notes',
    ];
}
