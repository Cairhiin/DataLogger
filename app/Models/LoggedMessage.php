<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoggedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'original_data',
        'new_data',
        'user_email',
        'route',
        'ip_address',
    ];

    protected $decryptable = [
        'original_data',
        'new_data',
        'user_data',
    ];
}
