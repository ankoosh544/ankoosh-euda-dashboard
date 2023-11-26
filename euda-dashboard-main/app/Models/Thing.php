<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Thing extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'plantId',
        'user_id',
        'thing_name',
        'thing_type',
        'file_name',
        'file_path',
     
    ];
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plantId', 'plant_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFilePathAttribute()
    {
        // Assuming you have a 'file_path' column in the database
        // You can customize this logic based on how you store file paths

        // Here, we're assuming 'file_path' contains the filename, and it's in the 'public' disk
        return Storage::disk('public')->url($this->attributes['file_path']);
    }

    
}
