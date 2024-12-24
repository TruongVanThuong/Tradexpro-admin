<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTranferIeoCoin extends Model
{
    use HasFactory;
    protected $table = 'log_tranfer_ieo_coin';

    protected $primaryKey = 'id_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ieo_id',
        'wallet_coin_id',
        'balance',
        'note',
        'create_at',
    ];

    protected $casts = [
        'balance' => 'float',
        'create_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ieo()
    {
        return $this->belongsTo(ieoModel::class, 'ieo_id');
    }

    public function walletCoin()
    {
        return $this->belongsTo(Wallet::class, 'wallet_coin_id');
    }
}
