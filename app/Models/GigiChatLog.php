<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigiChatLog extends Model
{
    protected $fillable = [
        'message_sanitized',
        'reply',
        'reply_level',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
