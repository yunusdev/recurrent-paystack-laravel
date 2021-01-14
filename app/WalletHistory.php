<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    //
    protected $appends = ['formatted_date'];

    protected $fillable = [
        'currency', 'amount', 'status', 'user_id', 'reference',
    ];

    public function getFormattedDateAttribute(){

        return $this->created_at->diffForHumans();

    }
}
