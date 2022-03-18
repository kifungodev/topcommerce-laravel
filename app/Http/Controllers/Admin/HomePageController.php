<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\PopularCategory;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\ThreeColumnCategory;
use Image;
use File;
class HomePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){

        $popularCategory = PopularCategory::first();
        $categories = Category::whereStatus('1')->orderBy('name','asc')->get();
        $subCategories = SubCategory::whereStatus('1')->orderBy('name','asc')->get();
        $childCategories = ChildCategory::whereStatus('1')->orderBy('name','asc')->get();

        $threeColumnCategory = ThreeColumnCategory::first();

        return view('admin.home_page_banner', compact('popularCategory','categories','subCategories','childCategories','threeColumnCategory'));
    }

    public function updatePopularCategory(Request $request){
        $rules = [
            'category_one' => 'required',
            'category_two' => 'required',
            'category_three' => 'required',
            'category_four' => 'required',
            'title' => 'required',
            'product_qty' => 'required',
        ];
        $customMessages = [
            'category_one.required' => trans('admin_validation.Category one is required'),
            'category_two.required' => trans('admin_validation.Category two is required'),
            'category_three.required' => trans('admin_validation.Category three is required'),
            'category_four.required' => trans('admin_validation.Category four is required'),
            'title.required' => trans('admin_validation.Title is required'),
            'product_qty.required' => trans('admin_validation.Product Qty is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $popularCategory = PopularCategory::first();
        $popularCategory->title = $request->title;
        $popularCategory->product_qty = $request->product_qty;
        $popularCategory->category_id_one = $request->category_one;
        $popularCategory->sub_category_id_one = $request->sub_category_one ? $request->sub_category_one : 0;
        $popularCategory->child_category_id_one = $request->child_category_one ? $request->child_category_one : 0;
        $popularCategory->category_id_two = $request->category_two;
        $popularCategory->sub_category_id_two = $request->sub_category_two ? $request->sub_category_two : 0;
        $popularCategory->child_category_id_two = $request->child_category_two ? $request->child_category_two : 0;
        $popularCategory->category_id_three = $request->category_three;
        $popularCategory->sub_category_id_three = $request->sub_category_three ? $request->sub_category_three : 0;
        $popularCategory->child_category_id_three = $request->child_category_three ? $request->child_category_three : 0;
         $popularCategory->category_id_four = $request->category_four;
        $popularCategory->sub_category_id_four = $request->sub_category_four ? $request->sub_category_four : 0;
        $popularCategory->child_category_id_four = $request->child_category_four ? $request->child_category_four : 0;
        $popularCategory->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateThreeColumnCategory(Request $request){
        $rules = [
            'category_one' => 'required',
            'category_two' => 'required',
            'category_three' => 'required',
        ];
        $customMessages = [
            'category_one.required' => trans('admin_validation.Category one is required'),
            'category_two.required' => trans('admin_validation.Category two is required'),
            'category_three.required' => trans('admin_validation.Category three is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $threeColumnCategory = ThreeColumnCategory::first();
        $threeColumnCategory->category_id_one = $request->category_one;
        $threeColumnCategory->sub_category_id_one = $request->sub_category_one ? $request->sub_category_one : 0;
        $threeColumnCategory->child_category_id_one = $request->child_category_one ? $request->child_category_one : 0;
        $threeColumnCategory->category_id_two = $request->category_two;
        $threeColumnCategory->sub_category_id_two = $request->sub_category_two ? $request->sub_category_two : 0;
        $threeColumnCategory->child_category_id_two = $request->child_category_two ? $request->child_category_two : 0;
        $threeColumnCategory->category_id_three = $request->category_three;
        $threeColumnCategory->sub_category_id_three = $request->sub_category_three ? $request->sub_category_three : 0;
        $threeColumnCategory->child_category_id_three = $request->child_category_three ? $request->child_category_three : 0;
        $threeColumnCategory->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

}
