<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Osiset\ShopifyApp\Storage\Models\Charge;

class UserObserver
{
    public function saving(User $user)
    {
        $old_plan = $user->getOriginal('plan_id');
        $new_plan = $user->plan_id;
       // logger('Old plan :: ' .$old_plan);
       // logger('new plan :: ' .$new_plan);
        if( $old_plan != $new_plan && $old_plan != null ){
          //  logger('user observer');
            $shop = Auth::user();

            if( $shop ){
                 $charge = Charge::where('status', 'ACTIVE')->where('user_id', $shop->id)->where('plan_id', 1)->first();

                if( $charge ){
                    $charge->status = 'CANCELLED';
                    $charge->cancelled_on = date('Y-m-d H:i:s');
                    $charge->save();
                }
            }
        }
    }
}
