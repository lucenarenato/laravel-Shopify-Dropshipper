<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function LineItems(){
        return $this->hasMany(LineItem::class, 'db_order_id', 'id' );
    }
}
