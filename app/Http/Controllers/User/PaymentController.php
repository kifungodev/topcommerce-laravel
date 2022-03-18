<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BreadcrumbImage;
use Auth;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\Vendor;
use App\Models\ShippingMethod;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StripePayment;
use App\Mail\OrderSuccessfully;
use App\Helpers\MailHelper;
use App\Models\EmailTemplate;
use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\PaystackAndMollie;
use App\Models\InstamojoPayment;
use App\Models\Coupon;
use Mail;
Use Stripe;
use Cart;
use Session;
use Str;
use Razorpay\Api\Api;
use Exception;
use Redirect;

use Mollie\Laravel\Facades\Mollie;


class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function cashOnDelivery(Request $request){
        $tax_amount = 0;
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;

        $user = Auth::guard('web')->user();
        $billing = BillingAddress::where('user_id', $user->id)->first();
        $shipping = ShippingAddress::where('user_id', $user->id)->first();
        $cartContents = Cart::content();
        $shipping_method = Session::get('shipping_method');
        $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
        $shipping_fee = $shippingMethod->fee;
        foreach ($cartContents as $key => $content) {
            $tax = $content->options->tax * $content->qty;
            $tax_amount = $tax_amount + $tax;
        }

        $subTotal = 0;
        foreach ($cartContents as $cartContent) {
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productPrice = $cartContent->price ;
            $total = $productPrice * $cartContent->qty ;
            $subTotal += $total;
        }

        $total_price = $tax_amount + $subTotal;
        if(Session::get('coupon_price') && Session::get('offer_type')) {
            if(Session::get('offer_type') == 1) {
                $coupon_price = Session::get('coupon_price');
                $coupon_price = ($coupon_price / 100) * $total_price;
            }else {
                $coupon_price = Session::get('coupon_price');
            }
        }

        $total_price = $total_price - $coupon_price ;
        $total_price += $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $setting = Setting::first();

        $additional_information = '';
        if(Session::get('addition_information')){
            $additional_information = Session::get('addition_information');
        }
        $agree_terms_condition = 'no';
        if(Session::get('agree_terms_condition')){
            $agree_terms_condition = Session::get('agree_terms_condition');
        }

        $amount_real_currency = $total_price;
        $amount_usd = round($total_price / $setting->currency_rate,2);
        $currency_rate = $setting->currency_rate;
        $currency_icon = $setting->currency_icon;
        $currency_name = $setting->currency_name;

        $order = new Order();
        $orderId = substr(rand(0,time()),0,10);
        $order->order_id = $orderId;
        $order->user_id = $user->id;
        $order->sub_total = $subTotal;
        $order->amount_real_currency = $total_price;
        $order->amount_usd = $amount_usd;
        $order->currency_rate = $currency_rate;
        $order->currency_icon = $currency_icon;
        $order->currency_name = $currency_name;
        $order->product_qty = Cart::count();
        $order->payment_method = 'Cash on Delivery';
        $order->payment_status = 0;
        $order->shipping_method = $shippingMethod->title;
        $order->shipping_cost = $shippingMethod->fee;
        $order->coupon_coast = $coupon_price;
        $order->order_vat = $tax_amount;
        $order->order_status = 0;
        $order->cash_on_delivery = 1;
        $order->additional_info = $additional_information;
        $order->agree_terms_condition = $agree_terms_condition;
        $order->save();

        if(Session::get('coupon_name')){
            $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
            $qty = $coupon->apply_qty;
            $qty = $qty +1;
            $coupon->apply_qty = $qty;
            $coupon->save();
        }


        $order_details = '';
        foreach ($cartContents as $key => $cartContent) {

            $productUnitPrice = 0;
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productUnitPrice = $cartContent->price;

            $product = Product::find($cartContent->id);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartContent->id;
            $orderProduct->seller_id = $product->vendor_id;
            $orderProduct->product_name = $cartContent->name;
            $orderProduct->unit_price = $productUnitPrice;
            $orderProduct->qty = $cartContent->qty;
            $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
            $orderProduct->save();

            $productStock = Product::find($cartContent->id);
            $qty = $productStock->qty - $cartContent->qty;
            $productStock->qty = $qty;
            $productStock->save();

            if(count($cartContent->options->variants) > 0) {
                foreach($cartContent->options->variants as $index => $variant) {
                    $productVariant = new OrderProductVariant();
                    $productVariant->order_product_id = $orderProduct->id;
                    $productVariant->product_id = $cartContent->id;
                    $productVariant->variant_name = $variant;
                    $productVariant->variant_value = $cartContent->options->values[$index];
                    $productVariant->variant_price = $cartContent->options->prices[$index];
                    $productVariant->save();
                }
            }

            $order_details.='Product: '.$cartContent->name. '<br>';
            $order_details.='Quantity: '. $cartContent->qty .'<br>';
            $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
        }

        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $order->id;
        $orderAddress->billing_name = $billing->name;
        $orderAddress->billing_email = $billing->email;
        $orderAddress->billing_phone = $billing->phone;
        $orderAddress->billing_address = $billing->address;
        $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
        $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
        $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
        $orderAddress->billing_zip_code = $billing->zip_code;
        $orderAddress->shipping_name = $shipping->name;
        $orderAddress->shipping_email = $shipping->email;
        $orderAddress->shipping_phone = $shipping->phone;
        $orderAddress->shipping_address = $shipping->address;
        $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
        $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
        $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
        $orderAddress->shipping_zip_code = $shipping->zip_code;
        $orderAddress->save();

        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',6)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);
        $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
        $message = str_replace('{{payment_method}}','Cash on delivery',$message);
        $message = str_replace('{{payment_status}}','Pending',$message);
        $message = str_replace('{{order_status}}','Pending',$message);
        $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
        $message = str_replace('{{order_detail}}',$order_details,$message);
        Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

        Session::forget('hipping_method');
        Session::forget('coupon_price');
        Session::forget('coupon_name');
        Session::forget('offer_type');
        Session::forget('agree_terms_condition');
        Session::forget('addition_information');
        Cart::destroy();

        $notification = trans('user_validation.Order submited successfully. please wait for admin approval');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('user.order')->with($notification);

    }

    public function payWithStripe(Request $request){

        $tax_amount = 0;
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;

        $user = Auth::guard('web')->user();
        $billing = BillingAddress::where('user_id', $user->id)->first();
        $shipping = ShippingAddress::where('user_id', $user->id)->first();
        $cartContents = Cart::content();
        $shipping_method = Session::get('shipping_method');
        $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
        $shipping_fee = $shippingMethod->fee;
        foreach ($cartContents as $key => $content) {
            $tax = $content->options->tax * $content->qty;
            $tax_amount = $tax_amount + $tax;
        }

        $subTotal = 0;
        foreach ($cartContents as $cartContent) {
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productPrice = $cartContent->price ;
            $total = $productPrice * $cartContent->qty ;
            $subTotal += $total;
        }

        $total_price = $tax_amount + $subTotal;
        if(Session::get('coupon_price') && Session::get('offer_type')) {
            if(Session::get('offer_type') == 1) {
                $coupon_price = Session::get('coupon_price');
                $coupon_price = ($coupon_price / 100) * $total_price;
            }else {
                $coupon_price = Session::get('coupon_price');
            }
        }

        $total_price = $total_price - $coupon_price ;
        $total_price += $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $setting = Setting::first();

        $additional_information = '';
        if(Session::get('addition_information')){
            $additional_information = Session::get('addition_information');
        }
        $agree_terms_condition = 'no';
        if(Session::get('agree_terms_condition')){
            $agree_terms_condition = Session::get('agree_terms_condition');
        }

        $amount_real_currency = $total_price;
        $amount_usd = round($total_price / $setting->currency_rate,2);
        $currency_rate = $setting->currency_rate;
        $currency_icon = $setting->currency_icon;
        $currency_name = $setting->currency_name;



        $stripe = StripePayment::first();
        $payableAmount = round($total_price * $stripe->currency_rate,2);
        Stripe\Stripe::setApiKey($stripe->stripe_secret);

        $result = Stripe\Charge::create ([
                "amount" => $payableAmount * 100,
                "currency" => $stripe->currency_code,
                "source" => $request->stripeToken,
                "description" => env('APP_NAME')
        ]);

        $order = new Order();
        $orderId = substr(rand(0,time()),0,10);
        $order->order_id = $orderId;
        $order->user_id = $user->id;
        $order->sub_total = $subTotal;
        $order->amount_real_currency = $total_price;
        $order->amount_usd = $amount_usd;
        $order->currency_rate = $currency_rate;
        $order->currency_icon = $currency_icon;
        $order->currency_name = $currency_name;
        $order->transection_id = $result->balance_transaction;
        $order->product_qty = Cart::count();
        $order->payment_method = 'Stripe';
        $order->payment_status = 1;
        $order->shipping_method = $shippingMethod->title;
        $order->shipping_cost = $shippingMethod->fee;
        $order->coupon_coast = $coupon_price;
        $order->order_vat = $tax_amount;
        $order->order_status = 0;
        $order->cash_on_delivery = 0;
        $order->additional_info = $additional_information;
        $order->agree_terms_condition = $agree_terms_condition;
        $order->save();

        if(Session::get('coupon_name')){
            $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
            $qty = $coupon->apply_qty;
            $qty = $qty +1;
            $coupon->apply_qty = $qty;
            $coupon->save();
        }

        $order_details = '';
        foreach ($cartContents as $key => $cartContent) {

            $productUnitPrice = 0;
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productUnitPrice = $cartContent->price;

            $product = Product::find($cartContent->id);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartContent->id;
            $orderProduct->seller_id = $product->vendor_id;
            $orderProduct->product_name = $cartContent->name;
            $orderProduct->unit_price = $productUnitPrice;
            $orderProduct->qty = $cartContent->qty;
            $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
            $orderProduct->save();

            $productStock = Product::find($cartContent->id);
            $qty = $productStock->qty - $cartContent->qty;
            $productStock->qty = $qty;
            $productStock->save();

            if(count($cartContent->options->variants) > 0) {
                foreach($cartContent->options->variants as $index => $variant) {
                    $productVariant = new OrderProductVariant();
                    $productVariant->order_product_id = $orderProduct->id;
                    $productVariant->product_id = $cartContent->id;
                    $productVariant->variant_name = $variant;
                    $productVariant->variant_value = $cartContent->options->values[$index];
                    $productVariant->variant_price = $cartContent->options->prices[$index];
                    $productVariant->save();
                }
            }

            $order_details.='Product: '.$cartContent->name. '<br>';
            $order_details.='Quantity: '. $cartContent->qty .'<br>';
            $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
        }

        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $order->id;
        $orderAddress->billing_name = $billing->name;
        $orderAddress->billing_email = $billing->email;
        $orderAddress->billing_phone = $billing->phone;
        $orderAddress->billing_address = $billing->address;
        $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
        $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
        $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
        $orderAddress->billing_zip_code = $billing->zip_code;
        $orderAddress->shipping_name = $shipping->name;
        $orderAddress->shipping_email = $shipping->email;
        $orderAddress->shipping_phone = $shipping->phone;
        $orderAddress->shipping_address = $shipping->address;
        $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
        $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
        $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
        $orderAddress->shipping_zip_code = $shipping->zip_code;
        $orderAddress->save();

        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',6)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);
        $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
        $message = str_replace('{{payment_method}}','Stripe',$message);
        $message = str_replace('{{payment_status}}','Success',$message);
        $message = str_replace('{{order_status}}','Pending',$message);
        $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
        $message = str_replace('{{order_detail}}',$order_details,$message);
        Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

        Session::forget('hipping_method');
        Session::forget('coupon_price');
        Session::forget('coupon_name');
        Session::forget('offer_type');
        Session::forget('agree_terms_condition');
        Session::forget('addition_information');
        Cart::destroy();

        $notification = trans('user_validation.Payment Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('user.order')->with($notification);

    }


    public function payWithRazorpay(Request $request){
        $razorpay = RazorpayPayment::first();
        $input = $request->all();
        $api = new Api($razorpay->key,$razorpay->secret_key);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount']));
                $payId = $response->id;

                $tax_amount = 0;
                $total_price = 0;
                $coupon_price = 0;
                $shipping_fee = 0;

                $user = Auth::guard('web')->user();
                $billing = BillingAddress::where('user_id', $user->id)->first();
                $shipping = ShippingAddress::where('user_id', $user->id)->first();
                $cartContents = Cart::content();
                $shipping_method = Session::get('shipping_method');
                $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
                $shipping_fee = $shippingMethod->fee;
                foreach ($cartContents as $key => $content) {
                    $tax = $content->options->tax * $content->qty;
                    $tax_amount = $tax_amount + $tax;
                }

                $subTotal = 0;
                foreach ($cartContents as $cartContent) {
                    $variantPrice = 0;
                    foreach ($cartContent->options->variants as $indx => $variant) {
                        $variantPrice += $cartContent->options->prices[$indx];
                    }
                    $productPrice = $cartContent->price ;
                    $total = $productPrice * $cartContent->qty ;
                    $subTotal += $total;
                }

                $total_price = $tax_amount + $subTotal;
                if(Session::get('coupon_price') && Session::get('offer_type')) {
                    if(Session::get('offer_type') == 1) {
                        $coupon_price = Session::get('coupon_price');
                        $coupon_price = ($coupon_price / 100) * $total_price;
                    }else {
                        $coupon_price = Session::get('coupon_price');
                    }
                }

                $total_price = $total_price - $coupon_price ;
                $total_price += $shipping_fee;
                $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
                $setting = Setting::first();

                $additional_information = '';
                if(Session::get('addition_information')){
                    $additional_information = Session::get('addition_information');
                }
                $agree_terms_condition = 'no';
                if(Session::get('agree_terms_condition')){
                    $agree_terms_condition = Session::get('agree_terms_condition');
                }

                $amount_real_currency = $total_price;
                $amount_usd = round($total_price / $setting->currency_rate,2);
                $currency_rate = $setting->currency_rate;
                $currency_icon = $setting->currency_icon;
                $currency_name = $setting->currency_name;

                $order = new Order();
                $orderId = substr(rand(0,time()),0,10);
                $order->order_id = $orderId;
                $order->user_id = $user->id;
                $order->sub_total = $subTotal;
                $order->amount_real_currency = $total_price;
                $order->amount_usd = $amount_usd;
                $order->currency_rate = $currency_rate;
                $order->currency_icon = $currency_icon;
                $order->currency_name = $currency_name;
                $order->transection_id = $payId;
                $order->product_qty = Cart::count();
                $order->payment_method = 'Razorpay';
                $order->payment_status = 1;
                $order->shipping_method = $shippingMethod->title;
                $order->shipping_cost = $shippingMethod->fee;
                $order->coupon_coast = $coupon_price;
                $order->order_vat = $tax_amount;
                $order->order_status = 0;
                $order->cash_on_delivery = 0;
                $order->additional_info = $additional_information;
                $order->agree_terms_condition = $agree_terms_condition;
                $order->save();

                if(Session::get('coupon_name')){
                    $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
                    $qty = $coupon->apply_qty;
                    $qty = $qty +1;
                    $coupon->apply_qty = $qty;
                    $coupon->save();
                }

                $order_details = '';
                foreach ($cartContents as $key => $cartContent) {

                    $productUnitPrice = 0;
                    $variantPrice = 0;
                    foreach ($cartContent->options->variants as $indx => $variant) {
                        $variantPrice += $cartContent->options->prices[$indx];
                    }
                    $productUnitPrice = $cartContent->price;

                    $product = Product::find($cartContent->id);
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $cartContent->id;
                    $orderProduct->seller_id = $product->vendor_id;
                    $orderProduct->product_name = $cartContent->name;
                    $orderProduct->unit_price = $productUnitPrice;
                    $orderProduct->qty = $cartContent->qty;
                    $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
                    $orderProduct->save();

                    $productStock = Product::find($cartContent->id);
                    $qty = $productStock->qty - $cartContent->qty;
                    $productStock->qty = $qty;
                    $productStock->save();

                    if(count($cartContent->options->variants) > 0) {
                        foreach($cartContent->options->variants as $index => $variant) {
                            $productVariant = new OrderProductVariant();
                            $productVariant->order_product_id = $orderProduct->id;
                            $productVariant->product_id = $cartContent->id;
                            $productVariant->variant_name = $variant;
                            $productVariant->variant_value = $cartContent->options->values[$index];
                            $productVariant->variant_price = $cartContent->options->prices[$index];
                            $productVariant->save();
                        }
                    }

                    $order_details.='Product: '.$cartContent->name. '<br>';
                    $order_details.='Quantity: '. $cartContent->qty .'<br>';
                    $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
                }

                $orderAddress = new OrderAddress();
                $orderAddress->order_id = $order->id;
                $orderAddress->billing_name = $billing->name;
                $orderAddress->billing_email = $billing->email;
                $orderAddress->billing_phone = $billing->phone;
                $orderAddress->billing_address = $billing->address;
                $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
                $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
                $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
                $orderAddress->billing_zip_code = $billing->zip_code;
                $orderAddress->shipping_name = $shipping->name;
                $orderAddress->shipping_email = $shipping->email;
                $orderAddress->shipping_phone = $shipping->phone;
                $orderAddress->shipping_address = $shipping->address;
                $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
                $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
                $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
                $orderAddress->shipping_zip_code = $shipping->zip_code;
                $orderAddress->save();

                MailHelper::setMailConfig();

                $template=EmailTemplate::where('id',6)->first();
                $subject=$template->subject;
                $message=$template->description;
                $message = str_replace('{{user_name}}',$user->name,$message);
                $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
                $message = str_replace('{{payment_method}}','Razorpay',$message);
                $message = str_replace('{{payment_status}}','Success',$message);
                $message = str_replace('{{order_status}}','Pending',$message);
                $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
                $message = str_replace('{{order_detail}}',$order_details,$message);
                Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

                Session::forget('hipping_method');
                Session::forget('coupon_price');
                Session::forget('coupon_name');
                Session::forget('offer_type');
                Session::forget('agree_terms_condition');
                Session::forget('addition_information');
                Cart::destroy();

                $notification = trans('user_validation.Payment Successfully');
                $notification = array('messege'=>$notification,'alert-type'=>'success');
                return redirect()->route('user.order')->with($notification);

            }catch (Exception $e) {
                $notification = trans('user_validation.Payment Faild');
                $notification = array('messege'=>$notification,'alert-type'=>'error');
                return redirect()->back()->with($notification);
            }

        }
    }

    public function payWithFlutterwave(Request $request){
        $flutterwave = Flutterwave::first();
        $curl = curl_init();
        $tnx_id = $request->tnx_id;
        $url = "https://api.flutterwave.com/v3/transactions/$tnx_id/verify";
        $token = $flutterwave->secret_key;
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if($response->status == 'success'){
            $tax_amount = 0;
            $total_price = 0;
            $coupon_price = 0;
            $shipping_fee = 0;

            $user = Auth::guard('web')->user();
            $billing = BillingAddress::where('user_id', $user->id)->first();
            $shipping = ShippingAddress::where('user_id', $user->id)->first();
            $cartContents = Cart::content();
            $shipping_method = Session::get('shipping_method');
            $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
            $shipping_fee = $shippingMethod->fee;
            foreach ($cartContents as $key => $content) {
                $tax = $content->options->tax * $content->qty;
                $tax_amount = $tax_amount + $tax;
            }

            $subTotal = 0;
            foreach ($cartContents as $cartContent) {
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productPrice = $cartContent->price ;
                $total = $productPrice * $cartContent->qty ;
                $subTotal += $total;
            }

            $total_price = $tax_amount + $subTotal;
            if(Session::get('coupon_price') && Session::get('offer_type')) {
                if(Session::get('offer_type') == 1) {
                    $coupon_price = Session::get('coupon_price');
                    $coupon_price = ($coupon_price / 100) * $total_price;
                }else {
                    $coupon_price = Session::get('coupon_price');
                }
            }

            $total_price = $total_price - $coupon_price ;
            $total_price += $shipping_fee;
            $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
            $setting = Setting::first();

            $additional_information = '';
            if(Session::get('addition_information')){
                $additional_information = Session::get('addition_information');
            }
            $agree_terms_condition = 'no';
            if(Session::get('agree_terms_condition')){
                $agree_terms_condition = Session::get('agree_terms_condition');
            }

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency_rate,2);
            $currency_rate = $setting->currency_rate;
            $currency_icon = $setting->currency_icon;
            $currency_name = $setting->currency_name;

            $order = new Order();
            $orderId = substr(rand(0,time()),0,10);
            $order->order_id = $orderId;
            $order->user_id = $user->id;
            $order->sub_total = $subTotal;
            $order->amount_real_currency = $total_price;
            $order->amount_usd = $amount_usd;
            $order->currency_rate = $currency_rate;
            $order->currency_icon = $currency_icon;
            $order->currency_name = $currency_name;
            $order->transection_id = $tnx_id;
            $order->product_qty = Cart::count();
            $order->payment_method = 'Flutterwave';
            $order->payment_status = 1;
            $order->shipping_method = $shippingMethod->title;
            $order->shipping_cost = $shippingMethod->fee;
            $order->coupon_coast = $coupon_price;
            $order->order_vat = $tax_amount;
            $order->order_status = 0;
            $order->cash_on_delivery = 0;
            $order->additional_info = $additional_information;
            $order->agree_terms_condition = $agree_terms_condition;
            $order->save();

            if(Session::get('coupon_name')){
                $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
                $qty = $coupon->apply_qty;
                $qty = $qty +1;
                $coupon->apply_qty = $qty;
                $coupon->save();
            }

            $order_details = '';
            foreach ($cartContents as $key => $cartContent) {

                $productUnitPrice = 0;
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productUnitPrice = $cartContent->price;

                $product = Product::find($cartContent->id);
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $cartContent->id;
                $orderProduct->seller_id = $product->vendor_id;
                $orderProduct->product_name = $cartContent->name;
                $orderProduct->unit_price = $productUnitPrice;
                $orderProduct->qty = $cartContent->qty;
                $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
                $orderProduct->save();

                $productStock = Product::find($cartContent->id);
                $qty = $productStock->qty - $cartContent->qty;
                $productStock->qty = $qty;
                $productStock->save();

                if(count($cartContent->options->variants) > 0) {
                    foreach($cartContent->options->variants as $index => $variant) {
                        $productVariant = new OrderProductVariant();
                        $productVariant->order_product_id = $orderProduct->id;
                        $productVariant->product_id = $cartContent->id;
                        $productVariant->variant_name = $variant;
                        $productVariant->variant_value = $cartContent->options->values[$index];
                        $productVariant->variant_price = $cartContent->options->prices[$index];
                        $productVariant->save();
                    }
                }

                $order_details.='Product: '.$cartContent->name. '<br>';
                $order_details.='Quantity: '. $cartContent->qty .'<br>';
                $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
            }

            $orderAddress = new OrderAddress();
            $orderAddress->order_id = $order->id;
            $orderAddress->billing_name = $billing->name;
            $orderAddress->billing_email = $billing->email;
            $orderAddress->billing_phone = $billing->phone;
            $orderAddress->billing_address = $billing->address;
            $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
            $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
            $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
            $orderAddress->billing_zip_code = $billing->zip_code;
            $orderAddress->shipping_name = $shipping->name;
            $orderAddress->shipping_email = $shipping->email;
            $orderAddress->shipping_phone = $shipping->phone;
            $orderAddress->shipping_address = $shipping->address;
            $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
            $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
            $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
            $orderAddress->shipping_zip_code = $shipping->zip_code;
            $orderAddress->save();

            MailHelper::setMailConfig();

            $template=EmailTemplate::where('id',6)->first();
            $subject=$template->subject;
            $message=$template->description;
            $message = str_replace('{{user_name}}',$user->name,$message);
            $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
            $message = str_replace('{{payment_method}}','Flutterwave',$message);
            $message = str_replace('{{payment_status}}','Success',$message);
            $message = str_replace('{{order_status}}','Pending',$message);
            $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
            $message = str_replace('{{order_detail}}',$order_details,$message);
            Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

            Session::forget('hipping_method');
            Session::forget('coupon_price');
            Session::forget('coupon_name');
            Session::forget('offer_type');
            Session::forget('agree_terms_condition');
            Session::forget('addition_information');
            Cart::destroy();

            $notification = trans('user_validation.Payment Successfully');
            return response()->json(['status' => 'success' , 'message' => $notification]);
        }else{
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['status' => 'faild' , 'message' => $notification]);
        }
    }

    public function payWithMollie(Request $request){

        $tax_amount = 0;
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;

        $user = Auth::guard('web')->user();
        $billing = BillingAddress::where('user_id', $user->id)->first();
        $shipping = ShippingAddress::where('user_id', $user->id)->first();
        $cartContents = Cart::content();
        $shipping_method = Session::get('shipping_method');
        $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
        $shipping_fee = $shippingMethod->fee;
        foreach ($cartContents as $key => $content) {
            $tax = $content->options->tax * $content->qty;
            $tax_amount = $tax_amount + $tax;
        }

        $subTotal = 0;
        foreach ($cartContents as $cartContent) {
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productPrice = $cartContent->price ;
            $total = $productPrice * $cartContent->qty ;
            $subTotal += $total;
        }

        $total_price = $tax_amount + $subTotal;
        if(Session::get('coupon_price') && Session::get('offer_type')) {
            if(Session::get('offer_type') == 1) {
                $coupon_price = Session::get('coupon_price');
                $coupon_price = ($coupon_price / 100) * $total_price;
            }else {
                $coupon_price = Session::get('coupon_price');
            }
        }

        $total_price = $total_price - $coupon_price ;
        $total_price += $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $setting = Setting::first();

        $amount_real_currency = $total_price;
        $mollie = PaystackAndMollie::first();
        $price = $amount_real_currency * $mollie->mollie_currency_rate;
        $price = round($price,2);

        $mollie_api_key = $mollie->mollie_key;
        $currency = strtoupper($mollie->mollie_currency_code);
        Mollie::api()->setApiKey($mollie_api_key);
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $currency,
                'value' => ''.$price.'',
            ],
            'description' => env('APP_NAME'),
            'redirectUrl' => route('user.checkout.mollie-payment-success'),
        ]);

        $payment = Mollie::api()->payments()->get($payment->id);
        session()->put('payment_id',$payment->id);
        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function molliePaymentSuccess(Request $request){
        $mollie = PaystackAndMollie::first();
        $mollie_api_key = $mollie->mollie_key;
        Mollie::api()->setApiKey($mollie_api_key);
        $payment = Mollie::api()->payments->get(session()->get('payment_id'));
        if ($payment->isPaid()){
            $tax_amount = 0;
            $total_price = 0;
            $coupon_price = 0;
            $shipping_fee = 0;

            $user = Auth::guard('web')->user();
            $billing = BillingAddress::where('user_id', $user->id)->first();
            $shipping = ShippingAddress::where('user_id', $user->id)->first();
            $cartContents = Cart::content();
            $shipping_method = Session::get('shipping_method');
            $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
            $shipping_fee = $shippingMethod->fee;
            foreach ($cartContents as $key => $content) {
                $tax = $content->options->tax * $content->qty;
                $tax_amount = $tax_amount + $tax;
            }

            $subTotal = 0;
            foreach ($cartContents as $cartContent) {
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productPrice = $cartContent->price ;
                $total = $productPrice * $cartContent->qty ;
                $subTotal += $total;
            }

            $total_price = $tax_amount + $subTotal;
            if(Session::get('coupon_price') && Session::get('offer_type')) {
                if(Session::get('offer_type') == 1) {
                    $coupon_price = Session::get('coupon_price');
                    $coupon_price = ($coupon_price / 100) * $total_price;
                }else {
                    $coupon_price = Session::get('coupon_price');
                }
            }

            $total_price = $total_price - $coupon_price ;
            $total_price += $shipping_fee;
            $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
            $setting = Setting::first();

            $additional_information = '';
            if(Session::get('addition_information')){
                $additional_information = Session::get('addition_information');
            }
            $agree_terms_condition = 'no';
            if(Session::get('agree_terms_condition')){
                $agree_terms_condition = Session::get('agree_terms_condition');
            }

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency_rate,2);
            $currency_rate = $setting->currency_rate;
            $currency_icon = $setting->currency_icon;
            $currency_name = $setting->currency_name;

            $order = new Order();
            $orderId = substr(rand(0,time()),0,10);
            $order->order_id = $orderId;
            $order->user_id = $user->id;
            $order->sub_total = $subTotal;
            $order->amount_real_currency = $total_price;
            $order->amount_usd = $amount_usd;
            $order->currency_rate = $currency_rate;
            $order->currency_icon = $currency_icon;
            $order->currency_name = $currency_name;
            $order->transection_id = session()->get('payment_id');
            $order->product_qty = Cart::count();
            $order->payment_method = 'Mollie';
            $order->payment_status = 1;
            $order->shipping_method = $shippingMethod->title;
            $order->shipping_cost = $shippingMethod->fee;
            $order->coupon_coast = $coupon_price;
            $order->order_vat = $tax_amount;
            $order->order_status = 0;
            $order->cash_on_delivery = 0;
            $order->additional_info = $additional_information;
            $order->agree_terms_condition = $agree_terms_condition;
            $order->save();

            if(Session::get('coupon_name')){
                $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
                $qty = $coupon->apply_qty;
                $qty = $qty +1;
                $coupon->apply_qty = $qty;
                $coupon->save();
            }

            $order_details = '';
            foreach ($cartContents as $key => $cartContent) {

                $productUnitPrice = 0;
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productUnitPrice = $cartContent->price;

                $product = Product::find($cartContent->id);
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $cartContent->id;
                $orderProduct->seller_id = $product->vendor_id;
                $orderProduct->product_name = $cartContent->name;
                $orderProduct->unit_price = $productUnitPrice;
                $orderProduct->qty = $cartContent->qty;
                $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
                $orderProduct->save();

                $productStock = Product::find($cartContent->id);
                $qty = $productStock->qty - $cartContent->qty;
                $productStock->qty = $qty;
                $productStock->save();

                if(count($cartContent->options->variants) > 0) {
                    foreach($cartContent->options->variants as $index => $variant) {
                        $productVariant = new OrderProductVariant();
                        $productVariant->order_product_id = $orderProduct->id;
                        $productVariant->product_id = $cartContent->id;
                        $productVariant->variant_name = $variant;
                        $productVariant->variant_value = $cartContent->options->values[$index];
                        $productVariant->variant_price = $cartContent->options->prices[$index];
                        $productVariant->save();
                    }
                }

                $order_details.='Product: '.$cartContent->name. '<br>';
                $order_details.='Quantity: '. $cartContent->qty .'<br>';
                $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
            }

            $orderAddress = new OrderAddress();
            $orderAddress->order_id = $order->id;
            $orderAddress->billing_name = $billing->name;
            $orderAddress->billing_email = $billing->email;
            $orderAddress->billing_phone = $billing->phone;
            $orderAddress->billing_address = $billing->address;
            $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
            $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
            $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
            $orderAddress->billing_zip_code = $billing->zip_code;
            $orderAddress->shipping_name = $shipping->name;
            $orderAddress->shipping_email = $shipping->email;
            $orderAddress->shipping_phone = $shipping->phone;
            $orderAddress->shipping_address = $shipping->address;
            $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
            $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
            $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
            $orderAddress->shipping_zip_code = $shipping->zip_code;
            $orderAddress->save();

            MailHelper::setMailConfig();

            $template=EmailTemplate::where('id',6)->first();
            $subject=$template->subject;
            $message=$template->description;
            $message = str_replace('{{user_name}}',$user->name,$message);
            $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
            $message = str_replace('{{payment_method}}','Mollie',$message);
            $message = str_replace('{{payment_status}}','Success',$message);
            $message = str_replace('{{order_status}}','Pending',$message);
            $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
            $message = str_replace('{{order_detail}}',$order_details,$message);
            Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

            Session::forget('hipping_method');
            Session::forget('coupon_price');
            Session::forget('coupon_name');
            Session::forget('offer_type');
            Session::forget('agree_terms_condition');
            Session::forget('addition_information');
            Cart::destroy();

            $notification = trans('user_validation.Payment Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->route('user.order')->with($notification);
        }else{
            $notification = trans('user_validation.Payment Faild');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('user.checkout.payment')->with($notification);
        }
    }

    public function payWithPayStack(Request $request){
        $paystack = PaystackAndMollie::first();

        $reference = $request->reference;
        $transaction = $request->tnx_id;
        $secret_key = $paystack->paystack_secret_key;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST =>0,
            CURLOPT_SSL_VERIFYPEER =>0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $secret_key",
                "Cache-Control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $final_data = json_decode($response);
        if($final_data->status == true) {
            $tax_amount = 0;
            $total_price = 0;
            $coupon_price = 0;
            $shipping_fee = 0;

            $user = Auth::guard('web')->user();
            $billing = BillingAddress::where('user_id', $user->id)->first();
            $shipping = ShippingAddress::where('user_id', $user->id)->first();
            $cartContents = Cart::content();
            $shipping_method = Session::get('shipping_method');
            $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
            $shipping_fee = $shippingMethod->fee;
            foreach ($cartContents as $key => $content) {
                $tax = $content->options->tax * $content->qty;
                $tax_amount = $tax_amount + $tax;
            }

            $subTotal = 0;
            foreach ($cartContents as $cartContent) {
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productPrice = $cartContent->price ;
                $total = $productPrice * $cartContent->qty ;
                $subTotal += $total;
            }

            $total_price = $tax_amount + $subTotal;
            if(Session::get('coupon_price') && Session::get('offer_type')) {
                if(Session::get('offer_type') == 1) {
                    $coupon_price = Session::get('coupon_price');
                    $coupon_price = ($coupon_price / 100) * $total_price;
                }else {
                    $coupon_price = Session::get('coupon_price');
                }
            }

            $total_price = $total_price - $coupon_price ;
            $total_price += $shipping_fee;
            $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
            $setting = Setting::first();

            $additional_information = '';
            if(Session::get('addition_information')){
                $additional_information = Session::get('addition_information');
            }
            $agree_terms_condition = 'no';
            if(Session::get('agree_terms_condition')){
                $agree_terms_condition = Session::get('agree_terms_condition');
            }

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency_rate,2);
            $currency_rate = $setting->currency_rate;
            $currency_icon = $setting->currency_icon;
            $currency_name = $setting->currency_name;

            $order = new Order();
            $orderId = substr(rand(0,time()),0,10);
            $order->order_id = $orderId;
            $order->user_id = $user->id;
            $order->sub_total = $subTotal;
            $order->amount_real_currency = $total_price;
            $order->amount_usd = $amount_usd;
            $order->currency_rate = $currency_rate;
            $order->currency_icon = $currency_icon;
            $order->currency_name = $currency_name;
            $order->transection_id = $transaction;
            $order->product_qty = Cart::count();
            $order->payment_method = 'Paystack';
            $order->payment_status = 1;
            $order->shipping_method = $shippingMethod->title;
            $order->shipping_cost = $shippingMethod->fee;
            $order->coupon_coast = $coupon_price;
            $order->order_vat = $tax_amount;
            $order->order_status = 0;
            $order->cash_on_delivery = 0;
            $order->additional_info = $additional_information;
            $order->agree_terms_condition = $agree_terms_condition;
            $order->save();

            if(Session::get('coupon_name')){
                $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
                $qty = $coupon->apply_qty;
                $qty = $qty +1;
                $coupon->apply_qty = $qty;
                $coupon->save();
            }

            $order_details = '';
            foreach ($cartContents as $key => $cartContent) {

                $productUnitPrice = 0;
                $variantPrice = 0;
                foreach ($cartContent->options->variants as $indx => $variant) {
                    $variantPrice += $cartContent->options->prices[$indx];
                }
                $productUnitPrice = $cartContent->price;

                $product = Product::find($cartContent->id);
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $cartContent->id;
                $orderProduct->seller_id = $product->vendor_id;
                $orderProduct->product_name = $cartContent->name;
                $orderProduct->unit_price = $productUnitPrice;
                $orderProduct->qty = $cartContent->qty;
                $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
                $orderProduct->save();

                $productStock = Product::find($cartContent->id);
                $qty = $productStock->qty - $cartContent->qty;
                $productStock->qty = $qty;
                $productStock->save();

                if(count($cartContent->options->variants) > 0) {
                    foreach($cartContent->options->variants as $index => $variant) {
                        $productVariant = new OrderProductVariant();
                        $productVariant->order_product_id = $orderProduct->id;
                        $productVariant->product_id = $cartContent->id;
                        $productVariant->variant_name = $variant;
                        $productVariant->variant_value = $cartContent->options->values[$index];
                        $productVariant->variant_price = $cartContent->options->prices[$index];
                        $productVariant->save();
                    }
                }

                $order_details.='Product: '.$cartContent->name. '<br>';
                $order_details.='Quantity: '. $cartContent->qty .'<br>';
                $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
            }

            $orderAddress = new OrderAddress();
            $orderAddress->order_id = $order->id;
            $orderAddress->billing_name = $billing->name;
            $orderAddress->billing_email = $billing->email;
            $orderAddress->billing_phone = $billing->phone;
            $orderAddress->billing_address = $billing->address;
            $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
            $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
            $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
            $orderAddress->billing_zip_code = $billing->zip_code;
            $orderAddress->shipping_name = $shipping->name;
            $orderAddress->shipping_email = $shipping->email;
            $orderAddress->shipping_phone = $shipping->phone;
            $orderAddress->shipping_address = $shipping->address;
            $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
            $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
            $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
            $orderAddress->shipping_zip_code = $shipping->zip_code;
            $orderAddress->save();

            MailHelper::setMailConfig();

            $template=EmailTemplate::where('id',6)->first();
            $subject=$template->subject;
            $message=$template->description;
            $message = str_replace('{{user_name}}',$user->name,$message);
            $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
            $message = str_replace('{{payment_method}}','Paystack',$message);
            $message = str_replace('{{payment_status}}','Success',$message);
            $message = str_replace('{{order_status}}','Pending',$message);
            $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
            $message = str_replace('{{order_detail}}',$order_details,$message);
            Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

            Session::forget('hipping_method');
            Session::forget('coupon_price');
            Session::forget('coupon_name');
            Session::forget('offer_type');
            Session::forget('agree_terms_condition');
            Session::forget('addition_information');
            Cart::destroy();

            $notification = trans('user_validation.Payment Successfully');
            return response()->json(['status' => 'success' , 'message' => $notification]);
        }
    }


    public function payWithInstamojo(){

        $tax_amount = 0;
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;

        $user = Auth::guard('web')->user();
        $billing = BillingAddress::where('user_id', $user->id)->first();
        $shipping = ShippingAddress::where('user_id', $user->id)->first();
        $cartContents = Cart::content();
        $shipping_method = Session::get('shipping_method');
        $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
        $shipping_fee = $shippingMethod->fee;
        foreach ($cartContents as $key => $content) {
            $tax = $content->options->tax * $content->qty;
            $tax_amount = $tax_amount + $tax;
        }

        $subTotal = 0;
        foreach ($cartContents as $cartContent) {
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productPrice = $cartContent->price ;
            $total = $productPrice * $cartContent->qty ;
            $subTotal += $total;
        }

        $total_price = $tax_amount + $subTotal;
        if(Session::get('coupon_price') && Session::get('offer_type')) {
            if(Session::get('offer_type') == 1) {
                $coupon_price = Session::get('coupon_price');
                $coupon_price = ($coupon_price / 100) * $total_price;
            }else {
                $coupon_price = Session::get('coupon_price');
            }
        }

        $total_price = $total_price - $coupon_price ;
        $total_price += $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $setting = Setting::first();

        $additional_information = '';
        if(Session::get('addition_information')){
            $additional_information = Session::get('addition_information');
        }
        $agree_terms_condition = 'no';
        if(Session::get('agree_terms_condition')){
            $agree_terms_condition = Session::get('agree_terms_condition');
        }

        $amount_real_currency = $total_price;
        $instamojoPayment = InstamojoPayment::first();
        $price = $amount_real_currency * $instamojoPayment->currency_rate;
        $price = round($price,2);

        $environment = $instamojoPayment->account_mode;
        $api_key = $instamojoPayment->api_key;
        $auth_token = $instamojoPayment->auth_token;

        if($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url.'payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"));
        $payload = Array(
            'purpose' => env("APP_NAME"),
            'amount' => $price,
            'phone' => '918160651749',
            'buyer_name' => Auth::user()->name,
            'redirect_url' => route('user.checkout.instamojo-response'),
            'send_email' => true,
            'webhook' => 'http://www.example.com/webhook/',
            'send_sms' => true,
            'email' => Auth::user()->email,
            'allow_repeated_payments' => false
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        return redirect($response->payment_request->longurl);
    }

    public function instamojoResponse(Request $request){
        $input = $request->all();

        $instamojoPayment = InstamojoPayment::first();
        $environment = $instamojoPayment->account_mode;
        $api_key = $instamojoPayment->api_key;
        $auth_token = $instamojoPayment->auth_token;

        if($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'payments/'.$request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $notification = trans('user_validation.Payment Faild');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('user.checkout.payment')->with($notification);
        } else {
            $data = json_decode($response);
        }

        if($data->success == true) {
            if($data->payment->status == 'Credit') {
                $tax_amount = 0;
                $total_price = 0;
                $coupon_price = 0;
                $shipping_fee = 0;

                $user = Auth::guard('web')->user();
                $billing = BillingAddress::where('user_id', $user->id)->first();
                $shipping = ShippingAddress::where('user_id', $user->id)->first();
                $cartContents = Cart::content();
                $shipping_method = Session::get('shipping_method');
                $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
                $shipping_fee = $shippingMethod->fee;
                foreach ($cartContents as $key => $content) {
                    $tax = $content->options->tax * $content->qty;
                    $tax_amount = $tax_amount + $tax;
                }

                $subTotal = 0;
                foreach ($cartContents as $cartContent) {
                    $variantPrice = 0;
                    foreach ($cartContent->options->variants as $indx => $variant) {
                        $variantPrice += $cartContent->options->prices[$indx];
                    }
                    $productPrice = $cartContent->price ;
                    $total = $productPrice * $cartContent->qty ;
                    $subTotal += $total;
                }

                $total_price = $tax_amount + $subTotal;
                if(Session::get('coupon_price') && Session::get('offer_type')) {
                    if(Session::get('offer_type') == 1) {
                        $coupon_price = Session::get('coupon_price');
                        $coupon_price = ($coupon_price / 100) * $total_price;
                    }else {
                        $coupon_price = Session::get('coupon_price');
                    }
                }

                $total_price = $total_price - $coupon_price ;
                $total_price += $shipping_fee;
                $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
                $setting = Setting::first();

                $additional_information = '';
                if(Session::get('addition_information')){
                    $additional_information = Session::get('addition_information');
                }
                $agree_terms_condition = 'no';
                if(Session::get('agree_terms_condition')){
                    $agree_terms_condition = Session::get('agree_terms_condition');
                }

                $amount_real_currency = $total_price;
                $amount_usd = round($total_price / $setting->currency_rate,2);
                $currency_rate = $setting->currency_rate;
                $currency_icon = $setting->currency_icon;
                $currency_name = $setting->currency_name;

                $order = new Order();
                $orderId = substr(rand(0,time()),0,10);
                $order->order_id = $orderId;
                $order->user_id = $user->id;
                $order->sub_total = $subTotal;
                $order->amount_real_currency = $total_price;
                $order->amount_usd = $amount_usd;
                $order->currency_rate = $currency_rate;
                $order->currency_icon = $currency_icon;
                $order->currency_name = $currency_name;
                $order->transection_id = $request->get('payment_id');
                $order->product_qty = Cart::count();
                $order->payment_method = 'Instamojo';
                $order->payment_status = 1;
                $order->shipping_method = $shippingMethod->title;
                $order->shipping_cost = $shippingMethod->fee;
                $order->coupon_coast = $coupon_price;
                $order->order_vat = $tax_amount;
                $order->order_status = 0;
                $order->cash_on_delivery = 0;
                $order->additional_info = $additional_information;
                $order->agree_terms_condition = $agree_terms_condition;
                $order->save();

                if(Session::get('coupon_name')){
                    $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
                    $qty = $coupon->apply_qty;
                    $qty = $qty +1;
                    $coupon->apply_qty = $qty;
                    $coupon->save();
                }

                $order_details = '';
                foreach ($cartContents as $key => $cartContent) {

                    $productUnitPrice = 0;
                    $variantPrice = 0;
                    foreach ($cartContent->options->variants as $indx => $variant) {
                        $variantPrice += $cartContent->options->prices[$indx];
                    }
                    $productUnitPrice = $cartContent->price;

                    $product = Product::find($cartContent->id);
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $cartContent->id;
                    $orderProduct->seller_id = $product->vendor_id;
                    $orderProduct->product_name = $cartContent->name;
                    $orderProduct->unit_price = $productUnitPrice;
                    $orderProduct->qty = $cartContent->qty;
                    $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
                    $orderProduct->save();

                    $productStock = Product::find($cartContent->id);
                    $qty = $productStock->qty - $cartContent->qty;
                    $productStock->qty = $qty;
                    $productStock->save();

                    if(count($cartContent->options->variants) > 0) {
                        foreach($cartContent->options->variants as $index => $variant) {
                            $productVariant = new OrderProductVariant();
                            $productVariant->order_product_id = $orderProduct->id;
                            $productVariant->product_id = $cartContent->id;
                            $productVariant->variant_name = $variant;
                            $productVariant->variant_value = $cartContent->options->values[$index];
                            $productVariant->variant_price = $cartContent->options->prices[$index];
                            $productVariant->save();
                        }
                    }

                    $order_details.='Product: '.$cartContent->name. '<br>';
                    $order_details.='Quantity: '. $cartContent->qty .'<br>';
                    $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
                }

                $orderAddress = new OrderAddress();
                $orderAddress->order_id = $order->id;
                $orderAddress->billing_name = $billing->name;
                $orderAddress->billing_email = $billing->email;
                $orderAddress->billing_phone = $billing->phone;
                $orderAddress->billing_address = $billing->address;
                $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
                $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
                $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
                $orderAddress->billing_zip_code = $billing->zip_code;
                $orderAddress->shipping_name = $shipping->name;
                $orderAddress->shipping_email = $shipping->email;
                $orderAddress->shipping_phone = $shipping->phone;
                $orderAddress->shipping_address = $shipping->address;
                $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
                $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
                $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
                $orderAddress->shipping_zip_code = $shipping->zip_code;
                $orderAddress->save();

                MailHelper::setMailConfig();

                $template=EmailTemplate::where('id',6)->first();
                $subject=$template->subject;
                $message=$template->description;
                $message = str_replace('{{user_name}}',$user->name,$message);
                $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
                $message = str_replace('{{payment_method}}','Instamojo',$message);
                $message = str_replace('{{payment_status}}','Success',$message);
                $message = str_replace('{{order_status}}','Pending',$message);
                $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
                $message = str_replace('{{order_detail}}',$order_details,$message);
                Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

                Session::forget('hipping_method');
                Session::forget('coupon_price');
                Session::forget('coupon_name');
                Session::forget('offer_type');
                Session::forget('agree_terms_condition');
                Session::forget('addition_information');
                Cart::destroy();

                $notification = trans('user_validation.Payment Successfully');
                $notification = array('messege'=>$notification,'alert-type'=>'success');
                return redirect()->route('user.order')->with($notification);
            }
        }

    }

    public function payWithBank(Request $request){
        $rules = [
            'tnx_info'=>'required',
        ];
        $this->validate($request, $rules);

        $tax_amount = 0;
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;

        $user = Auth::guard('web')->user();
        $billing = BillingAddress::where('user_id', $user->id)->first();
        $shipping = ShippingAddress::where('user_id', $user->id)->first();
        $cartContents = Cart::content();
        $shipping_method = Session::get('shipping_method');
        $shippingMethod = ShippingMethod::where('id',$shipping_method)->first();
        $shipping_fee = $shippingMethod->fee;
        foreach ($cartContents as $key => $content) {
            $tax = $content->options->tax * $content->qty;
            $tax_amount = $tax_amount + $tax;
        }

        $subTotal = 0;
        foreach ($cartContents as $cartContent) {
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productPrice = $cartContent->price ;
            $total = $productPrice * $cartContent->qty ;
            $subTotal += $total;
        }

        $total_price = $tax_amount + $subTotal;
        if(Session::get('coupon_price') && Session::get('offer_type')) {
            if(Session::get('offer_type') == 1) {
                $coupon_price = Session::get('coupon_price');
                $coupon_price = ($coupon_price / 100) * $total_price;
            }else {
                $coupon_price = Session::get('coupon_price');
            }
        }

        $total_price = $total_price - $coupon_price ;
        $total_price += $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $setting = Setting::first();

        $additional_information = '';
        if(Session::get('addition_information')){
            $additional_information = Session::get('addition_information');
        }
        $agree_terms_condition = 'no';
        if(Session::get('agree_terms_condition')){
            $agree_terms_condition = Session::get('agree_terms_condition');
        }

        $amount_real_currency = $total_price;
        $amount_usd = round($total_price / $setting->currency_rate,2);
        $currency_rate = $setting->currency_rate;
        $currency_icon = $setting->currency_icon;
        $currency_name = $setting->currency_name;

        $order = new Order();
        $orderId = substr(rand(0,time()),0,10);
        $order->order_id = $orderId;
        $order->user_id = $user->id;
        $order->sub_total = $subTotal;
        $order->amount_real_currency = $total_price;
        $order->amount_usd = $amount_usd;
        $order->currency_rate = $currency_rate;
        $order->currency_icon = $currency_icon;
        $order->currency_name = $currency_name;
        $order->transection_id = $request->tnx_info;
        $order->product_qty = Cart::count();
        $order->payment_method = 'Bank Payment';
        $order->payment_status = 0;
        $order->shipping_method = $shippingMethod->title;
        $order->shipping_cost = $shippingMethod->fee;
        $order->coupon_coast = $coupon_price;
        $order->order_vat = $tax_amount;
        $order->order_status = 0;
        $order->cash_on_delivery = 0;
        $order->additional_info = $additional_information;
        $order->agree_terms_condition = $agree_terms_condition;
        $order->save();

        if(Session::get('coupon_name')){
            $coupon = Coupon::where(['code' => Session::get('coupon_name')])->first();
            $qty = $coupon->apply_qty;
            $qty = $qty +1;
            $coupon->apply_qty = $qty;
            $coupon->save();
        }

        $order_details = '';
        foreach ($cartContents as $key => $cartContent) {

            $productUnitPrice = 0;
            $variantPrice = 0;
            foreach ($cartContent->options->variants as $indx => $variant) {
                $variantPrice += $cartContent->options->prices[$indx];
            }
            $productUnitPrice = $cartContent->price;

            $product = Product::find($cartContent->id);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartContent->id;
            $orderProduct->seller_id = $product->vendor_id;
            $orderProduct->product_name = $cartContent->name;
            $orderProduct->unit_price = $productUnitPrice;
            $orderProduct->qty = $cartContent->qty;
            $orderProduct->vat = $cartContent->options->tax * $cartContent->qty;
            $orderProduct->save();

            $productStock = Product::find($cartContent->id);
            $qty = $productStock->qty - $cartContent->qty;
            $productStock->qty = $qty;
            $productStock->save();

            if(count($cartContent->options->variants) > 0) {
                foreach($cartContent->options->variants as $index => $variant) {
                    $productVariant = new OrderProductVariant();
                    $productVariant->order_product_id = $orderProduct->id;
                    $productVariant->product_id = $cartContent->id;
                    $productVariant->variant_name = $variant;
                    $productVariant->variant_value = $cartContent->options->values[$index];
                    $productVariant->variant_price = $cartContent->options->prices[$index];
                    $productVariant->save();
                }
            }

            $order_details.='Product: '.$cartContent->name. '<br>';
            $order_details.='Quantity: '. $cartContent->qty .'<br>';
            $order_details.='Price: '.$setting->currency_icon . $cartContent->qty * $productUnitPrice .'<br>';
        }

        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $order->id;
        $orderAddress->billing_name = $billing->name;
        $orderAddress->billing_email = $billing->email;
        $orderAddress->billing_phone = $billing->phone;
        $orderAddress->billing_address = $billing->address;
        $orderAddress->billing_country = $billing->country ? $billing->country->name : '';
        $orderAddress->billing_state = $billing->countryState ? $billing->countryState->name : '';
        $orderAddress->billing_city = $billing->city ? $billing->city->name : '';
        $orderAddress->billing_zip_code = $billing->zip_code;
        $orderAddress->shipping_name = $shipping->name;
        $orderAddress->shipping_email = $shipping->email;
        $orderAddress->shipping_phone = $shipping->phone;
        $orderAddress->shipping_address = $shipping->address;
        $orderAddress->shipping_country = $shipping->country ? $shipping->country->name : '';
        $orderAddress->shipping_state = $shipping->countryState ? $shipping->countryState->name : '';
        $orderAddress->shipping_city = $shipping->city ? $shipping->city->name : '';
        $orderAddress->shipping_zip_code = $shipping->zip_code;
        $orderAddress->save();

        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',6)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);
        $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
        $message = str_replace('{{payment_method}}','Bank Payment',$message);
        $message = str_replace('{{payment_status}}','Pending',$message);
        $message = str_replace('{{order_status}}','Pending',$message);
        $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
        $message = str_replace('{{order_detail}}',$order_details,$message);
        Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));

        Session::forget('hipping_method');
        Session::forget('coupon_price');
        Session::forget('coupon_name');
        Session::forget('offer_type');
        Session::forget('agree_terms_condition');
        Session::forget('addition_information');
        Cart::destroy();

        $notification = trans('user_validation.Order submited successfully. please wait for admin approval');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('user.order')->with($notification);
    }
}
