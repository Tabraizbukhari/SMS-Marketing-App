<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'sender',
        'receiver',
        'body',
        'recvtime',
        'message_id',
        'operator',
    ];
}
