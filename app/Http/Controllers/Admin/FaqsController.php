<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\cmsFaq;
use App\Models\cmsFaqContent;
use Illuminate\Http\Request;

class FaqsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index()
    {
        $records = cmsFaq::with('cmsFaqContent')->get();

        return view('admin.faqs.edit',compact('records'));
    }

    public function update(Request $request)
    {

        try{

              $input =  $request->except(['_token']);


              $faq_type_list  =  $input['faq_type'];
              $faq_question_list  =  $input['faq_question'];
              $faq_answer_list  =  $input['faq_answer'];


             //  Start :: Save faqs Type

            foreach ($faq_type_list as $key => $value) {
                $details_data = [
                    'faq_type' => $value
                ];

                cmsFaq::where('faq_slug', $key)->update($details_data);
            }

             // End ::  Save faqs Type


                foreach ($faq_question_list as $key_question => $faq_question) {


                    foreach ($faq_answer_list as $key_answer => $faq_answer) {


                        if ($faq_question !== null && $faq_answer!==null) {

                            if ($key_question == $key_answer) {

                                $details_data = [
                                    'faq_question' => $faq_question,
                                    'faq_answer' => $faq_answer
                                ];

                                cmsFaqContent::where('id', $key_answer)
                                    ->update($details_data);
                            }
                        }
                    }
                }

                return redirect()->route('admin.faqs')
                    ->with('success', 'Saved.');


        }catch( \Exception $e ){
            return redirect()->route('admin.faqs')
                ->with('error', $e->getMessage());

        }


    }
}
