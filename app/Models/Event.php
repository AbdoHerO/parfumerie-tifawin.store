<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        
        'user_name',
        'user_type',
        'user_email',

        'product_name',
        'product_image',

        'order_user_name',
        'order_price_total',
        'type_order_event',
        'status_order_event',

        'date_event',
        'type_event',
    ];

    // CREATE TABLE events (
    //     id int NOT NULL AUTO_INCREMENT,
    //     user_id int NOT NULL,
    //     product_id int NULL,
    //     order_id int NULL,
    //     user_name varchar(100) NOT NULL,
    //     user_type varchar(50) NOT NULL,
    //     user_email varchar(150) NULL,
    //     product_name varchar(250) NULL,
    //     product_image varchar(250) NULL,

    //     order_user_name varchar(100) NULL,
    //     order_price_total double(20,2) NULL,
    //     type_order_event varchar(50) NULL,
    //     status_order_event varchar(50) NULL,
        
    //     date_event TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //     type_event varchar(50) NOT NULL,
    //     PRIMARY KEY (id)
    // )

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function order()
    // {
    //     return $this->belongsTo(Order::class);
    // }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
}
