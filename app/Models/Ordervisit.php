<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordervisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'adresse',
        'id_product',
        'quantite',
        'date',
        'payment_status',
        'delivery_status',
        'shipping_type',
        'name_product',
        'price_product',
        'variant'
    ];
}
