<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\ShopPage;
use Image;
use File;
class AdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $megaMenuBanner = BannerImage::whereId('1')->first();
        $oneColumnBanner = BannerImage::whereId('2')->first();
        $firstTwoColumnBannerOne = BannerImage::whereId('3')->first();
        $firstTwoColumnBannerTwo = BannerImage::whereId('4')->first();
        $secondTwoColumnBannerOne = BannerImage::whereId('5')->first();
        $secondTwoColumnBannerTwo = BannerImage::whereId('6')->first();
        $thirdTwoColumnBannerOne = BannerImage::whereId('7')->first();
        $thirdTwoColumnBannerTwo = BannerImage::whereId('8')->first();
        $shop_page = ShopPage::first();
        $banner = BannerImage::whereId('14')->first();
        $shoppingCartBannerOne = BannerImage::whereId('9')->first();
        $shoppingCartBannerTwo = BannerImage::whereId('10')->first();
        $campaignBannerOne = BannerImage::whereId('11')->first();
        $campaignBannerTwo = BannerImage::whereId('12')->first();

        return view('admin.advertisement', compact('megaMenuBanner','oneColumnBanner','firstTwoColumnBannerOne','firstTwoColumnBannerTwo','secondTwoColumnBannerOne','secondTwoColumnBannerTwo','thirdTwoColumnBannerOne','thirdTwoColumnBannerTwo','shop_page','banner','shoppingCartBannerOne','shoppingCartBannerTwo','campaignBannerOne','campaignBannerTwo'));
    }

    public function megaMenuBannerUpdate(Request $request){
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'button_link' => 'required',
            'button_text' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'button_link.required' => trans('admin_validation.Button link is required'),
            'button_text.required' => trans('admin_validation.Button text is required'),
            'status.required' => trans('admin_validation.Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $megaMenuBanner = BannerImage::whereId('1')->first();

        if($request->banner_image){
            $existing_banner = $megaMenuBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $megaMenuBanner->image = $banner_name;
            $megaMenuBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }

        $megaMenuBanner->title = $request->title;
        $megaMenuBanner->description = $request->description;
        $megaMenuBanner->link = $request->button_link;
        $megaMenuBanner->button_text = $request->button_text;
        $megaMenuBanner->status = $request->status;
        $megaMenuBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function updateHomePageOneColumnBanner(Request $request){
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'button_link' => 'required',
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'button_link.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $oneColumnBanner = BannerImage::whereId('2')->first();

        if($request->banner_image){
            $existing_banner = $oneColumnBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'banner-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $oneColumnBanner->image = $banner_name;
            $oneColumnBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }

        $oneColumnBanner->title = $request->title;
        $oneColumnBanner->description = $request->description;
        $oneColumnBanner->link = $request->button_link;
        $oneColumnBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateHomePageFirstTwoColumnBanner(Request $request){
        $rules = [
            'title_one' => 'required',
            'description_one' => 'required',
            'button_link_one' => 'required',
            'title_two' => 'required',
            'description_two' => 'required',
            'button_link_two' => 'required',

        ];
        $customMessages = [
            'title_one.required' => trans('admin_validation.Title is required'),
            'description_one.required' => trans('admin_validation.Description is required'),
            'button_link_one.required' => trans('admin_validation.Button link is required'),
            'title_two.required' => trans('admin_validation.Title is required'),
            'description_two.required' => trans('admin_validation.Description is required'),
            'button_link_two.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $firstTwoColumnBannerOne = BannerImage::whereId('3')->first();
        if($request->banner_image_one){
            $existing_banner = $firstTwoColumnBannerOne->image;
            $extention = $request->banner_image_one->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_one)
                ->save(public_path().'/'.$banner_name);
            $firstTwoColumnBannerOne->image = $banner_name;
            $firstTwoColumnBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $firstTwoColumnBannerOne->title = $request->title_one;
        $firstTwoColumnBannerOne->description = $request->description_one;
        $firstTwoColumnBannerOne->link = $request->button_link_one;
        $firstTwoColumnBannerOne->save();


        $firstTwoColumnBannerTwo = BannerImage::whereId('4')->first();
        if($request->banner_image_two){
            $existing_banner = $firstTwoColumnBannerTwo->image;
            $extention = $request->banner_image_two->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_two)
                ->save(public_path().'/'.$banner_name);
            $firstTwoColumnBannerTwo->image = $banner_name;
            $firstTwoColumnBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $firstTwoColumnBannerTwo->title = $request->title_two;
        $firstTwoColumnBannerTwo->description = $request->description_two;
        $firstTwoColumnBannerTwo->link = $request->button_link_two;
        $firstTwoColumnBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateHomePageSecondTwoColumnBanner(Request $request){
        $rules = [
            'title_one' => 'required',
            'description_one' => 'required',
            'button_link_one' => 'required',
            'title_two' => 'required',
            'description_two' => 'required',
            'button_link_two' => 'required',

        ];
        $customMessages = [
            'title_one.required' => trans('admin_validation.Title is required'),
            'description_one.required' => trans('admin_validation.Description is required'),
            'button_link_one.required' => trans('admin_validation.Button link is required'),
            'title_two.required' => trans('admin_validation.Title is required'),
            'description_two.required' => trans('admin_validation.Description is required'),
            'button_link_two.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $secondTwoColumnBannerOne = BannerImage::whereId('5')->first();
        if($request->banner_image_one){
            $existing_banner = $secondTwoColumnBannerOne->image;
            $extention = $request->banner_image_one->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_one)
                ->save(public_path().'/'.$banner_name);
            $secondTwoColumnBannerOne->image = $banner_name;
            $secondTwoColumnBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $secondTwoColumnBannerOne->title = $request->title_one;
        $secondTwoColumnBannerOne->description = $request->description_one;
        $secondTwoColumnBannerOne->link = $request->button_link_one;
        $secondTwoColumnBannerOne->save();


        $secondTwoColumnBannerTwo = BannerImage::whereId('6')->first();
        if($request->banner_image_two){
            $existing_banner = $secondTwoColumnBannerTwo->image;
            $extention = $request->banner_image_two->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_two)
                ->save(public_path().'/'.$banner_name);
            $secondTwoColumnBannerTwo->image = $banner_name;
            $secondTwoColumnBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $secondTwoColumnBannerTwo->title = $request->title_two;
        $secondTwoColumnBannerTwo->description = $request->description_two;
        $secondTwoColumnBannerTwo->link = $request->button_link_two;
        $secondTwoColumnBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateHomePageThirdTwoColumnBanner(Request $request){
        $rules = [
            'title_one' => 'required',
            'description_one' => 'required',
            'button_link_one' => 'required',
            'title_two' => 'required',
            'description_two' => 'required',
            'button_link_two' => 'required',

        ];
        $customMessages = [
            'title_one.required' => trans('admin_validation.Title is required'),
            'description_one.required' => trans('admin_validation.Description is required'),
            'button_link_one.required' => trans('admin_validation.Button link is required'),
            'title_two.required' => trans('admin_validation.Title is required'),
            'description_two.required' => trans('admin_validation.Description is required'),
            'button_link_two.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $thirdTwoColumnBannerOne = BannerImage::whereId('7')->first();
        if($request->banner_image_one){
            $existing_banner = $thirdTwoColumnBannerOne->image;
            $extention = $request->banner_image_one->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_one)
                ->save(public_path().'/'.$banner_name);
            $thirdTwoColumnBannerOne->image = $banner_name;
            $thirdTwoColumnBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $thirdTwoColumnBannerOne->title = $request->title_one;
        $thirdTwoColumnBannerOne->description = $request->description_one;
        $thirdTwoColumnBannerOne->link = $request->button_link_one;
        $thirdTwoColumnBannerOne->save();


        $thirdTwoColumnBannerTwo = BannerImage::whereId('8')->first();
        if($request->banner_image_two){
            $existing_banner = $thirdTwoColumnBannerTwo->image;
            $extention = $request->banner_image_two->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_two)
                ->save(public_path().'/'.$banner_name);
            $thirdTwoColumnBannerTwo->image = $banner_name;
            $thirdTwoColumnBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $thirdTwoColumnBannerTwo->title = $request->title_two;
        $thirdTwoColumnBannerTwo->description = $request->description_two;
        $thirdTwoColumnBannerTwo->link = $request->button_link_two;
        $thirdTwoColumnBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateShopPage(Request $request){
        $rules = [
            'header_one' => 'required',
            'header_two' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'link' => 'required',
            'button_text' => 'required',
        ];
        $customMessages = [
            'header_one.required' => trans('admin_validation.Header one is required'),
            'header_two.required' => trans('admin_validation.Header two is required'),
            'title_one.required' => trans('admin_validation.Title one is required'),
            'title_two.required' => trans('admin_validation.Title two is required'),
            'link.required' => trans('admin_validation.Link is required'),
            'button_text.required' => trans('admin_validation.Button text is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shop_page = ShopPage::first();

        if($request->banner){
            $existing_banner = $shop_page->banner;
            $extention = $request->banner->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner)
                ->save(public_path().'/'.$banner_name);
            $shop_page->banner = $banner_name;
            $shop_page->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $shop_page->header_one = $request->header_one;
        $shop_page->header_two = $request->header_two;
        $shop_page->title_one = $request->title_one;
        $shop_page->title_two = $request->title_two;
        $shop_page->link = $request->link;
        $shop_page->button_text = $request->button_text;
        $shop_page->status = $request->status ? 1 : 0;
        $shop_page->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateProductDetailBanner(Request $request){
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'button_link' => 'required',
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'button_link.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $banner = BannerImage::whereId('14')->first();

        if($request->banner){
            $existing_banner = $banner->image;
            $extention = $request->banner->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner)
                ->save(public_path().'/'.$banner_name);
            $banner->image = $banner_name;
            $banner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link = $request->button_link;
        $banner->status = $request->status ? 1 : 0;
        $banner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateShoppingCartBottomBanner(Request $request){
        $rules = [
            'title_one' => 'required',
            'description_one' => 'required',
            'button_link_one' => 'required',
            'title_two' => 'required',
            'description_two' => 'required',
            'button_link_two' => 'required',

        ];
        $customMessages = [
            'title_one.required' => trans('admin_validation.Title is required'),
            'description_one.required' => trans('admin_validation.Description is required'),
            'button_link_one.required' => trans('admin_validation.Button link is required'),
            'title_two.required' => trans('admin_validation.Title is required'),
            'description_two.required' => trans('admin_validation.Description is required'),
            'button_link_two.required' => trans('admin_validation.Button link is required'),

        ];
        $this->validate($request, $rules,$customMessages);

        $bannerOne = BannerImage::whereId('9')->first();

        if($request->banner_image_one){
            $existing_banner = $bannerOne->image;
            $extention = $request->banner_image_one->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_one)
                ->save(public_path().'/'.$banner_name);
            $bannerOne->image = $banner_name;
            $bannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $bannerOne->title = $request->title_one;
        $bannerOne->description = $request->description_one;
        $bannerOne->link = $request->button_link_one;
        $bannerOne->status = $request->status ? 1 : 0;;
        $bannerOne->save();


        $bannerTwo = BannerImage::whereId('10')->first();
        if($request->banner_image_two){
            $existing_banner = $bannerTwo->image;
            $extention = $request->banner_image_two->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_two)
                ->save(public_path().'/'.$banner_name);
            $bannerTwo->image = $banner_name;
            $bannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $bannerTwo->title = $request->title_two;
        $bannerTwo->description = $request->description_two;
        $bannerTwo->link = $request->button_link_two;
        $bannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateCampaignPageBanner(Request $request){
        $rules = [
            'title_one' => 'required',
            'description_one' => 'required',
            'button_link_one' => 'required',
            'title_two' => 'required',
            'description_two' => 'required',
            'button_link_two' => 'required',

        ];
        $customMessages = [
            'title_one.required' => trans('admin_validation.Title is required'),
            'description_one.required' => trans('admin_validation.Description is required'),
            'button_link_one.required' => trans('admin_validation.Button link is required'),
            'title_two.required' => trans('admin_validation.Title is required'),
            'description_two.required' => trans('admin_validation.Description is required'),
            'button_link_two.required' => trans('admin_validation.Button link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $bannerOne = BannerImage::whereId('11')->first();

        if($request->banner_image_one){
            $existing_banner = $bannerOne->image;
            $extention = $request->banner_image_one->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_one)
                ->save(public_path().'/'.$banner_name);
            $bannerOne->image = $banner_name;
            $bannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $bannerOne->title = $request->title_one;
        $bannerOne->description = $request->description_one;
        $bannerOne->link = $request->button_link_one;
        $bannerOne->status = $request->status ? 1 : 0;;
        $bannerOne->save();


        $bannerTwo = BannerImage::whereId('12')->first();
        if($request->banner_image_two){
            $existing_banner = $bannerTwo->image;
            $extention = $request->banner_image_two->getClientOriginalExtension();
            $banner_name = 'banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image_two)
                ->save(public_path().'/'.$banner_name);
            $bannerTwo->image = $banner_name;
            $bannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $bannerTwo->title = $request->title_two;
        $bannerTwo->description = $request->description_two;
        $bannerTwo->link = $request->button_link_two;
        $bannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
