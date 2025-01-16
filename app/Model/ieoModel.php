<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ieoModel extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'ieo';
    protected $dates = ['start_date', 'end_date'];
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'symbol',
        'ieo_icon',
        'total_supply',
        'max_rate',
        'start_date',
        'end_date',
        'test',
    ];

    protected $casts = [
        'value' => 'float',
        'total_supply' => 'integer',
        'max_rate' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $timestamps = true;

    public function getFormattedValueAttribute()
    {
        return number_format($this->value, 2) . ' USDT';
    }

    /**
     * relationship with table user_registered_ieo.
     * Get all user records registered for this IEO.
     */
    public function userRegisteredIeo()
    {
        return $this->hasMany(UserRegisteredIeo::class, 'ieo_id', 'id');
    }

    /**
     * Returns the total number of subscribers (count) of the IEO.
     */
    public function getRegisteredQuantity()
    {
        return $this->userRegisteredIeo()->sum('quantity');
    }

    public function getTotalRegisteredUsdt()
    {
        return $this->userRegisteredIeo()->sum('total_usdt');
    }

    public function getProgressPercentage()
    {
        $registeredQuantity = $this->getRegisteredQuantity();
        $totalSupply = $this->total_supply;
        return ($registeredQuantity / $totalSupply) * 100;
    }

    public function getTimeRemaining()
    {
        $now = Carbon::now();
        $endDate = Carbon::parse($this->end_date);

        if ($now->greaterThanOrEqualTo($endDate)) {
            return 'Ended';
        }

        // Calculate remaining days
        $diff = $now->diff($endDate);

        return $diff->days . ' days ' . $diff->h . ' hours ';
    }

    public function getStatus()
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        if ($now->lessThan($startDate)) {
            return 'Coming soon';
        }

        if ($now->greaterThan($endDate)) {
            return 'Ended';
        }

        return 'In progress';
    }
}
