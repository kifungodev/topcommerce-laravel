<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BreadcrumbImage;
use App\Models\BannerImage;
use App\Models\ProductVariantItem;
use App\Models\Product;
use App\Models\CampaignProduct;
use App\Models\Coupon;
use App\Models\Setting;
use Cart;
use Session;
class CartController extends Controller
{
    public function cart(){
        $banner = BreadcrumbImage::where(['id' => 2])->first();
        $banners  = BannerImage::all();
        $cartContents = Cart::content();
        $setting = Setting::first();
        $currencySetting = Setting::first();
        return view('cart', compact('banner','banners','cartContents','setting','currencySetting'));
    }

    public function addToCart(Request $request){
        $itemExist = false;
        $cartContents = Cart::content();
        foreach($cartContents as $cartContent){
            if($cartContent->id == $request->product_id) $itemExist = true;
        }

        $productStock = Product::find($request->product_id);
        if($productStock->qty == 0){
            $notification = trans('user_validation.Product stock out');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        if($productStock->qty < $request->quantity){
            $notification = trans('user_validation.Quantity not available in our stock');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        if($itemExist) {
            $notification = trans('user_validation.Item already exist');
            return response()->json(['status' => 0, 'message' => $notification]);
        }
        $variants = [];
        $values = [];
        $prices = [];
        $variantPrice = 0;
        if($request->variants){
            foreach($request->variants as $index => $varr){
                $variants[] = $request->variantNames[$index];
                $item = ProductVariantItem::where(['id' => $request->items[$index]])->first();
                $values[] = $item->name;
                $prices[] = $item->price;
            }
            $variantPrice = $variantPrice + array_sum($prices);
        }

        $product = Product::with('tax')->find($request->product_id);
        $tax_percentage = $product->tax->price;

        $isCampaign = false;
        $today = date('Y-m-d');
        $campaign = CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
        if($campaign){
            $campaign = $campaign->campaign;
            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                $isCampaign = true;
            }
            $campaignOffer = $campaign->offer;
            $productPrice = $product->price;
            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
            $totalPrice = $product->price;
        }else{
            $totalPrice = $product->price;
            if($product->offer_price != null){
                $offerPrice = $product->offer_price;
            }
        }

        $productPrice = 0;
        if($isCampaign){
            $productPrice = $campaignOfferPrice + $variantPrice;
            $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
        }else{
            if ($product->offer_price == null) {
                $productPrice = $totalPrice + $variantPrice;
                $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
            }else {
                $productPrice = $product->offer_price + $variantPrice;
                $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
            }
        }

        $data=array();
        $data['id'] = $product->id;
        $data['name'] = $product->short_name;
        $data['qty'] = $request->quantity;
        $data['price'] = $productPrice;
        $data['weight'] = 1;
        $data['options']['tax'] = $tax_percentage_amount;
        $data['options']['coupon_price'] = 0;
        $data['options']['image'] = $request->image;
        $data['options']['slug'] = $request->slug;
        $data['options']['variants'] = $variants;
        $data['options']['values'] = $values;
        $data['options']['prices'] = $prices;
        Cart::add($data);

        $notification = trans('user_validation.Item added successfully');
        return response()->json(['status' => 1, 'message' => $notification]);
    }

    public function addToBuy(Request $request){
        $itemExist = false;
        $cartContents = Cart::content();
        foreach($cartContents as $cartContent){
            if($cartContent->id == $request->product_id) $itemExist = true;
        }

        if($itemExist) {
            $notification = trans('user_validation.Item Already Exist');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        $variants = [];
        $values = [];
        $prices = [];
        $variantPrice = 0;
        if($request->variants){
            foreach($request->variants as $index => $varr){
                $variants[] = $request->variantNames[$index];
                $item = ProductVariantItem::where(['id' => $request->items[$index]])->first();
                $values[] = $item->name;
                $prices[] = $item->price;
            }
            $variantPrice = $variantPrice + array_sum($prices);
        }

        $product = Product::with('tax')->find($request->product_id);
        $tax_percentage = $product->tax->price;

        $isCampaign = false;
        $today = date('Y-m-d');
        $campaign = CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
        if($campaign){
            $campaign = $campaign->campaign;
            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                $isCampaign = true;
            }
            $campaignOffer = $campaign->offer;
            $productPrice = $product->price;
            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
            $totalPrice = $product->price;
        }else{
            $totalPrice = $product->price;
            if($product->offer_price != null){
                $offerPrice = $product->offer_price;
            }
        }

        $productPrice = 0;
        if($isCampaign){
            $productPrice = $campaignOfferPrice + $variantPrice;
            $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
        }else{
            if ($product->offer_price == null) {
                $productPrice = $totalPrice + $variantPrice;
                $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
            }else {
                $productPrice = $product->offer_price + $variantPrice;
                $tax_percentage_amount = ($tax_percentage / 100) * $productPrice;
            }
        }

        $data=array();
        $data['id'] = $product->id;
        $data['name'] = $product->short_name;
        $data['qty'] = $request->quantity;
        $data['price'] = $productPrice;
        $data['weight'] = 1;
        $data['options']['tax'] = $tax_percentage_amount;
        $data['options']['coupon_price'] = 0;
        $data['options']['image'] = $request->image;
        $data['options']['slug'] = $request->slug;
        $data['options']['variants'] = $variants;
        $data['options']['values'] = $values;
        $data['options']['prices'] = $prices;
        Cart::add($data);

        $notification = trans('user_validation.Item added successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('cart')->with($notification);
    }



    public function cartItemRemove($rowId){
        Cart::remove($rowId);
        $cartContents = Cart::content();
        $setting = Setting::first();
        $banners  = BannerImage::all();
        return view('ajax_cart_view', compact('cartContents', 'setting','banners'));
    }

    public function cartClear(){
        Cart::destroy();
        Session::forget('coupon_price');
        Session::forget('offer_type');

        $cartContents = Cart::content();
        $setting = Setting::first();
        $banners  = BannerImage::all();
        return view('ajax_cart_view', compact('cartContents', 'setting','banners'));
    }



    public function applyCoupon(Request $request){
        if($request->coupon == null){
            $notification = trans('user_validation.Coupon Field is required');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        $coupon = Coupon::where(['code' => $request->coupon, 'status' => 1])->first();

        if($coupon->expired_date < date('Y-m-d')){
            $notification = trans('user_validation.Coupon already expired');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        if($coupon->apply_qty >=  $coupon->max_quantity ){
            $notification = trans('user_validation.Sorry! You can not apply this coupon');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        if($coupon){
            if($coupon->offer_type == 1){
                $coupon_price = $coupon->discount;
                Session::put('coupon_price', $coupon_price);
                Session::put('offer_type', 1);
                Session::put('coupon_name', $request->coupon);
            }else {
                $coupon_price = $coupon->discount;
                Session::put('coupon_price', $coupon_price);
                Session::put('offer_type', 2);
                Session::put('coupon_name', $request->coupon);
            }

            $cartContents = Cart::content();
            $setting = Setting::first();
            $banners  = BannerImage::all();
            return view('ajax_cart_view', compact('cartContents', 'setting','banners'));
        }else{
            $notification = trans('user_validation.Invalid Coupon');
            return response()->json(['status' => 0, 'message' => $notification]);
        }


    }

    public function cartUpdate(Request $request){
        $stockOut = false;
        foreach($request->rowIds as $index => $rowId){
            $product = Product::find($request->ids[$index]);
            if($product->qty < $request->quantities[$index]){
                $stockOut = true;
            }
        }

        if($stockOut){
            $notification = trans('user_validation.Quantity not available in our stock');
            return response()->json(['status' => 0, 'message' => $notification]);
        }

        foreach($request->rowIds as $index => $rowId){
            Cart::update($rowId, ['qty' => $request->quantities[$index]]);
        }

        $cartContents = Cart::content();
        $setting = Setting::first();
        $banners  = BannerImage::all();
        return view('ajax_cart_view', compact('cartContents', 'setting','banners'));
    }


    public function calculateProductPrice(Request $request) {
        $prices = [];
        $variantPrice = 0;
        if($request->variants){
            foreach($request->variants as $index => $varr){
                $item = ProductVariantItem::where(['id' => $request->items[$index]])->first();
                $prices[] = $item->price;
            }
            $variantPrice = $variantPrice + array_sum($prices);
        }

        $product = Product::with('tax')->find($request->product_id);


        $isCampaign = false;
        $today = date('Y-m-d');
        $campaign = CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
        if($campaign){
            $campaign = $campaign->campaign;
            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                $isCampaign = true;
            }
            $campaignOffer = $campaign->offer;
            $productPrice = $product->price;
            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
            $totalPrice = $product->price;
        }else{
            $totalPrice = $product->price;
            if($product->offer_price != null){
                $offerPrice = $product->offer_price;
            }
        }

        $productPrice = 0;
        if($isCampaign){
            $productPrice = $campaignOfferPrice + $variantPrice;
        }else{
            if ($product->offer_price == null) {
                $productPrice = $totalPrice + $variantPrice;
            }else {
                $productPrice = $product->offer_price + $variantPrice;
            }
        }

        $productPrice = round($productPrice, 2);
        return response()->json(['productPrice' => $productPrice]);
    }

    public function sidebarcartItemRemove($rowId){
        Cart::remove($rowId);
        return response()->json(['status' => 1, 'message' => trans('user_validation.Item removed successfully')]);
    }

    public function loadSidebarCart(){
        return view('ajax_sidebar_cart');
    }

    public function calculateCartQty(){
        $qty = Cart::instance('default')->count();
        return response()->json(['qty' => $qty]);
    }

    public function loadMainCart(){
        $cartContents = Cart::content();
        $setting = Setting::first();
        $banners  = BannerImage::all();
        return view('ajax_cart_view', compact('cartContents', 'setting','banners'));
    }

    public function calculateWholsaleDiscount(Request $request){
        $qty = $request->qty;
        $product = Product::find($request->product_id);
        if($product->wholesales->count() > 0){
            $wholsale = '';
            if($product->wholesales->count() == 1){
                $wholsale = $product->wholesales->first();
            }else{
                $count = $product->wholesales->count();
                $lastChild = $product->wholesales[$count - 1];
                if($lastChild->minimum_product <= $qty){
                    $wholsale = $lastChild;
                }else{
                    $wholsales = $product->wholesales;
                    for($i = 0 ; $i < $count ; $i++){
                        if($qty >= $wholsales[$i]['minimum_product'] && $qty < $wholsales[$i + 1]['minimum_product']){
                            $wholsale = $wholsales[$i];
                        }
                    }
                }

            }
            if($wholsale){
                $price = $request->price;
                $offerPrice = ($wholsale->offer / 100) * $price;
                $price = $price - $offerPrice;
                $price = round($price,2);
                return response()->json(['status' => 1, 'price' => $price]);
            }else{
                return response()->json(['status' => 0]);
            }
        }else{
            return response()->json(['status' => 0]);
        }
    }
}
