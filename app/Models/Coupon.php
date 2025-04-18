<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];
    
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
    use HasFactory;
}
