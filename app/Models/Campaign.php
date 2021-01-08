<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'file_url',
        'file_name',
        'size',
        'campaign_date',
        'status',
        'price',
        'type', 
    ];
    
    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
