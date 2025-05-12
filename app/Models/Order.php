<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'is_live_order',
        'notes',
        'total_amount',
        'payment_date',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'is_live_order' => 'boolean',
        'total_amount' => 'decimal:2',
    ];
    
    protected $with = ['items']; // Charger automatiquement les items
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
