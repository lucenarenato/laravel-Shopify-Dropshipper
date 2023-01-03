<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyShop extends Model
{

    use SoftDeletes;

    protected $table = 'shopify_shops';

        protected $fillable = [
        'shop_id',
        'name',
        'email',
        'domain',
        'province',
        'country',
        'address1',
        'zip',
        'city',
        'phone',
        'primary_locale',
        'country_code',
        'country_name',
        'currency',
        'shop_owner',
        'weight_unit',
        'province_code',
        'plan_name',
        'user_id',
        'deleted_at'
    ];
    protected $dates = ['deleted_at'];

}
