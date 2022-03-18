<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Image;
use File;
class TermsAndConditionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $termsAndCondition = TermsAndCondition::first();
        $isTermsCondition = false;
        if($termsAndCondition){
            $isTermsCondition = true;
        }
        return view('admin.terms_and_condition',compact('termsAndCondition','isTermsCondition'));
    }


    public function store(Request $request)
    {
        $rules = [
            'terms_and_condition' => 'required',
            'banner_image' => 'required',
        ];
        $customMessages = [
            'terms_and_condition.required' => trans('admin_validation.Terms and condition is required'),
            'banner_image.required' => trans('admin_validation.Banner image is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $termsAndCondition = new TermsAndCondition();

        $termsAndCondition->terms_and_condition = $request->terms_and_condition;
        $termsAndCondition->save();
        if($request->banner_image){
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'terms-condition'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $termsAndCondition->terms_condition_banner = $banner_name;
            $termsAndCondition->save();
        }

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.terms-and-condition.index')->with($notification);
    }


    public function update(Request $request, $id)
    {
        $termsAndCondition = TermsAndCondition::find($id);

        $rules = [
            'terms_and_condition' => 'required',
            'banner_image' => $termsAndCondition->terms_condition_banner ? '' : 'required',
        ];
        $customMessages = [
            'terms_and_condition.required' => trans('admin_validation.Terms and condition is required'),
            'banner_image.required' => trans('admin_validation.Banner image is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $termsAndCondition->terms_and_condition = $request->terms_and_condition;
        $termsAndCondition->save();
        if($request->banner_image){
            $exist_banner = $termsAndCondition->terms_condition_banner;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'terms-condition'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $termsAndCondition->terms_condition_banner = $banner_name;
            $termsAndCondition->save();

            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.terms-and-condition.index')->with($notification);
    }



}
