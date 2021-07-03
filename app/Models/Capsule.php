<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capsule extends Model
{
    use HasFactory;

    protected $fillable = [
        'capsules_count', 'user_id', 'date'
    ];

    public $timestamps = false;

    // Get user that has the capsules count
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
