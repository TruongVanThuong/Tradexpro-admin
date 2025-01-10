<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\IeoWallet;
use App\User;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateWinRate($ieoId, $userId)
    {
        $isIeoEnded = $this->isIeoEnded();

        if ($isIeoEnded) {
            $userRegistered = UserRegisteredIeo::where('user_id', $userId)
                ->where('ieo_id', $ieoId)
                ->first();

            if ($userRegistered && $userRegistered->rating_win) {
                return $userRegistered->rating_win . '%';
            } else {
                return $this->ieo->max_rate . '%';
            }
        }

        return 'Calculating';
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
