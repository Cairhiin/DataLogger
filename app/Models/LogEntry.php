<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    use HasFactory;

    protected $table = 'logged_messages';

    protected $fillable = [
        'event_name',
        'model',
        'original_data',
        'new_data',
        'user_email',
        'route',
        'ip_address',
    ];

    protected $decryptable = [
        'original_data',
        'new_data',
        'user_email',
    ];

    protected $hidden = [
        'user_email'
    ];
}
