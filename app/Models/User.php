<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ResellerCustomer;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $file_prefix_path = 'public/file';
    public $logo_prefix_path = 'public/logos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'api_token',
        'phone_number',
        'type',
        'register_as',
        'reference_id',
        'is_blocked',
        'shortcode_monthly_rental_charges',
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

    public function getFormatedCreatedAtAttribute()
    {
        return date('d-m-Y H:m a', strtotime($this->created_at));
    }

    public function setUserNameAttribute($value)
    {
        $this->attributes['username'] = strtolower('sync_' . $value . rand(99, 1999));
    }

    public function getFullNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function UserData()
    {
        return $this->hasOne(UsersData::class);
    }

    public function getCurrentAccountBallance()
    {
        $ct = $this->transactions->where('type', 'credit')->sum('amount');
        $dt = $this->transactions->where('type', 'debit')->sum('amount');
        return round($ct - $dt, 2);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAllMessages()
    {
        return $this->hasMany(Message::class);
    }

    public function getUserSmsApi()
    {
        return $this->hasOne(SmsApi::class, 'user_id');
    }

    public function getResellerMasking()
    {
        return $this->belongsToMany(Masking::class, 'user_maskings', 'user_id', 'masking_id');
    }

    public function getFormatedCreatedAt()
    {
        return date('d-M-y h-m-s', $this->created_at);
    }

    public function getCustomerAddBy()
    {
        return  $this->belongsToMany(User::class, 'reseller_customers', 'customer_id', 'user_id');
    }

    public function getResellerCustomer()
    {
        return  $this->belongsToMany(User::class, 'reseller_customers', 'user_Id', 'customer_id');
    }


    public function getResellerCustomerProfit()
    {
        return  $this->belongsToMany(User::class, 'reseller_customers', 'user_Id', 'customer_id')->withCount(['getAllMessages AS myprofit' => function ($query) {
            $query->select(DB::raw('SUM(price) as profit'));
        }]);
    }

    public function IncomingApi()
    {
        return $this->hasOne(IncomingApi::class);
    }

    public function getBulkSmsExcelPath()
    {
        return $this->file_prefix_path . '/' . Auth::user()->id;
    }


    public function getLogoUrlPath()
    {
        return $this->logo_prefix_path . '/' . Auth::user()->id;
    }
}
