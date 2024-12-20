<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\IeoWallet;

class UserRegisteredIeo extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'user_registered_ieo';
    public $timestamps = false;
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ieo_id',
        'status',
        'quantity',
        'rating_win'
    ];

    public function ieo()
    {
        return $this->belongsTo(ieoModel::class, 'ieo_id', 'id');
    }

    public function calculateWinRate($ieoId, $userId)
    {
        $isIeoEnded = $this->isIeoEnded();

        if ($isIeoEnded) {
            $ieoWallet = IeoWallet::where('user_id', $userId)
                ->where('coin_id', $ieoId)
                ->first();
            if ($ieoWallet) {
                return $this->rating_win . '%';
            } else {
                return $this->ieo->max_rate . '%';
            }
        }

        return 'Đang tính toán';
    }

    public function isIeoEnded()
    {
        return now()->greaterThan($this->ieo->end_date);
    }

    public function getLockedPercentage()
    {
        return $this->isIeoEnded() ? 0 : 100;
    }

    public function getUnlockedPercentage()
    {
        return $this->isIeoEnded() ? 100 : 0;
    }
}
