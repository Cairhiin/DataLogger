<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that set default values.
     *
     * @var array<int, string>
     */
    protected $attributes = [
        'name' => 'Member'
    ];

    /**
     * Get the user that owns the role.
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
