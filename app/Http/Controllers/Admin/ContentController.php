<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintainanceText;
use App\Models\AnnouncementModal;
use App\Models\Setting;
use App\Models\BannerImage;
use App\Models\ShopPage;
use App\Models\SeoSetting;
use Image;
use File;
class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function maintainanceMode()
    {
        $maintainance = MaintainanceText::first();
        return view('admin.maintainance_mode', compact('maintainance'));
    }

    public function maintainanceModeUpdate(Request $request)
    {
        $rules = [
            'description'=> $request->maintainance_mode ? 'required' : ''
        ];
        $customMessages = [
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $maintainance = MaintainanceText::first();
        if($request->image){
            $old_image=$maintainance->image;
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $image_name= 'maintainance-mode-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $image_name='uploads/website-images/'.$image_name;
            Image::make($image)
                ->save(public_path().'/'.$image_name);
            $maintainance->image=$image_name;
            $maintainance->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }
        $maintainance->status = $request->maintainance_mode ? 1 : 0;
        $maintainance->description = $request->description;
        $maintainance->save();

        $notification= trans('admin_validation.Updated Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function announcementModal(){
        $announcement = AnnouncementModal::first();
        return view('admin.announcement', compact('announcement'));
    }

    public function announcementModalUpdate(Request $request)
    {
        $rules = [
            'description' => $request->status ? 'required' : '',
            'title' => $request->status ? 'required' : '',
            'footer_text' => $request->status ? 'required' : '',
            'expired_date' => $request->status ? 'required' : '',
        ];
        $customMessages = [
            'description.required' => trans('admin_validation.Description is required'),
            'title.required' => trans('admin_validation.Title is required'),
            'footer_text.required' => trans('admin_validation.Footer text is required'),
            'expired_date.required' => trans('admin_validation.Expired date is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $announcement = AnnouncementModal::first();
        if($request->image){
            $old_image=$announcement->image;
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $image_name= 'announcement-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $image_name='uploads/website-images/'.$image_name;
            Image::make($image)
                ->save(public_path().'/'.$image_name);
            $announcement->image=$image_name;
            $announcement->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }
        $announcement->description = $request->description;
        $announcement->title = $request->title;
        $announcement->footer_text = $request->footer_text;
        $announcement->expired_date = $request->expired_date;
        $announcement->status = $request->status ? 1 : 0;
        $announcement->save();

        $notification= trans('admin_validation.Updated Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function headerPhoneNumber(){
        $setting = Setting::first();
        return view('admin.header_phone_number',compact('setting'));
    }

    public function updateHeaderPhoneNumber(Request $request){
        $rules = [
            'topbar_phone'=>'required',
            'topbar_email'=>'required',
            'menu_phone'=>'required'
        ];
        $customMessages = [
            'topbar_phone.required' => trans('admin_validation.Topbar phone is required'),
            'topbar_email.required' => trans('admin_validation.Topbar email is required'),
            'menu_phone.required' => trans('admin_validation.Menu phone is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $setting = Setting::first();
        $setting->topbar_phone = $request->topbar_phone;
        $setting->topbar_email = $request->topbar_email;
        $setting->menu_phone = $request->menu_phone;
        $setting->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function loginPage(){
        $banner = BannerImage::whereId('13')->first();
        return view('admin.login_page', compact('banner'));
    }

    public function updateloginPage(Request $request){
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'header' => 'required',
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'header.required' => trans('admin_validation.Header link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $banner = BannerImage::whereId('13')->first();

        if($request->bg_image){
            $existing_banner = $banner->image;
            $extention = $request->bg_image->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->bg_image)
                ->save(public_path().'/'.$banner_name);
            $banner->image = $banner_name;
            $banner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->header = $request->header;
        $banner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function shopPage(){
        $shop_page = ShopPage::first();
        return view('admin.shop_page', compact('shop_page'));
    }

    public function updateFilterPrice(Request $request){
        $rules = [
            'filter_price_range' => 'required|numeric',
        ];
        $customMessages = [
            'filter_price_range.required' => trans('admin_validation.Filter price is required'),
            'filter_price_range.numeric' => trans('admin_validation.Filter price should be numeric number'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shop_page = ShopPage::first();
        $shop_page->filter_price_range = $request->filter_price_range;
        $shop_page->save();
        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function seoSetup(){
        $pages = SeoSetting::all();
        return view('admin.seo_setup', compact('pages'));
    }

    public function updateSeoSetup(Request $request, $id){
        $rules = [
            'seo_title' => 'required',
            'seo_description' => 'required'
        ];
        $customMessages = [
            'seo_title.required' => trans('admin_validation.Seo title is required'),
            'seo_description.required' => trans('admin_validation.Seo description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $page = SeoSetting::find($id);
        $page->seo_title = $request->seo_title;
        $page->seo_description = $request->seo_description;
        $page->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function productDetailBanner(){
        $banner = BannerImage::whereId('14')->first();
        $setting = Setting::first();
        return view('admin.product_detail_banner', compact('banner','setting'));
    }


    public function updateStockQtyVisibility(){
        $setting = Setting::first();
        if($setting->show_product_qty == 1){
            $setting->show_product_qty = 0;
            $setting->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $setting->show_product_qty = 1;
            $setting->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function defaultAvatar(){
        $defaultProfile = BannerImage::whereId('15')->first();
        return view('admin.default_profile_image', compact('defaultProfile'));
    }

    public function updateDefaultAvatar(Request $request){
        $defaultProfile = BannerImage::whereId('15')->first();
        if($request->avatar){
            $existing_avatar = $defaultProfile->image;
            $extention = $request->avatar->getClientOriginalExtension();
            $default_avatar = 'default-avatar'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $default_avatar = 'uploads/website-images/'.$default_avatar;
            Image::make($request->avatar)
                ->save(public_path().'/'.$default_avatar);
            $defaultProfile->image = $default_avatar;
            $defaultProfile->save();
            if($existing_avatar){
                if(File::exists(public_path().'/'.$existing_avatar))unlink(public_path().'/'.$existing_avatar);
            }
        }

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function sellerCondition(){
        $setting = Setting::first();
        return view('admin.seller_condition', compact('setting'));
    }

    public function updatesellerCondition(Request $request){
        $rules = [
            'terms_and_condition' => 'required'
        ];
        $customMessages = [
            'terms_and_condition.required' => trans('admin_validation.Terms and condition is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $setting = Setting::first();
        $setting->seller_condition = $request->terms_and_condition;
        $setting->save();
        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
