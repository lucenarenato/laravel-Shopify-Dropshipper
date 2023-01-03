<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['collection_id','user_id','shopify_id','type','locale','source','source_url','description','amazon_associate_link','show_prices'];


    protected $casts = [

        'collection_id' => 'array',

    ];
}
