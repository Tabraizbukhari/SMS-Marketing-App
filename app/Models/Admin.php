<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guard = 'admin';
    public $file_prefix_path = 'public/file';
  
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'email_verified_at',
        'username',
        'has_sms',
        'price_per_sms',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function adminApi()
    {
        return $this->hasOne(AdminSmsApi::class);
    }

    public function getBulkSmsExcelPath()
    {
        return $this->file_prefix_path.'/'.Auth::user()->id;
    }

    public function getFullNameAttribute($value)
    {
    	return $this->first_name.' '.$this->last_name;
    }

    public function getTranscation()
    {
        return $this->hasMany(Transaction::class,'id', 'admin_id');
    }

}
