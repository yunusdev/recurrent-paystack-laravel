<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    //
    protected $fillable = [
        'user_id', 'reference', 'authorization_code', 'bank', 'card_type', 'country', 'last4', 'exp_year', 'exp_month'
    ];

    public function user(){

        return $this->belongsTo(User::class);

    }

    public static function deleteUserCard(){

        return self::where('user_id', auth()->id())->delete();

    }

}
