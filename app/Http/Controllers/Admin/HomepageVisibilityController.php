<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePageOneVisibility;
class HomepageVisibilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $sections = HomePageOneVisibility::all();
        return view('admin.home_page_one_visibility', compact('sections'));
    }

    public function update(Request $request, $id){
        if($id == 4 || $id == 5 || $id == 8 || $id == 10){
            $section = HomePageOneVisibility::find($id);
            $section->status = $request->status ? 1 : 0;
            $section->save();
        }else{
            $rules = [
                'qty' => $request->status ? 'required' : '',
            ];
            $customMessages = [
                'qty.required' => trans('admin_validation.Quantity is required')
            ];
            $this->validate($request, $rules,$customMessages);

            $section = HomePageOneVisibility::find($id);
            $section->qty = $request->qty;
            $section->status = $request->status ? 1 : 0;
            $section->save();
        }

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
