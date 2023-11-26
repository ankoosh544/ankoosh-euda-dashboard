<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'owner_id',
        'job_id',
        'plantId',
        'thing_type',
        'status',
        'version_number',
    ];
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plantId', 'plant_id');
    }

    public function scopeForAuthUser($query)
    {
        return $query->where('owner_id', Auth::id());
        
    }
   
}
