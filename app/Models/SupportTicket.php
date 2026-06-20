<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_login_id',
        'ticket_no',
        'subject',
        'category',
        'priority',
        'message',
        'status'
    ];

    /**
     * Customer Login Relation
     */
    public function customer()
    {
        return $this->belongsTo(
            CustomerLogin::class,
            'customer_login_id'
        );
    }
}
