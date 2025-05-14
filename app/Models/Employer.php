<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    use HasFactory;
    protected $guarded = [''];




    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


}
