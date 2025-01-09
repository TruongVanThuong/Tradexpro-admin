<?php

namespace App\Model;

use App\User;
use App\Model\Coin;
use Illuminate\Database\Eloquent\Model;
// DC
class IeoWallet extends Model
{
    protected $table = 'ieo_wallet';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'coin_id',
        'coin_type',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class,'coin_id');
    }
}
