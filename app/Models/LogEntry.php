<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogEntry extends Model
{
    use HasFactory;

    const APP_ID = 'Unspecified';
    const EVENT_TYPE = 'create';
    const ROUTE = 'dashboard';

    protected $table = 'logged_messages';

    protected $fillable = [
        'event_type',
        'model',
        'original_data',
        'new_data',
        'app_id',
        'route',
        'ip_address',
        'date',
        'user_id',
        'remote_user_id'
    ];

    protected $decryptable = [
        'original_data',
        'new_data',
        'ip_address'
    ];

    protected $attributes = [
        'event_type' => self::EVENT_TYPE,
        'app_id' => self::APP_ID,
        'route' => self::ROUTE
    ];

    /**
     * Get the user that owns the entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
