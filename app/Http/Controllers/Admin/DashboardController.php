<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Vendor;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Brand;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashobard(){
        $todayOrders = Order::with('user')->orderBy('id','desc')->whereDay('created_at', now()->day)->get();
        $totalOrders = Order::with('user')->orderBy('id','desc')->get();
        $monthlyOrders = Order::with('user')->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();
        $yearlyOrders = Order::with('user')->orderBy('id','desc')->whereYear('created_at', now()->year)->get();
        $setting = Setting::first();
        $products = Product::all();
        $reviews = ProductReview::all();
        $reports = ProductReport::all();
        $users = User::all();
        $sellers = Vendor::all();
        $subscribers = Subscriber::where('is_verified',1)->get();
        $blogs = Blog::all();
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.dashboard',compact('todayOrders','totalOrders','setting','monthlyOrders','yearlyOrders','products','reviews','reports','users','sellers','subscribers','blogs','categories','brands'));
    }


}
