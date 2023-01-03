<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPages extends Model
{
    public function CmsPagesContent(){
        return $this->hasMany(CmsPagesContent::class, 'db_cms_pages_id', 'id' );
    }

}
