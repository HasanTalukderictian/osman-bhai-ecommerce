<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Headers extends Model
{
    use HasFactory;

    protected $table ="header";
    protected $fillable = [
        'Companyname', 'image'
    ];


}
