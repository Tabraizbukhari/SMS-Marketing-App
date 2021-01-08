<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'masking_id',
        'contact_number',
        'message',
        'message_length',
        'status',
        'send_date',
    ];

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
