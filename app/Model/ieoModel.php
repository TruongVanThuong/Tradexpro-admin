<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'total_supply',
        'max_rate',
        'start_date',
        'end_date',
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
}
