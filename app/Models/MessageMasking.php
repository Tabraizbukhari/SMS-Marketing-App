<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageMasking extends Model
{
    use HasFactory;
    protected $fillable = [
        'masking_id',
        'message_id',
    ];
}
