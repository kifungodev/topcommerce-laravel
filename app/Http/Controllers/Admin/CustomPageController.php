<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Image;
use File;
class CustomPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $customPages = CustomPage::all();
        return view('admin.custom_page',compact('customPages'));
    }

    public function create()
    {
        return view('admin.create_custom_page');
    }


    public function store(Request $request)
    {
        $rules = [
            'description' => 'required',
            'banner_image' => 'required',
            'page_name' => 'required|unique:custom_pages',
            'slug' => 'required|unique:custom_pages',
            'status' => 'required'
        ];
        $customMessages = [
            'page_name.required' => trans('admin_validation.Page name is required'),
            'page_name.unique' => trans('admin_validation.Page name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'description.required' => trans('admin_validation.Description is required'),
            'banner_image.required' => trans('admin_validation.Banner image is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $customPage = new CustomPage();
        if($request->banner_image){
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'custom-page'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $customPage->banner_image = $banner_name;
        }

        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.custom-page.index')->with($notification);
    }


    public function edit($id)
    {
        $customPage = CustomPage::find($id);
        return view('admin.edit_custom_page',compact('customPage'));
    }

    public function update(Request $request, $id)
    {
        $customPage = CustomPage::find($id);
        $rules = [
            'description' => 'required',
            'page_name' => 'required|unique:custom_pages,page_name,'.$customPage->id,
            'slug' => 'required|unique:custom_pages,page_name,'.$customPage->id,
            'status' => 'required'
        ];
        $customMessages = [
            'page_name.required' => trans('admin_validation.Page name is required'),
            'page_name.unique' => trans('admin_validation.Page name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'description.required' => trans('admin_validation.Description is required'),

        ];
        $this->validate($request, $rules,$customMessages);

        if($request->banner_image){
            $exist_banner = $customPage->banner_image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'custom-page'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->resize(1000,null,function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/'.$banner_name);
            $customPage->banner_image = $banner_name;
            $customPage->save();

            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.custom-page.index')->with($notification);
    }

    public function destroy($id)
    {
        $customPage = CustomPage::find($id);
        $exist_banner = $customPage->banner_image;
        $customPage->delete();
        if($exist_banner){
            if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
        }

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.custom-page.index')->with($notification);
    }


    public function changeStatus($id){
        $customPage = CustomPage::find($id);
        if($customPage->status == 1){
            $customPage->status = 0;
            $customPage->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $customPage->status = 1;
            $customPage->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

}
