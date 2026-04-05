<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couirer extends Model
{
    use HasFactory;

    protected $table = "courier";
    protected $fillable = [ 'paperflyKey', 'Username', 'Password'];

}
