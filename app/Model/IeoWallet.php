<?php

namespace App\Model;

use App\User;
use App\Model\Coin;
use Illuminate\Database\Eloquent\Model;
// DC
class IeoWallet extends Model
{
    protected $table = 'ieo_wallet';
    protected $fillable = [
        'user_id',
        'coin_id',
        'coin_type',
        'balance',
        'created_at',
        'updated_at'
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
