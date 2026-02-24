<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reivew extends Model
{
    use HasFactory;

    protected $table ="reviews";

     protected $fillable = [
        'name',
        'designation',
        'rating',
        'comment',
        'image',
    ];
}
