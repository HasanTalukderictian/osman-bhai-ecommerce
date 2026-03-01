<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = "store_tables";
    protected $fillable = [
        'full_name', 'phone_number', 'district_name', 'thana_name', 'address', 'label'
    ];
}
