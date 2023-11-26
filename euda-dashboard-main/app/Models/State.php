<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $fillable = [
        'plantId',
        'OOS',
        'OSN',
        'FCN',
        'CAM',
        'STR',
        'DFN',
        'IUPS',
        'rides',
        'sequence',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'plantId', 'plantId');
    }

}
