<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPairOperation extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'coin_pair_id',
        'bot_operation',
        'upper_threshold',
        'lower_threshold',
        'running_time_start',
        'running_time_close',
    ];

    public function coinPair()
    {
        return $this->belongsTo(CoinPair::class);
    }

    // /**
    //  * @param int $coinPairId
    //  * @param string $runningTimeStart
    //  * @param string $runningTimeClose
    //  * @return array
    //  */
    // public static function getPercentages($coinPairId, $runningTimeStart, $runningTimeClose)
    // {
    //     $query = self::where('coin_pair_id', $coinPairId)
    //         ->where(function ($q) use ($runningTimeStart, $runningTimeClose) {
    //             $q->where('running_time_start', '<=', $runningTimeClose)
    //             ->where('running_time_close', '>=', $runningTimeStart);
    //         });

    //     $maxPercent = $query->max('upper_threshold');
    //     $minPercent = $query->min('lower_threshold');

    //     return [
    //         'maxPercent' => $maxPercent,
    //         'minPercent' => $minPercent,
    //     ];
    // }

}
