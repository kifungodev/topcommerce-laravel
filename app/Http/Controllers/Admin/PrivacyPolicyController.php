<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Image;
use File;
class PrivacyPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $privacyPolicy = TermsAndCondition::first();
        $isPrivacyPolicy = false;
        if($privacyPolicy){
            $isPrivacyPolicy = true;
        }
        return view('admin.privacy_policy',compact('privacyPolicy','isPrivacyPolicy'));
    }


    public function store(Request $request)
    {
        $rules = [
            'privacy_policy' => 'required',
            'banner_image' => 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('admin_validation.Privacy policy is required'),
            'banner_image.required' => trans('admin_validation.Banner image is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy = new TermsAndCondition();

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();
        if($request->banner_image){
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'privacy-policy'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $privacyPolicy->privacy_banner = $banner_name;
            $privacyPolicy->save();
        }

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.privacy-policy.index')->with($notification);
    }


    public function update(Request $request, $id)
    {
        $privacyPolicy = TermsAndCondition::find($id);

        $rules = [
            'privacy_policy' => 'required',
            'banner_image' => $privacyPolicy->privacy_banner ? '' : 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('admin_validation.Privacy policy is required'),
            'banner_image.required' => trans('admin_validation.Banner image is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();
        if($request->banner_image){
            $exist_banner = $privacyPolicy->privacy_banner;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'privacy-policy'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $privacyPolicy->privacy_banner = $banner_name;
            $privacyPolicy->save();

            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        $notification = trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.privacy-policy.index')->with($notification);
    }
}
