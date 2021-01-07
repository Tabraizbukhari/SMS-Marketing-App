<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ResellerCustomer;
use Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $file_prefix_path = 'public/file';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sms',
        'price',
        'type',
        'api_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getUserData()
    {
        return $this->hasOne(UsersData::class);
    }

    public function getUserSmsApi()
    {
        return $this->hasOne(SmsApi::class);
    }

    public function getResellerMasking()
    {
        return $this->belongsToMany(Masking::class,'user_maskings','user_id','masking_id');
    }

    public function getFormatedCreatedAt()
    {
        return date('d-M-y h-m-s', $this->created_at);
    }

    public function getResellerCustomer()
    {
      return  $this->belongsToMany(User::class,'reseller_customers','customer_id','user_id');
    }

    public function getBulkSmsExcelPath()
    {
        return $this->file_prefix_path.'/'.Auth::user()->id;
    }
}
