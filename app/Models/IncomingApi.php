<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingApi extends Model
{
    use HasFactory;
    protected $fillable = [
        'prefix',
        'customer_api',
        'user_id',
    ];
}
