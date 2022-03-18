<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTax;
use Illuminate\Http\Request;

class ProductTaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $taxes = ProductTax::with('products')->get();
        return view('admin.product_tax',compact('taxes'));
    }

    public function create()
    {
        return view('admin.create_product_tax');
    }


    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|unique:product_taxes',
            'status' => 'required',
            'price' => 'required|numeric'
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'title.unique' => trans('admin_validation.Title already exist'),
            'price.required' => trans('admin_validation.Price is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $productTax = new ProductTax();

        $productTax->title = $request->title;
        $productTax->price = $request->price;
        $productTax->status = $request->status;
        $productTax->save();

        $notification= trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-tax.index')->with($notification);
    }

    public function edit($id)
    {
        $productTax = ProductTax::find($id);
        return view('admin.edit_product_tax',compact('productTax'));

    }


    public function update(Request $request, $id)
    {
        $productTax = ProductTax::find($id);
        $rules = [
            'title' => 'required|unique:product_taxes,title,'.$productTax->id,
            'status' => 'required',
            'price' => 'required|numeric'
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'title.unique' => trans('admin_validation.Title already exist'),
            'price.required' => trans('admin_validation.Price is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $productTax->title = $request->title;
        $productTax->price = $request->price;
        $productTax->status = $request->status;
        $productTax->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-tax.index')->with($notification);
    }

    public function destroy($id)
    {
        $productTax = ProductTax::find($id);
        $productTax->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-tax.index')->with($notification);
    }

    public function changeStatus($id){
        $productTax = ProductTax::find($id);
        if($productTax->status==1){
            $productTax->status=0;
            $productTax->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $productTax->status=1;
            $productTax->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
