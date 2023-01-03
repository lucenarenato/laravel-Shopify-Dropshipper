<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cmsFaq extends Model
{

    public function cmsFaqContent(){
        return $this->hasMany(cmsFaqContent::class, 'db_cms_faq_id', 'id' );
    }


}
