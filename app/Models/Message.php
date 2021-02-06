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
        'campaign_id',
        'type',
        'api_type',
        'price',
    ];

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getCampaign()
    {
        return $this->belongsToMany(Campaign::class,'campaign_messages','message_id','campaign_id');
    }
    
}
