<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPagesContent;
use Illuminate\Http\Request;
use Auth;
use App\Models\CmsGlobalContent;
use App\Models\CmsSideBarMenu;
use Response;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function Dashboard()
    {

         $admin = Auth::guard('admin')->user();
         //logger("=============ADMIN DETAILS==========");
        // logger($admin);

        if(Auth::guard('admin')->check()) // this means that the admin was logged in.
        {
           // logger("admin is");
           // logger("==Admin are able to Dashboard==");
            try{

                $CmsGlobalContent = CmsGlobalContent::first();

                $CmsSideBarMenu = CmsSideBarMenu::all();

                return view('admin.home',compact('CmsGlobalContent','CmsSideBarMenu'));

            }catch( \Exception $e ){
                return redirect()->route('admin.dashboard')
                    ->with('error', $e->getMessage());

            }

        }else{
           // logger("admin is not login");
            return redirect('/app');
        }

    }


    public function saveGlobalContent(Request $request,$id)
    {


        try{
            if($id) {

                $input =  $request->all();

                $CmsGlobalContent = CmsGlobalContent::find($id);
                $CmsGlobalContent->side_bar_logo_text1  = $input['side_bar_logo_text1'];
                $CmsGlobalContent->side_bar_logo_text2  = $input['side_bar_logo_text2'];
                $CmsGlobalContent->footer_cpr_text1  = $input['footer_cpr_text1'];
                $CmsGlobalContent->footer_cpr_link  = $input['footer_cpr_link'];
                $CmsGlobalContent->footer_cpr_text2  = $input['footer_cpr_text2'];

                        if($request->hasFile("side_bar_logo")){
                            if ($image = $request->file("side_bar_logo")) {
                                $destinationPath = 'images/';
                                $OriginalName = explode('.', $image->getClientOriginalName())[0];
                                $ImageName = strtolower($OriginalName).date('YmdHis') . "." . $image->getClientOriginalExtension();
                                $image->move($destinationPath, $ImageName);
                                $side_bar_logo = $destinationPath.$ImageName;
                                $CmsGlobalContent->side_bar_logo  = $side_bar_logo;
                            }
                        }

                $CmsGlobalContent->save();


                return redirect()->route('admin.dashboard')
                    ->with('success', 'Saved.');
            }else{
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Not Saved.');
            }

        }catch( \Exception $e ){
            return redirect()->route('admin.dashboard')
                ->with('error', $e->getMessage());

        }


    }

    public function saveSideBarMenu(Request $request)
    {


        try{

            $input =  $request->all();

            $menu_title_list  =  $input['menu_title'];
            $menu_link_redirect_to_list  =  $input['menu_link_redirect_to'];

            $menu_icon_list  =  @$input['menu_icon'] ? $input['menu_icon'] : [];


            foreach ($menu_title_list as $key_title => $val_title) {

                foreach ($menu_link_redirect_to_list as $key_link => $val_link) {

                    if ($key_title !== null && $key_link!==null) {

                        if ($key_title == $key_link) {

                            $details_data = [
                                'menu_title' => $val_title,
                                'menu_link_redirect_to' => $val_link
                            ];

                            CmsSideBarMenu::where('menu_slug', $key_link)
                                ->update($details_data);
                        }
                    }
                }
            }

            if(count($menu_icon_list)>0) {
                foreach ($menu_icon_list as $key_icon => $val_icon) {

                        if ($image = $val_icon) {
                            $destinationPath = 'images/sidebar/';
                            $OriginalName = explode('.', $image->getClientOriginalName())[0];
                            $ImageName = strtolower($OriginalName).date('YmdHis') . "." . $image->getClientOriginalExtension();
                            $image->move($destinationPath, $ImageName);
                            $menu_icon = $destinationPath.$ImageName;

                            $details_data = [
                                'menu_icon' => $menu_icon,
                            ];

                            CmsSideBarMenu::where('menu_slug', $key_icon)
                                ->update($details_data);
                    }
                }
            }

            return redirect()->route('admin.dashboard')
                    ->with('success', 'Sidebar Menu Saved.');


        }catch( \Exception $e ){
            return redirect()->route('admin.dashboard')
                ->with('error', $e->getMessage());

        }


    }


    public function removeImageFieldValue(Request $request,$id)
    {

       try {

            $input = $request->all();

           CmsPagesContent::where('id', $id)
               ->update([
                   "field_value" => null
               ]);

           return  Response::json(["success" => true], 200);

         } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', $e->getMessage());

        }
    }


}
