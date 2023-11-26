<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'assigned_to',
        'plant_id',
        'name',
        'state',
        'city',
        'cap',
        'address',
        'country_code',
        'schedule_date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'plant_id', 'plantId');
    }

    public function getCompleteAddress() {
        return $this->address.", ".$this->cap.", ".$this->city.", ".$this->state.", ".$this->country_code;
    }
    public function scopeOwnedByUser($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }
}