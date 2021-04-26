<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsApi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_url',
        'api_username',
        'api_password',
    ];

}
