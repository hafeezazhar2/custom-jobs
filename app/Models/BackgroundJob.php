<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundJob extends Model
{
    
    protected $fillable = [
        'class',
        'method',
        'parameters',
        'status',
        'priority',
        'retry_attempts',
        'max_retries',
        'scheduled_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'scheduled_at' => 'datetime',
    ];
  
}
