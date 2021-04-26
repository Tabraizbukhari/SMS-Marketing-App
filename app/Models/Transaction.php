<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'transaction_id',
        'user_id',
        'admin_id',
        'title',
        'description',
        'amount',
        'type',
        'data',
    ];

    public function setTransactionIdAttribute($value)
    {
    	$this->attributes['transaction_id'] = strtoupper('TS'.rand(0,100000).Str::random(2).Str::random(1));
    }
    
}
