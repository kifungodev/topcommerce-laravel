<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\Setting;
class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $contactMessages = ContactMessage::all();
        $setting = Setting::first();
        return view('admin.contact_message',compact('contactMessages','setting'));
    }

    public function destroy($id){
        $contactMessage = ContactMessage::find($id);
        $contactMessage->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function handleSaveContactMessage(){
        $setting = Setting::first();
        if($setting->enable_save_contact_message == 1){
            $setting->enable_save_contact_message = 0;
            $setting->save();
            $message = trans('admin_validation.Disable Successfully');
        }else{
            $setting->enable_save_contact_message = 1;
            $setting->save();
            $message = trans('admin_validation.Enable Successfully');
        }
        return response()->json($message);
    }
}
