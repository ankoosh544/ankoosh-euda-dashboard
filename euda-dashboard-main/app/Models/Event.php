<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'plantId',
        'OOS',
        'DFD',
        'STR',
        'IUPS',
        'CLS',
        'BAT',
        'AC',
    ];
}
