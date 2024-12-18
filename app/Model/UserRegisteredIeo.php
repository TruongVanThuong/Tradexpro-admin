<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function calculateWinRate()
    {
        $isIeoEnded = $this->isIeoEnded();

        if ($isIeoEnded) {
            return $this->rating_win != 0 || $this->rating_win != null ? $this->rating_win . '%' : $this->ieo->max_rate. '%';
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
