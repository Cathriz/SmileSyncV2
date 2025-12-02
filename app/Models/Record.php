<?php

// app/Models/Record.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'records'; 
    protected $fillable = [
        'user_id',
        'patient',
        'doctor',
        'type',
        'date',
        'time',
        'status', 
        'notes',
        'document',
        'document_path',
        'created_at', // <-- Include these if your table has them
        'updated_at', // <-- Include these if your table has them
    ];
}