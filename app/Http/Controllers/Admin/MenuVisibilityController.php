<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuVisibility;
class MenuVisibilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $menus = MenuVisibility::all();
        return view('admin.menu_visibility', compact('menus'));
    }

    public function update($id){
        $menu = MenuVisibility::find($id);
        if($menu->status==1){
            $menu->status=0;
            $menu->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $menu->status=1;
            $menu->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
