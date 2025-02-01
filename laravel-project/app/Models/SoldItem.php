<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoldItem extends Model
{
    use HasFactory;

    protected $table = 'sold_item';

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_status',
        'stripe_session_id',
        'payment_intent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
