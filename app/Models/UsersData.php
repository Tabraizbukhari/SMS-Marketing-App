<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersData extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'has_sms',
        'price_per_sms',
        'Invoice_charges',
        'logo_img',
        'login_url',
    ];
}
