<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;

use App\Models\BreadcrumbImage;
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
use App\Models\PaypalPayment;
use App\Models\Coupon;
use Str;
use Cart;
use Mail;
use Session;
use Auth;

class PaypalController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $account = PaypalPayment::first();
        $paypal_conf = \Config::get('paypal');
        $this->apiContext = new ApiContext(new OAuthTokenCredential(
            $account->client_id,
            $account->secret_id,
            )
        );

        $setting=array(
            'mode' => $account->account_mode,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path() . '/logs/paypal.log',
            'log.LogLevel' => 'ERROR'
        );
        $this->apiContext->setConfig($setting);
    }


    public function payWithPaypal(){

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
        $paypalSetting = PaypalPayment::first();
        $payableAmount = round($total_price * $paypalSetting->currency_rate,2);

        $name=env('APP_NAME');

        // set payer
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // set amount total
        $amount = new Amount();
        $amount->setCurrency($paypalSetting->currency_code)
            ->setTotal($payableAmount);

        // transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(env('APP_NAME'));

        // redirect url
        $redirectUrls = new RedirectUrls();

        $root_url=url('/');
        $redirectUrls->setReturnUrl($root_url."/user/checkout/paypal-payment-success")
            ->setCancelUrl($root_url."/user/checkout/paypal-payment-cancled");

        // payment
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {

            $notification = trans('user_validation.Payment Faild');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('user.checkout.payment')->with($notification);
        }

        // get paymentlink
        $approvalUrl = $payment->getApprovalLink();

        return redirect($approvalUrl);
    }

    public function paypalPaymentSuccess(Request $request){
        if (empty($request->get('PayerID')) || empty($request->get('token'))) {

            $notification = trans('user_validation.Payment Faild');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('user.checkout.payment')->with($notification);

        }

        $payment_id=$request->get('paymentId');
        $payment = Payment::get($payment_id, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->apiContext);

        if ($result->getState() == 'approved') {
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
            $order->transection_id = $payment_id;
            $order->product_qty = Cart::count();
            $order->payment_method = 'Paypal';
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
            $message = str_replace('{{payment_method}}','Paypal',$message);
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

    public function paypalPaymentCancled(){
        $notification = trans('user_validation.Payment Faild');
        $notification = array('messege'=>$notification,'alert-type'=>'error');
        return redirect()->route('user.checkout.payment')->with($notification);
    }


}
