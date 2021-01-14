<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['card'];


    public function card(){

        return $this->hasOne(Card::class);

    }

    public function walletHistories(){

        return $this->hasMany(WalletHistory::class)->latest();

    }

    public static function topUpWalletBalance($newBalance){

        return self::where('id', auth()->id())->update([
            'wallet_balance' => $newBalance
        ]);

    }
}
