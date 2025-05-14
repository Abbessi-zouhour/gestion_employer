<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'employer_id',
        'amount',
        'launch_date',
        'done_date',
        'status',
        'month',
        'year',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

}
