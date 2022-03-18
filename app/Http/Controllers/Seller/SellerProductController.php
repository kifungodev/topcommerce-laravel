<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\ProductGallery;
use App\Models\Brand;
use App\Models\ProductTax;
use App\Models\ReturnPolicy;
use App\Models\ProductSpecificationKey;
use App\Models\ProductSpecification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\CampaignProduct;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Wishlist;
use App\Models\Setting;
use Image;
use File;
use Str;
use Auth;




class SellerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $seller = Auth::guard('web')->user()->seller;
        $products = Product::with('category')->orderBy('id','desc')->where('status',1)->where('vendor_id',$seller->id)->get();
        $orderProducts = OrderProduct::all();
        $setting = Setting::first();
        return view('seller.product',compact('products','orderProducts','setting'));
    }

    public function pendingProduct(){
        $seller = Auth::guard('web')->user()->seller;
        $products = Product::with('category')->orderBy('id','desc')->where('status',0)->where('vendor_id',$seller->id)->get();
        $orderProducts = OrderProduct::all();
        $setting = Setting::first();
        return view('seller.product',compact('products','orderProducts','setting'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $productTaxs = ProductTax::where('status',1)->get();
        $retrunPolicies = ReturnPolicy::where('status',1)->get();
        $specificationKeys = ProductSpecificationKey::all();
        return view('seller.create_product',compact('categories','brands','productTaxs','retrunPolicies','specificationKeys'));
    }


    public function getSubcategoryByCategory($id){
        $subCategories=SubCategory::where('category_id',$id)->get();
        $response='<option value="">'.trans('user_validation.Select Sub Category').'</option>';
        foreach($subCategories as $subCategory){
            $response .= "<option value=".$subCategory->id.">".$subCategory->name."</option>";
        }
        return response()->json(['subCategories'=>$response]);
    }

    public function getChildcategoryBySubCategory($id){
        $childCategories=ChildCategory::where('sub_category_id',$id)->get();
        $response='<option value="">'.trans('user_validation.Select Child Category').'</option>';
        foreach($childCategories as $childCategory){
            $response .= "<option value=".$childCategory->id.">".$childCategory->name."</option>";
        }
        return response()->json(['childCategories'=>$response]);
    }

    public function store(Request $request)
    {

        $rules = [
            'short_name' => 'required|unique:products',
            'name' => 'required|unique:products',
            'slug' => 'required|unique:products',
            'thumb_image' => 'required',
            'banner_image' => 'required',
            'category' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'brand' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required',
            'tax' => 'required',
            'is_return' => 'required',
            'is_warranty' => 'required',
            'return_policy_id' => $request->is_return == 1 ?  'required' : '',
        ];
        $customMessages = [
            'short_name.required' => trans('user_validation.Short name is required'),
            'short_name.unique' => trans('user_validation.Short name is required'),
            'name.required' => trans('user_validation.Name is required'),
            'name.unique' => trans('user_validation.Name is required'),
            'slug.required' => trans('user_validation.Slug is required'),
            'slug.unique' => trans('user_validation.Slug already exist'),
            'category.required' => trans('user_validation.Category is required'),
            'thumb_image.required' => trans('user_validation.thumbnail is required'),
            'banner_image.required' => trans('user_validation.Banner is required'),
            'short_description.required' => trans('user_validation.Short description is required'),
            'long_description.required' => trans('user_validation.Long description is required'),
            'brand.required' => trans('user_validation.Brand is required'),
            'price.required' => trans('user_validation.Price is required'),
            'quantity.required' => trans('user_validation.Quantity is required'),
            'tax.required' => trans('user_validation.Tax is required'),
            'is_return.required' => trans('user_validation.Return is required'),
            'is_warranty.required' => trans('user_validation.Warranty is required'),
            'return_policy_id.required' => trans('user_validation.Return policy is required'),
            'status.required' => trans('user_validation.Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);


        $seller = Auth::guard('web')->user()->seller;
        $product = new Product();
        if($request->thumb_image){
            $extention = $request->thumb_image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->thumb_image)
                ->save(public_path().'/'.$image_name);
            $product->thumb_image=$image_name;
        }

        if($request->banner_image){
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'product-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $product->banner_image = $banner_name;
        }

        $product->vendor_id = $seller->id;
        $product->short_name = $request->short_name;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category ? $request->sub_category : 0;
        $product->child_category_id = $request->child_category ? $request->child_category : 0;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->qty = $request->quantity;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->video_link = $request->video_link;
        $product->tags = $request->tags;
        $product->tax_id = $request->tax;
        $product->is_warranty = $request->is_warranty;
        $product->is_return = $request->is_return;
        $product->return_policy_id = $request->is_return == 1 ? $request->return_policy_id : 0;
        $product->is_undefine = 1;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->seo_title = $request->seo_title ? $request->seo_title : $request->name;
        $product->seo_description = $request->seo_description ? $request->seo_description : $request->name;
        $product->save();

        if($request->is_specification){
            $exist_specifications=[];
            if($request->keys){
                foreach($request->keys as $index => $key){
                    if($key){
                        if($request->specifications[$index]){
                            if(!in_array($key, $exist_specifications)){
                                $productSpecification= new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                            $exist_specifications[] = $key;
                        }
                    }
                }
            }
        }

        $notification = trans('user_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);
    }

    public function show(Product $product)
    {
        //
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if($product->vendor_id == 0){
            $notification = 'Something went wrong';
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('seller.product.index')->with($notification);
        }
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $childCategories = ChildCategory::all();
        $brands = Brand::all();
        $productTaxs = ProductTax::where('status',1)->get();
        $retrunPolicies = ReturnPolicy::where('status',1)->get();
        $specificationKeys = ProductSpecificationKey::all();
        $productSpecifications = ProductSpecification::where('product_id',$product->id)->get();
        $tagArray = json_decode($product->tags);
        $tags = '';
        if($product->tags){
            foreach($tagArray as $index => $tag){
                $tags .= $tag->value.',';
            }
        }

        return view('seller.edit_product',compact('categories','brands','productTaxs','retrunPolicies','specificationKeys','product','subCategories','childCategories','tags','productSpecifications'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $rules = [
            'short_name' => 'required|unique:products,short_name,'.$product->id,
            'name' => 'required|unique:products,name,'.$product->id,
            'slug' => 'required|unique:products,slug,'.$product->id,
            'category' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'brand' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required',
            'tax' => 'required',
            'is_return' => 'required',
            'is_warranty' => 'required',
            'return_policy_id' => $request->is_return == 1 ?  'required' : '',
        ];
        $customMessages = [
            'short_name.required' => trans('user_validation.Short name is required'),
            'short_name.unique' => trans('user_validation.Short name is required'),
            'name.required' => trans('user_validation.Name is required'),
            'name.unique' => trans('user_validation.Name is required'),
            'slug.required' => trans('user_validation.Slug is required'),
            'slug.unique' => trans('user_validation.Slug already exist'),
            'category.required' => trans('user_validation.Category is required'),
            'thumb_image.required' => trans('user_validation.thumbnail is required'),
            'banner_image.required' => trans('user_validation.Banner is required'),
            'short_description.required' => trans('user_validation.Short description is required'),
            'long_description.required' => trans('user_validation.Long description is required'),
            'brand.required' => trans('user_validation.Brand is required'),
            'price.required' => trans('user_validation.Price is required'),
            'quantity.required' => trans('user_validation.Quantity is required'),
            'tax.required' => trans('user_validation.Tax is required'),
            'is_return.required' => trans('user_validation.Return is required'),
            'is_warranty.required' => trans('user_validation.Warranty is required'),
            'return_policy_id.required' => trans('user_validation.Return policy is required'),
            'status.required' => trans('user_validation.Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->thumb_image){
            $old_thumbnail = $product->thumb_image;
            $extention = $request->thumb_image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->thumb_image)
                ->save(public_path().'/'.$image_name);
            $product->thumb_image=$image_name;
            $product->save();
            if($old_thumbnail){
                if(File::exists(public_path().'/'.$old_thumbnail))unlink(public_path().'/'.$old_thumbnail);
            }
        }

        if($request->banner_image){
            $old_banner = $product->banner_image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'product-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $product->banner_image = $banner_name;
            $product->save();
            if($old_banner){
                if(File::exists(public_path().'/'.$old_banner))unlink(public_path().'/'.$old_banner);
            }
        }

        $product->short_name = $request->short_name;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category ? $request->sub_category : 0;
        $product->child_category_id = $request->child_category ? $request->child_category : 0;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->qty = $request->quantity;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->video_link = $request->video_link;
        $product->tags = $request->tags;
        $product->tax_id = $request->tax;
        $product->is_warranty = $request->is_warranty;
        $product->is_return = $request->is_return;
        $product->return_policy_id = $request->is_return == 1 ? $request->return_policy_id : 0;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->seo_title = $request->seo_title ? $request->seo_title : $request->name;
        $product->seo_description = $request->seo_description ? $request->seo_description : $request->name;
        $product->save();

        $exist_specifications=[];
        if($request->keys){
            foreach($request->keys as $index => $key){
                if($key){
                    if($request->specifications[$index]){
                        if(!in_array($key, $exist_specifications)){
                            $existSroductSpecification = ProductSpecification::where(['product_id' => $product->id,'product_specification_key_id' => $key])->first();
                            if($existSroductSpecification){
                                $existSroductSpecification->specification = $request->specifications[$index];
                                $existSroductSpecification->save();
                            }else{
                                $productSpecification = new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                        }
                        $exist_specifications[] = $key;
                    }
                }
            }
        }

        $notification = trans('user_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $gallery = $product->gallery;
        $old_thumbnail = $product->thumb_image;
        $product->delete();
        if($old_thumbnail){
            if(File::exists(public_path().'/'.$old_thumbnail))unlink(public_path().'/'.$old_thumbnail);
        }
        foreach($gallery as $image){
            $old_image = $image->image;
            $image->delete();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }
        ProductVariant::where('product_id',$id)->delete();
        ProductVariantItem::where('product_id',$id)->delete();
        CampaignProduct::where('product_id',$id)->delete();
        ProductReport::where('product_id',$id)->delete();
        ProductReview::where('product_id',$id)->delete();
        ProductSpecification::where('product_id',$id)->delete();
        Wishlist::where('product_id',$id)->delete();

        $notification = trans('user_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $product = Product::find($id);
        if($product->status == 1){
            $product->status = 0;
            $product->save();
            $message = trans('user_validation.Inactive Successfully');
        }else{
            $product->status = 1;
            $product->save();
            $message = trans('user_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function removedProductExistSpecification($id){
        $productSpecification = ProductSpecification::find($id);
        $productSpecification->delete();
        $message = trans('user_validation.Removed Successfully');
        return response()->json($message);
    }


    public function productHighlight($id){
        $product = Product::find($id);
        return view('seller.product_highlight', compact('product'));
    }

    public function productHighlightUpdate(Request $request,$id){

        $product = Product::find($id);
        if($request->product_type == 1){
            $product->is_undefine = 1;
            $product->new_product = 0;
            $product->is_featured = 0;
            $product->is_best = 0;
            $product->is_top = 0;
            $product->is_flash_deal = 0;
            $product->save();
        }else if($request->product_type == 2){
            $product->is_undefine = 0;
            $product->new_product = 1;
            $product->is_featured = 0;
            $product->is_best = 0;
            $product->is_top = 0;
            $product->is_flash_deal = 0;
            $product->save();
        }else if($request->product_type == 3){
            $product->is_undefine = 0;
            $product->new_product = 0;
            $product->is_featured = 1;
            $product->is_best = 0;
            $product->is_top = 0;
            $product->is_flash_deal = 0;
            $product->save();
        }else if($request->product_type == 4){
            $product->is_undefine = 0;
            $product->new_product = 0;
            $product->is_featured = 0;
            $product->is_best = 0;
            $product->is_top = 1;
            $product->is_flash_deal = 0;
            $product->save();
        }else if($request->product_type == 5){
            $product->is_undefine = 0;
            $product->new_product = 0;
            $product->is_featured = 0;
            $product->is_best = 1;
            $product->is_top = 0;
            $product->is_flash_deal = 0;
            $product->save();
        }else if($request->product_type == 6){
            $rules = [
                'date' => 'required'
            ];

            $customMessages = [
                'date.required' => trans('user_validation.Date is required'),
            ];
            $this->validate($request, $rules, $customMessages);
            $product->is_flash_deal = 1;
            $product->flash_deal_date = $request->date;
            $product->is_undefine = 0;
            $product->new_product = 0;
            $product->is_featured = 0;
            $product->is_best = 0;
            $product->is_top = 0;
            $product->save();
        }

        $notification = trans('user_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);
    }

}
