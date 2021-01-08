<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'transaction_id',
        'user_id',
        'title',
        'description',
        'amount',
        'type',
        'data',
    ];
    
}
