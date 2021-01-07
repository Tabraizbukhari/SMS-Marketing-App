<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMessage extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'message_id',
        'campaign_id',
    ];
}
