<?php

namespace App\Http\Controllers;

use App\Models\Counters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Show Settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user();
        $store = $shop->name;
        $settings = DB::table('settings')->where('user_id', $shop->id)->first();

        if( !$settings ){
            DB::table('settings') ->insertOrIgnore([ 'user_id' => $shop->id]);
        }
        // Load user settings
        $settings = DB::table('users')->join('settings', 'users.id', '=', 'settings.user_id')->select('users.plan_id', 'settings.*')->where('users.id', $shop->id)->first();


        return view('settings', compact('store'))->with(['settings' => $settings]);
    }

    /**
     * Select new plan
     *
     * @return \Illuminate\Http\Response
     */
    public function setPlan(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $planID = intval($request->post('curr_plan_id'));
        $newPlan = intval($request->post('new_plan_id'));

        $charge = DB::table('charges')->where('user_id', $shop->id)->where('status', 'ACTIVE')->orderBy('created_at', 'desc')->first();
        $counter = Counters::where('user_id', $shop->id)->where('status', 'active')->where('charge_id', $charge->id)->first();
        $plan = DB::table('plans')->where('id', $newPlan)->first();

        $downgrade_err_msg = 'For this billing cycle ['.  $counter->start_date .' - '. $counter->end_date .'] you have
                                executed more imports than the ones allowed by the
                                ['. $plan->name .' plan]. If You want to downgrade you
                                have to wait until the date of Billing End cycle.';

        if ( ($planID < 1 || $planID > 5) || ($newPlan < 1 || $newPlan > 5)) {
            $errors[] = 'Invalid Plan ID: ' . $planID . '.';
        }else if( $planID >= 2 && $newPlan == 1){   // for downgrade plan
            $errors[] = 'you can not downgrade your plan with free plan.';
        }else if( $planID == 3 && $newPlan == 2 ){   // check counter to downgrade plan
            if( $counter->regular_product_count > 15 ){
                $errors[] = $downgrade_err_msg;
            }
        }else if( $planID == 4 && $newPlan == 2 ){
            if( $counter->regular_product_count > 15 ){
                $errors[] = $downgrade_err_msg;
            }
        }else if( $planID == 4 && $newPlan == 3 ){
            if( $counter->regular_product_count > 100 ){
                $errors[] = $downgrade_err_msg;
            }
        }else if( $planID == 5 && $newPlan == 2 ){
            if( $counter->regular_product_count > 15 ){
                $errors[] = $downgrade_err_msg;
            }
        }else if( $planID == 5 && $newPlan == 3 ){
            if( $counter->regular_product_count > 100 ){
                $errors[] = $downgrade_err_msg;
            }
        }else if( $planID == 5 && $newPlan == 4 ){
            if( $counter->regular_product_count > 500 ){
                $errors[] = $downgrade_err_msg;
            }
        }
        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save
        try {
            DB::table('users')
                ->where('id', $shop->id)
                ->update(array('plan_id' => $planID));

        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }
        return ['success' => true, 'errors' => $errors, 'plan_id' => $planID];
    }

    /**
     * Get Amazon Saved Associates
     *
     * @return \Illuminate\Http\Response
     */
    public function getAmazonAssociates()
    {
        $shop = Auth::user();

        $errors = [];

        try {
            $associates = DB::table('amazon_associates')
                ->select('id', 'locale', 'associate_id')
                ->where('user_id', $shop->id)
                ->orderBy('locale')
                ->get();

            $counter = Counters::where('user_id', $shop->id)->where('plan_id', $shop->plan_id)->where('status', 'active')->first();
            $is_disable_free = $counter->is_disable_freemium;
        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }


        return ['success' => true, 'errors' => $errors, 'associates' => $associates, 'is_disable_free_plan' => $is_disable_free];
    }

    /**
     * Add Amazon Associate
     *
     * @return \Illuminate\Http\Response
     */
    public function addAmazonAssociate(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $locale = strtoupper($request->post('locale'));

        if (!preg_match('~^[A-Z]{2}$~', $locale) || ($locale != 'BR' && !defined("Keepa\objects\AmazonLocale::" . $locale))) {
            $errors[] = 'Invalid Amazon locale.';
        }

        $validator = Validator::make($request->all(), [
            'associate_id' => 'required|max:250'
        ]);

        if ($validator->fails()) {
            $errors[] = 'Invalid Amazon Associate ID.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        // Insert record
        try {
            DB::table('amazon_associates')
                ->insert([ 'user_id' => $shop->id, 'locale' => $locale, 'associate_id' => $request->post('associate_id'), 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);

        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'errors' => $errors];
    }

    /**
     * Delete Amazon Associate
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAmazonAssociate(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $id = strtoupper($request->post('id'));

        if (!preg_match('~^[0-9]+$~', $id)) {
            $errors[] = 'Invalid Amazon Associate record id.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        // Delete record
        try {
            DB::table('amazon_associates')
                ->where([
                    ['id', $id],
                    ['user_id', $shop->id]
                ])
                ->delete();

        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'errors' => $errors];
    }

    /**
     * Save Amazon Associate button label
     *
     * @return \Illuminate\Http\Response
     */
    public function setAmazonAssociateBtn(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $validator = Validator::make($request->all(), [
            'amazon_associate_btn' => 'required|max:100'
        ]);

        if ($validator->fails()) {
            $errors[] = 'Amazon Associate Button Label must be between 1 and 100 chars long.';
            return ['success' => false, 'errors' => $errors];
        }

        // Save
        try {
            DB::table('settings')
                ->where('user_id', $shop->id)
                ->update(array('amazon_associate_btn' => $request->post('amazon_associate_btn')));

        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'errors' => $errors];
    }

    /**
     * Set advanced settings
     *
     * @return \Illuminate\Http\Response
     */
    public function setAdvanced(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $diagnostics = (bool) $request->post('diagnostics');

        // Save
        try {
            DB::table('settings')
                ->where('user_id', $shop->id)
                ->update(array('diagnostics' => $diagnostics));

        } catch(QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'errors' => $errors];
    }
}
