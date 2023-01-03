<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPages;
use App\Models\CmsPagesContent;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','settings')->first();

        return view('admin.settings.edit',compact('records'));
    }

    public function update(Request $request,$id)
    {


        try{
            if($id) {

                $input =  $request->except(['_token']);

                foreach ($input as $key => $value) {

                  //  if($value!==null) {

                        $db_cms_pages_id = $id;

                        $field_slug = $key;
                        $field_value = $value;

                        if($request->hasFile($field_slug)){
                            if ($image = $request->file($field_slug)) {
                                $destinationPath = 'images/product-import-services/';

                                $OriginalName = explode('.', $image->getClientOriginalName())[0];

                                $ImageName = strtolower($OriginalName).date('YmdHis') . "." . $image->getClientOriginalExtension();
                                $image->move($destinationPath, $ImageName);
                                $field_value = $destinationPath.$ImageName;
                            }
                        }

                        $details_data = [
                            'field_value' => $field_value
                        ];

                        CmsPagesContent::where('field_slug', $field_slug)
                            ->where('db_cms_pages_id', $db_cms_pages_id)
                            ->update($details_data);
                   // }

                }

                return redirect()->route('admin.settings')
                    ->with('success', 'Saved.');
            }else{
                return redirect()->route('admin.settings')
                    ->with('error', 'Not Saved.');
            }

        }catch( \Exception $e ){
            return redirect()->route('admin.settings')
                ->with('error', $e->getMessage());

        }


    }
}
