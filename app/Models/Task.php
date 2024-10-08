<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'priority', 'status', 'start_date', 'end_date', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
