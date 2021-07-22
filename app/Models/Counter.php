<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'counter_type'
    ];

    protected $primaryKey = "user_id";

    public $timestamps = false;
}
