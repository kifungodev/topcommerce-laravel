@extends('layout')
@section('title')
    <title>{{ $seoSetting->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $seoSetting->seo_description }}">
@endsection

@section('public-content')
    <!--============================
        BANNER PART START
    ==============================-->
    @php
        $sliderVisibility = $visibilities->where('id',1)->first();
    @endphp
    @if ($sliderVisibility->status == 1)
    <section id="wsus__banner">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                    <ul class="wsus_menu_cat_item">
                        @foreach ($productCategories as $productCategory)
                            @if ($productCategory->subCategories->count() == 0)
                                <li><a href="{{ route('product',['category' => $productCategory->slug]) }}"><i class="{{ $productCategory->icon }}"></i> {{ $productCategory->name }}</a></li>
                            @else
                                <li><a class="wsus__droap_arrow" href="{{ route('product',['category' => $productCategory->slug]) }}"><i class="{{ $productCategory->icon }}"></i> {{ $productCategory->name }} </a>
                                    <ul class="wsus_menu_cat_droapdown">
                                        @foreach ($productCategory->subCategories as $subCategory)
                                            @if ($subCategory->childCategories->count() == 0)
                                                <li><a href="{{ route('product',['sub_category' => $subCategory->slug]) }}">{{ $subCategory->name }}</a></li>
                                            @else
                                                <li><a href="{{ route('product',['sub_category' => $subCategory->slug]) }}">{{ $subCategory->name }} <i class="fas fa-angle-right"></i></a>
                                                    <ul class="wsus__sub_category">
                                                        @foreach ($subCategory->childCategories as $childCategory)
                                                            <li><a href="{{ route('product',['child_category' => $childCategory->slug]) }}">{{ $childCategory->name }}</a> </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif

                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="col-xl-9 col-lg-9">
                    <div class="wsus__banner_content">
                        <div class="row banner_slider">
                            @foreach ($sliders->take($sliderVisibility->qty) as $slider)
                            <div class="col-xl-12">
                                <div class="wsus__single_slider" style="background: url({{ asset($slider->image) }});">
                                    <div class="wsus__single_slider_text">
                                        <h1>{!! nl2br($slider->title) !!}</h1>
                                        <h6>{!! nl2br($slider->description) !!}</h6>
                                        <a class="common_btn" href="{{ $slider->link }}">{{__('user.shop now')}}</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--============================
        BANNER PART END
    ==============================-->

    <!--============================
        BRAND SLIDER START
    ==============================-->
    @php
        $brandVisibility = $visibilities->where('id',2)->first();
    @endphp
    @if ($brandVisibility->status == 1)
        <section id="wsus__brand_sleder">
            <div class="container">
                <div class="brand_border">
                    <div class="row brand_slider">
                        @foreach ($brands->take($brandVisibility->qty) as $brand)
                        <div class="col-xl-2">
                            <div class="wsus__brand_logo">
                                <a href="{{ route('product',['brand' => $brand->slug]) }}"><img src="{{ asset($brand->logo) }}" alt="brand" class="img-fluid w-100"></a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!--============================
        BRAND SLIDER END
    ==============================-->


    <!--============================
        FLASH SELL START
    ==============================-->
    @php
        $campaignVisibility = $visibilities->where('id',3)->first();
    @endphp
    @if ($campaignVisibility->status == 1)
        <section id="wsus__flash_sell">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 offer_time">
                        @if ($campaign)
                        @php
                            $end = strtotime($campaign->end_date);
                            $current_time=  Carbon\Carbon::now()->timestamp;
                            $capmaign_time = $end - $current_time;

                            if(env('APP_VERSION') == 0){
                                $demo_end = Carbon\Carbon::now()->addDays(3);
                                $demo_end = $demo_end->format('Y-m-d H:i:s');
                                $end = strtotime($demo_end);
                                $capmaign_time = $end - $current_time;
                             }
                        @endphp
                        <script>
                            var capmaign_time = {{ $capmaign_time }};
                        </script>
                        <div class="wsus__flash_coundown">
                            <span class="end_text">{{ $campaign->name }}</span>
                            <div id="countdown17" class="ClassyCountdownDemo"></div>
                            <a class="common_btn" href="{{ route('campaign-detail', $campaign->slug) }}">{{__('user.see more')}} <i class="fas fa-caret-right"></i></a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row flash_sell_slider">
                    @if ($campaignProducts != null)
                        @foreach ($campaignProducts->take($campaignVisibility->qty) as $campaignProduct)
                        <div class="col-xl-3 col-sm-6 col-lg-4">
                            <div class="wsus__product_item">
                                @if ($campaignProduct->product->new_product == 1)
                                    <span class="wsus__new">{{__('user.New')}}</span>
                                @elseif ($campaignProduct->product->is_featured == 1)
                                    <span class="wsus__new">{{__('user.Featured')}}</span>
                                @elseif ($campaignProduct->product->is_top == 1)
                                    <span class="wsus__new">{{__('user.Top')}}</span>
                                @elseif ($campaignProduct->product->is_best == 1)
                                    <span class="wsus__new">{{__('user.Best')}}</span>
                                @endif

                                @php
                                    $variantPrice = 0;
                                    $variants = $campaignProduct->product->variants->where('status', 1);
                                    if($variants->count() != 0){
                                        foreach ($variants as $variants_key => $variant) {
                                            if($variant->variantItems->where('status',1)->count() != 0){
                                                $item = $variant->variantItems->where('is_default',1)->first();
                                                if($item){
                                                    $variantPrice += $item->price;
                                                }
                                            }
                                        }
                                    }

                                    $isCampaign = false;
                                    $today = date('Y-m-d H:i:s');
                                    $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $campaignProduct->product->id])->first();
                                    if($campaign){
                                        $campaign = $campaign->campaign;
                                        if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                            $isCampaign = true;
                                        }
                                        $campaignOffer = $campaign->offer;
                                        $productPrice = $campaignProduct->product->price;
                                        $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                        $totalPrice = $campaignProduct->product->price;
                                    }

                                    $totalPrice = $campaignProduct->product->price;
                                    if($campaignProduct->product->offer_price != null){
                                        $offerPrice = $campaignProduct->product->offer_price;
                                        $offer = $totalPrice - $offerPrice;
                                        $percentage = ($offer * 100) / $totalPrice;
                                        $percentage = round($percentage);
                                    }
                                @endphp

                                @if ($isCampaign)
                                    <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                                @else
                                    @if ($campaignProduct->product->offer_price != null)
                                        <span class="wsus__minus">-{{ $percentage }}%</span>
                                    @endif
                                @endif

                                <a class="wsus__pro_link" href="{{ route('product-detail',$campaignProduct->product->slug) }}">
                                    <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                    <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                                </a>
                                <ul class="wsus__single_pro_icon">
                                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#productModalView-{{ $campaignProduct->product->id }}"><i
                                                class="far fa-eye"></i></a></li>
                                    <li><a href="javascript:;" onclick="addToWishlist('{{ $campaignProduct->product->id }}')"><i class="far fa-heart"></i></a></li>
                                    <li><a href="javascript:;" onclick="addToCompare('{{ $campaignProduct->product->id }}')"><i class="far fa-random"></i></a>
                                    </li>
                                </ul>
                                <div class="wsus__product_details">
                                    <a class="wsus__category" href="{{ route('product',['category' => $campaignProduct->product->category->slug]) }}">{{ $campaignProduct->product->category->name }} </a>
                                    @php
                                        $reviewQty = $campaignProduct->product->reviews->where('status',1)->count();
                                        $totalReview = $campaignProduct->product->reviews->where('status',1)->sum('rating');

                                        if ($reviewQty > 0) {
                                            $average = $totalReview / $reviewQty;

                                            $intAverage = intval($average);

                                            $nextValue = $intAverage + 1;
                                            $reviewPoint = $intAverage;
                                            $halfReview=false;
                                            if($intAverage < $average && $average < $nextValue){
                                                $reviewPoint= $intAverage + 0.5;
                                                $halfReview=true;
                                            }
                                        }
                                    @endphp

                                    @if ($reviewQty > 0)
                                        <p class="wsus__pro_rating">
                                            @for ($i = 1; $i <=5; $i++)
                                                @if ($i <= $reviewPoint)
                                                    <i class="fas fa-star"></i>
                                                @elseif ($i> $reviewPoint )
                                                    @if ($halfReview==true)
                                                    <i class="fas fa-star-half-alt"></i>
                                                        @php
                                                            $halfReview=false
                                                        @endphp
                                                    @else
                                                    <i class="fal fa-star"></i>
                                                    @endif
                                                @endif
                                            @endfor
                                            <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                        </p>
                                    @endif

                                    @if ($reviewQty == 0)
                                        <p class="wsus__pro_rating">
                                            <i class="fal fa-star"></i>
                                            <i class="fal fa-star"></i>
                                            <i class="fal fa-star"></i>
                                            <i class="fal fa-star"></i>
                                            <i class="fal fa-star"></i>
                                            <span>(0 {{__('user.review')}})</span>
                                        </p>
                                    @endif
                                    <a class="wsus__pro_name" href="{{ route('product-detail', $campaignProduct->product->slug) }}">{{ $campaignProduct->product->short_name }}</a>

                                    @if ($isCampaign)
                                        <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @else
                                        @if ($campaignProduct->product->offer_price == null)
                                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                        @else
                                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignProduct->product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @endif
                                    @endif

                                    <a class="add_cart" onclick="addToCartMainProduct('{{ $campaignProduct->product->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    @endif
    <!--============================
        FLASH SELL END
    ==============================-->


    <!--============================
       MONTHLY TOP PRODUCT START
    ==============================-->
 @php
    $popularCategoryVisible = $visibilities->where('id',4)->first();
@endphp
@if ($popularCategoryVisible->status == 1)
    <section id="wsus__monthly_top">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__section_header for_md">
                        <h3>{{ $popularCategory->title }}</h3>
                        <div class="monthly_top_filter">
                            <button class=" active click_first_cat" data-filter=".first_cat">{{ $firstCategory ? $firstCategory->name : '' }}</button>
                            <button data-filter=".second_cat">{{ $secondCategory ? $secondCategory->name : ''}}</button>
                            <button data-filter=".third_cat">{{ $thirdCategory ? $thirdCategory->name : ''}}</button>
                            <button data-filter=".fourth_cat">{{ $fourthCategory ? $fourthCategory->name : ''}}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="wsus__monthly_top_banner">
                        <div class="wsus__monthly_top_banner_img">
                            <img src="{{ asset($oneColumnBanner->image) }}" alt="img" class="img-fluid w-100">
                            <span></span>
                        </div>
                        <div class="wsus__monthly_top_banner_text">
                            <h3>{{ $oneColumnBanner->title }}</h3>
                            <H6>{{ $oneColumnBanner->description }}</H6>
                            <a class="shop_btn" href="{{ $oneColumnBanner->link }}">{{__('user.shop now')}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="row grid">
                        @foreach ($firstCategoryproducts as $firstCategoryproduct)
                            <div class="col-xl-4 col-sm-6  first_cat">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $firstCategoryproduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ $firstCategoryproduct->thumb_image }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    @php
                                        $reviewQty = $firstCategoryproduct->reviews->where('status',1)->count();
                                        $totalReview = $firstCategoryproduct->reviews->where('status',1)->sum('rating');

                                        if ($reviewQty > 0) {
                                            $average = $totalReview / $reviewQty;

                                            $intAverage = intval($average);

                                            $nextValue = $intAverage + 1;
                                            $reviewPoint = $intAverage;
                                            $halfReview=false;
                                            if($intAverage < $average && $average < $nextValue){
                                                $reviewPoint= $intAverage + 0.5;
                                                $halfReview=true;
                                            }
                                        }
                                    @endphp

                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $firstCategoryproduct->short_name }}</h5>

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $firstCategoryproduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }
                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $firstCategoryproduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $firstCategoryproduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $firstCategoryproduct->price;
                                            }

                                            $totalPrice = $firstCategoryproduct->price;
                                            if($firstCategoryproduct->offer_price != null){
                                                $offerPrice = $firstCategoryproduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp
                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($firstCategoryproduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $firstCategoryproduct->offer_price + $variantPrice) }} <del>{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        @foreach ($secondCategoryproducts as $secondCategoryproduct)
                            <div class="col-xl-4 col-sm-6  second_cat">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $secondCategoryproduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ $secondCategoryproduct->thumb_image }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    @php
                                        $reviewQty = $secondCategoryproduct->reviews->where('status',1)->count();
                                        $totalReview = $secondCategoryproduct->reviews->where('status',1)->sum('rating');

                                        if ($reviewQty > 0) {
                                            $average = $totalReview / $reviewQty;

                                            $intAverage = intval($average);

                                            $nextValue = $intAverage + 1;
                                            $reviewPoint = $intAverage;
                                            $halfReview=false;
                                            if($intAverage < $average && $average < $nextValue){
                                                $reviewPoint= $intAverage + 0.5;
                                                $halfReview=true;
                                            }
                                        }
                                    @endphp

                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $secondCategoryproduct->short_name }}</h5>

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $secondCategoryproduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }
                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $secondCategoryproduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $secondCategoryproduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $secondCategoryproduct->price;
                                            }

                                            $totalPrice = $secondCategoryproduct->price;
                                            if($secondCategoryproduct->offer_price != null){
                                                $offerPrice = $secondCategoryproduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp
                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($secondCategoryproduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $secondCategoryproduct->offer_price + $variantPrice) }} <del>{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        @foreach ($thirdCategoryproducts as $thirdCategoryproduct)
                            <div class="col-xl-4 col-sm-6  third_cat">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $thirdCategoryproduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ $thirdCategoryproduct->thumb_image }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    @php
                                        $reviewQty = $thirdCategoryproduct->reviews->where('status',1)->count();
                                        $totalReview = $thirdCategoryproduct->reviews->where('status',1)->sum('rating');

                                        if ($reviewQty > 0) {
                                            $average = $totalReview / $reviewQty;

                                            $intAverage = intval($average);

                                            $nextValue = $intAverage + 1;
                                            $reviewPoint = $intAverage;
                                            $halfReview=false;
                                            if($intAverage < $average && $average < $nextValue){
                                                $reviewPoint= $intAverage + 0.5;
                                                $halfReview=true;
                                            }
                                        }
                                    @endphp

                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $thirdCategoryproduct->short_name }}</h5>

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $thirdCategoryproduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }
                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $thirdCategoryproduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $thirdCategoryproduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $thirdCategoryproduct->price;
                                            }

                                            $totalPrice = $thirdCategoryproduct->price;
                                            if($thirdCategoryproduct->offer_price != null){
                                                $offerPrice = $thirdCategoryproduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp
                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $totalPrice }}</del></p>
                                        @else
                                            @if ($thirdCategoryproduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $thirdCategoryproduct->offer_price + $variantPrice) }} <del>{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        @foreach ($fourthCategoryproducts as $fourthCategoryproduct)
                            <div class="col-xl-4 col-sm-6  fourth_cat">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $fourthCategoryproduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ $fourthCategoryproduct->thumb_image }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    @php
                                        $reviewQty = $fourthCategoryproduct->reviews->where('status',1)->count();
                                        $totalReview = $fourthCategoryproduct->reviews->where('status',1)->sum('rating');

                                        if ($reviewQty > 0) {
                                            $average = $totalReview / $reviewQty;

                                            $intAverage = intval($average);

                                            $nextValue = $intAverage + 1;
                                            $reviewPoint = $intAverage;
                                            $halfReview=false;
                                            if($intAverage < $average && $average < $nextValue){
                                                $reviewPoint= $intAverage + 0.5;
                                                $halfReview=true;
                                            }
                                        }
                                    @endphp

                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $fourthCategoryproduct->short_name }}</h5>

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $fourthCategoryproduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }
                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $fourthCategoryproduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $fourthCategoryproduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $fourthCategoryproduct->price;
                                            }

                                            $totalPrice = $fourthCategoryproduct->price;
                                            if($fourthCategoryproduct->offer_price != null){
                                                $offerPrice = $fourthCategoryproduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp
                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{  sprintf("%.2f", $totalPrice ) }}</del></p>
                                        @else
                                            @if ($fourthCategoryproduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice )  }}</p>
                                            @else
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $fourthCategoryproduct->offer_price + $variantPrice ) }} <del>{{  sprintf("%.2f", $totalPrice ) }}</del></p>
                                            @endif
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endif
    <!--============================
       MONTHLY TOP PRODUCT END
    ==============================-->


    <!--============================
        SINGLE BANNER START
    ==============================-->
    @php
        $bannerVisibility = $visibilities->where('id',5)->first();
    @endphp
    @if ($bannerVisibility->status == 1)
    <section id="wsus__single_banner">
        <div class="container">
            <div class="row">
                @php
                    $bannerOne = $banners->where('id',3)->first();
                    $bannerTwo = $banners->where('id',4)->first();
                @endphp
                <div class="col-xl-6 col-lg-6">
                    <div class="wsus__single_banner_content">
                        <div class="wsus__single_banner_img">
                            <img src="{{ asset($bannerOne->image) }}" alt="banner" class="img-fluid w-100">
                        </div>
                        <div class="wsus__single_banner_text">
                            <h6>{{ $bannerOne->description }}</h6>
                            <h3>{{ $bannerOne->title }}</h3>
                            <a class="shop_btn" href="{{ $bannerOne->link }}">{{__('user.shop now')}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="wsus__single_banner_content">
                        <div class="wsus__single_banner_img">
                            <img src="{{ asset($bannerTwo->image) }}" alt="banner" class="img-fluid w-100">
                        </div>
                        <div class="wsus__single_banner_text">
                            <h6>{{ $bannerTwo->description }}</h6>
                            <h3>{{ $bannerTwo->title }}</h3>
                            <a class="shop_btn" href="{{ $bannerTwo->link }}">{{__('user.shop now')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--============================
        SINGLE BANNER END
    ==============================-->


    <!--============================
           HOT DEALS START
    ==============================-->
    <section id="wsus__hot_deals">
        <div class="container">
            @php
                $flashDealVisibility = $visibilities->where('id',6)->first();
                $productIds = [];
                $productYears = [];
                $productMonths = [];
                $productDays = [];

                if(env('APP_VERSION') == 0){
                    $demo_end = Carbon\Carbon::now()->addDays(3);
                    foreach ($flashDealProducts as $key => $flashDealProduct) {
                        $productIds[] = $flashDealProduct->id;
                        $productYears[] = $demo_end->format('Y');
                        $productMonths[] = $demo_end->format('m');;
                        $productDays[] = $demo_end->format('d');
                    }
                }else {
                    foreach ($flashDealProducts as $key => $flashDealProduct) {
                        $productIds[] = $flashDealProduct->id;
                        $productYears[] = date('Y', strtotime($flashDealProduct->flash_deal_date));
                        $productMonths[] = date('m', strtotime($flashDealProduct->flash_deal_date));
                        $productDays[] = date('d', strtotime($flashDealProduct->flash_deal_date));
                    }
                }

            @endphp
            <script>
                var productIds = <?= json_encode($productIds)?>;
                var productYears = <?= json_encode($productYears)?>;
                var productMonths = <?= json_encode($productMonths)?>;
                var productDays = <?= json_encode($productDays)?>;
            </script>
            @if ($flashDealVisibility->status == 1)
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__section_header">
                        <h3>{{__('user.Flash Deal')}}</h3>
                    </div>
                </div>
            </div>
            <div class="row hot_deals_slider">
                @foreach ($flashDealProducts->take($flashDealVisibility->qty) as $flashDealProduct)
                <div class="col-xl-6 col-lg-6">
                    <div class="wsus__hot_deals_offer">
                        <div class="wsus__hot_deals_img">
                            <img src="{{ $flashDealProduct->thumb_image }}" alt="mobile" class="img-fluid w-100">
                            <div class="simply-countdown flash-deal-product-{{ $flashDealProduct->id }}"></div>
                        </div>
                        <div class="wsus__hot_deals_text">
                            <a class="wsus__hot_title" href="{{ route('product-detail', $flashDealProduct->slug) }}">{{ $flashDealProduct->short_name }}</a>
                            @php
                                $reviewQty = $flashDealProduct->reviews->where('status',1)->count();
                                $totalReview = $flashDealProduct->reviews->where('status',1)->sum('rating');
                                if ($reviewQty > 0) {
                                    $average = $totalReview / $reviewQty;
                                    $intAverage = intval($average);
                                    $nextValue = $intAverage + 1;
                                    $reviewPoint = $intAverage;
                                    $halfReview=false;
                                    if($intAverage < $average && $average < $nextValue){
                                        $reviewPoint= $intAverage + 0.5;
                                        $halfReview=true;
                                    }
                                }
                            @endphp

                            @if ($reviewQty > 0)
                                <p class="wsus__rating">
                                    @for ($i = 1; $i <=5; $i++)
                                        @if ($i <= $reviewPoint)
                                            <i class="fas fa-star"></i>
                                        @elseif ($i> $reviewPoint )
                                            @if ($halfReview==true)
                                            <i class="fas fa-star-half-alt"></i>
                                                @php
                                                    $halfReview=false
                                                @endphp
                                            @else
                                            <i class="fal fa-star"></i>
                                            @endif
                                        @endif
                                    @endfor
                                    <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                </p>
                            @endif

                            @if ($reviewQty == 0)
                                <p class="wsus__rating">
                                    <i class="fal fa-star"></i>
                                    <i class="fal fa-star"></i>
                                    <i class="fal fa-star"></i>
                                    <i class="fal fa-star"></i>
                                    <i class="fal fa-star"></i>
                                    <span>(0 {{__('user.review')}})</span>
                                </p>
                            @endif

                            @php
                                $variantPrice = 0;
                                $variants = $flashDealProduct->variants->where('status', 1);
                                if($variants->count() != 0){
                                    foreach ($variants as $variants_key => $variant) {
                                        if($variant->variantItems->where('status',1)->count() != 0){
                                            $item = $variant->variantItems->where('is_default',1)->first();
                                            if($item){
                                                $variantPrice += $item->price;
                                            }
                                        }
                                    }
                                }

                                $isCampaign = false;
                                $today = date('Y-m-d H:i:s');

                                $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $flashDealProduct->id])->first();
                                if($campaign){
                                    $campaign = $campaign->campaign;
                                    if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                        $isCampaign = true;
                                    }
                                    $campaignOffer = $campaign->offer;
                                    $productPrice = $flashDealProduct->price;
                                    $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                    $totalPrice = $productPrice;
                                }

                                $totalPrice = $flashDealProduct->price;
                                if($flashDealProduct->offer_price != null){
                                    $offerPrice = $flashDealProduct->offer_price;
                                    $offer = $totalPrice - $offerPrice;
                                    $percentage = ($offer * 100) / $totalPrice;
                                    $percentage = round($percentage);
                                }
                            @endphp

                                @if ($isCampaign)
                                    <p class="wsus__hot_deals_proce">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($flashDealProduct->offer_price == null)
                                    <p class="wsus__hot_deals_proce">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__hot_deals_proce">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $flashDealProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif

                            <P class="wsus__details">
                                {{ $flashDealProduct->short_description }}
                            </P>
                            <ul>
                                <li><a class="add_cart" onclick="addToCartMainProduct('{{ $flashDealProduct->id }}')" href="javascript:;">{{__('user.add to cart')}}</a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $flashDealProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $flashDealProduct->id }}')"><i class="far fa-random"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @endif


            @php
                $productHighlightVisibility = $visibilities->where('id',7)->first();
            @endphp
            @if ($productHighlightVisibility->status == 1)
            <div class="row">
            <div class="wsus__hot_large_item">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="wsus__section_header justify-content-md-center">
                            <div class="monthly_top_filter2">
                                <button class="active click_featured_product" data-filter="._featured">{{__('user.Featured')}}</button>
                                <button data-filter="._best">{{__('user.Best Product')}}</button>
                                <button data-filter="._top">{{__('user.Top Rated')}}</button>
                                <button data-filter="._new">{{__('user.New')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row grid2">
                    @foreach ($featuredProducts as $featuredProduct)

                        @php
                            $reviewQty = $featuredProduct->reviews->where('status',1)->count();
                            $totalReview = $featuredProduct->reviews->where('status',1)->sum('rating');

                            if ($reviewQty > 0) {
                                $average = $totalReview / $reviewQty;

                                $intAverage = intval($average);

                                $nextValue = $intAverage + 1;
                                $reviewPoint = $intAverage;
                                $halfReview=false;
                                if($intAverage < $average && $average < $nextValue){
                                    $reviewPoint= $intAverage + 0.5;
                                    $halfReview=true;
                                }
                            }
                        @endphp

                        @php
                            $variantPrice = 0;
                            $variants = $featuredProduct->variants->where('status', 1);
                            if($variants->count() != 0){
                                foreach ($variants as $variants_key => $variant) {
                                    if($variant->variantItems->where('status',1)->count() != 0){
                                        $item = $variant->variantItems->where('is_default',1)->first();
                                        if($item){
                                            $variantPrice += $item->price;
                                        }
                                    }
                                }
                            }

                            $isCampaign = false;
                            $today = date('Y-m-d H:i:s');

                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $featuredProduct->id])->first();
                            if($campaign){
                                $campaign = $campaign->campaign;
                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                    $isCampaign = true;
                                }
                                $campaignOffer = $campaign->offer;
                                $productPrice = $featuredProduct->price;
                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                $totalPrice = $productPrice;
                            }

                            $totalPrice = $featuredProduct->price;
                            if($featuredProduct->offer_price != null){
                                $offerPrice = $featuredProduct->offer_price;
                                $offer = $totalPrice - $offerPrice;
                                $percentage = ($offer * 100) / $totalPrice;
                                $percentage = round($percentage);
                            }
                        @endphp
                    <div class="col-xl-3 col-sm-6 col-md-6 col-lg-4 _featured">
                        <div class="wsus__product_item">
                            @if ($featuredProduct->new_product == 1)
                                <span class="wsus__new">{{__('user.New')}}</span>
                            @elseif ($featuredProduct->is_featured == 1)
                                <span class="wsus__new">{{__('user.Featured')}}</span>
                            @elseif ($featuredProduct->is_top == 1)
                                <span class="wsus__new">{{__('user.Top')}}</span>
                            @elseif ($featuredProduct->is_best == 1)
                                <span class="wsus__new">{{__('user.Best')}}</span>
                            @endif

                            @if ($isCampaign)
                                <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                            @else
                                @if ($featuredProduct->offer_price != null)
                                    <span class="wsus__minus">-{{ $percentage }}%</span>
                                @endif
                            @endif

                            <a class="wsus__pro_link" href="{{ route('product-detail', $featuredProduct->slug) }}">
                                <img src="{{ asset($featuredProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                <img src="{{ asset($featuredProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                            </a>


                            <ul class="wsus__single_pro_icon">
                                <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $featuredProduct->id }}"><i class="fal fa-eye"></i></a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $featuredProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $featuredProduct->id }}')"><i class="far fa-random"></i></a></li>
                            </ul>
                            <div class="wsus__product_details">
                                <a class="wsus__category" href="{{ route('product',['category' => $featuredProduct->category->slug]) }}">{{ $featuredProduct->category->name }} </a>

                                @if ($reviewQty > 0)
                                    <p class="wsus__pro_rating">
                                        @for ($i = 1; $i <=5; $i++)
                                            @if ($i <= $reviewPoint)
                                                <i class="fas fa-star"></i>
                                            @elseif ($i> $reviewPoint )
                                                @if ($halfReview==true)
                                                <i class="fas fa-star-half-alt"></i>
                                                    @php
                                                        $halfReview=false
                                                    @endphp
                                                @else
                                                <i class="fal fa-star"></i>
                                                @endif
                                            @endif
                                        @endfor
                                        <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                    </p>
                                @endif

                                @if ($reviewQty == 0)
                                    <p class="wsus__pro_rating">
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <span>(0 {{__('user.review')}})</span>
                                    </p>
                                @endif

                                <a class="wsus__pro_name" href="{{ route('product-detail', $featuredProduct->slug) }}">{{ $featuredProduct->short_name }}</a>
                                @if ($isCampaign)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($featuredProduct->offer_price == null)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $featuredProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif
                                <a class="add_cart" onclick="addToCartMainProduct('{{ $featuredProduct->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($bestProducts as $bestProduct)
                    @php
                        $reviewQty = $bestProduct->reviews->where('status',1)->count();
                        $totalReview = $bestProduct->reviews->where('status',1)->sum('rating');

                        if ($reviewQty > 0) {
                            $average = $totalReview / $reviewQty;

                            $intAverage = intval($average);

                            $nextValue = $intAverage + 1;
                            $reviewPoint = $intAverage;
                            $halfReview=false;
                            if($intAverage < $average && $average < $nextValue){
                                $reviewPoint= $intAverage + 0.5;
                                $halfReview=true;
                            }
                        }
                    @endphp

                    @php
                        $variantPrice = 0;
                        $variants = $bestProduct->variants->where('status', 1);
                        if($variants->count() != 0){
                            foreach ($variants as $variants_key => $variant) {
                                if($variant->variantItems->where('status',1)->count() != 0){
                                    $item = $variant->variantItems->where('is_default',1)->first();
                                    if($item){
                                        $variantPrice += $item->price;
                                    }
                                }
                            }
                        }

                        $isCampaign = false;
                        $today = date('Y-m-d H:i:s');

                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $bestProduct->id])->first();
                        if($campaign){
                            $campaign = $campaign->campaign;
                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                $isCampaign = true;
                            }
                            $campaignOffer = $campaign->offer;
                            $productPrice = $bestProduct->price;
                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                            $totalPrice = $productPrice;
                        }

                        $totalPrice = $bestProduct->price;
                        if($bestProduct->offer_price != null){
                            $offerPrice = $bestProduct->offer_price;
                            $offer = $totalPrice - $offerPrice;
                            $percentage = ($offer * 100) / $totalPrice;
                            $percentage = round($percentage);
                        }
                    @endphp
                    <div class="col-xl-3 col-sm-6 col-md-6 col-lg-4 _best">
                        <div class="wsus__product_item">
                            @if ($bestProduct->new_product == 1)
                                <span class="wsus__new">{{__('user.New')}}</span>
                            @elseif ($bestProduct->is_featured == 1)
                                <span class="wsus__new">{{__('user.Featured')}}</span>
                            @elseif ($bestProduct->is_top == 1)
                                <span class="wsus__new">{{__('user.Top')}}</span>
                            @elseif ($bestProduct->is_best == 1)
                                <span class="wsus__new">{{__('user.Best')}}</span>
                            @endif
                            @if ($isCampaign)
                                <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                            @else
                                @if ($bestProduct->offer_price != null)
                                    <span class="wsus__minus">-{{ $percentage }}%</span>
                                @endif
                            @endif
                            <a class="wsus__pro_link" href="{{ route('product-detail', $bestProduct->slug) }}">
                                <img src="{{ asset($bestProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                <img src="{{ asset($bestProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                            </a>
                            @php
                                $reviewQty = $bestProduct->reviews->where('status',1)->count();
                                $totalReview = $bestProduct->reviews->where('status',1)->sum('rating');

                                if ($reviewQty > 0) {
                                    $average = $totalReview / $reviewQty;

                                    $intAverage = intval($average);

                                    $nextValue = $intAverage + 1;
                                    $reviewPoint = $intAverage;
                                    $halfReview=false;
                                    if($intAverage < $average && $average < $nextValue){
                                        $reviewPoint= $intAverage + 0.5;
                                        $halfReview=true;
                                    }
                                }
                            @endphp

                            @php
                                $variantPrice = 0;
                                $variants = $bestProduct->variants->where('status', 1);
                                if($variants->count() != 0){
                                    foreach ($variants as $variants_key => $variant) {
                                        if($variant->variantItems->where('status',1)->count() != 0){
                                            $item = $variant->variantItems->where('is_default',1)->first();
                                            if($item){
                                                $variantPrice += $item->price;
                                            }
                                        }
                                    }
                                }

                                $isCampaign = false;
                                $today = date('Y-m-d H:i:s');

                                $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $bestProduct->id])->first();
                                if($campaign){
                                    $campaign = $campaign->campaign;
                                    if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                        $isCampaign = true;
                                    }
                                    $campaignOffer = $campaign->offer;
                                    $productPrice = $bestProduct->price;
                                    $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                    $totalPrice = $productPrice;
                                }

                                $totalPrice = $bestProduct->price;
                                if($bestProduct->offer_price != null){
                                    $offerPrice = $bestProduct->offer_price;
                                    $offer = $totalPrice - $offerPrice;
                                    $percentage = ($offer * 100) / $totalPrice;
                                    $percentage = round($percentage);
                                }
                            @endphp

                            <ul class="wsus__single_pro_icon">
                                <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $bestProduct->id }}"><i class="fal fa-eye"></i></a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $bestProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $bestProduct->id }}')"><i class="far fa-random"></i></a></li>
                            </ul>
                            <div class="wsus__product_details">
                                <a class="wsus__category" href="{{ route('product',['category' => $bestProduct->category->slug]) }}">{{ $bestProduct->category->name }} </a>

                                @if ($reviewQty > 0)
                                    <p class="wsus__pro_rating">
                                        @for ($i = 1; $i <=5; $i++)
                                            @if ($i <= $reviewPoint)
                                                <i class="fas fa-star"></i>
                                            @elseif ($i> $reviewPoint )
                                                @if ($halfReview==true)
                                                <i class="fas fa-star-half-alt"></i>
                                                    @php
                                                        $halfReview=false
                                                    @endphp
                                                @else
                                                <i class="fal fa-star"></i>
                                                @endif
                                            @endif
                                        @endfor
                                        <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                    </p>
                                @endif

                                @if ($reviewQty == 0)
                                    <p class="wsus__pro_rating">
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <span>(0 {{__('user.review')}})</span>
                                    </p>
                                @endif

                                <a class="wsus__pro_name" href="{{ route('product-detail', $bestProduct->slug) }}">{{ $bestProduct->short_name }}</a>
                                @if ($isCampaign)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($bestProduct->offer_price == null)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $bestProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif
                                <a class="add_cart" onclick="addToCartMainProduct('{{ $bestProduct->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($topProducts as $topProduct)

                    @php
                        $reviewQty = $topProduct->reviews->where('status',1)->count();
                        $totalReview = $topProduct->reviews->where('status',1)->sum('rating');

                        if ($reviewQty > 0) {
                            $average = $totalReview / $reviewQty;

                            $intAverage = intval($average);

                            $nextValue = $intAverage + 1;
                            $reviewPoint = $intAverage;
                            $halfReview=false;
                            if($intAverage < $average && $average < $nextValue){
                                $reviewPoint= $intAverage + 0.5;
                                $halfReview=true;
                            }
                        }
                    @endphp

                    @php
                        $variantPrice = 0;
                        $variants = $topProduct->variants->where('status', 1);
                        if($variants->count() != 0){
                            foreach ($variants as $variants_key => $variant) {
                                if($variant->variantItems->where('status',1)->count() != 0){
                                    $item = $variant->variantItems->where('is_default',1)->first();
                                    if($item){
                                        $variantPrice += $item->price;
                                    }
                                }
                            }
                        }

                        $isCampaign = false;
                        $today = date('Y-m-d H:i:s');

                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $topProduct->id])->first();
                        if($campaign){
                            $campaign = $campaign->campaign;
                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                $isCampaign = true;
                            }
                            $campaignOffer = $campaign->offer;
                            $productPrice = $topProduct->price;
                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                            $totalPrice = $productPrice;
                        }

                        $totalPrice = $topProduct->price;
                        if($topProduct->offer_price != null){
                            $offerPrice = $topProduct->offer_price;
                            $offer = $totalPrice - $offerPrice;
                            $percentage = ($offer * 100) / $totalPrice;
                            $percentage = round($percentage);
                        }
                    @endphp

                    <div class="col-xl-3 col-sm-6 col-md-6 col-lg-4 _top">
                        <div class="wsus__product_item ">
                            @if ($topProduct->new_product == 1)
                                <span class="wsus__new">{{__('user.New')}}</span>
                            @elseif ($topProduct->is_featured == 1)
                                <span class="wsus__new">{{__('user.Featured')}}</span>
                            @elseif ($topProduct->is_top == 1)
                                <span class="wsus__new">{{__('user.Top')}}</span>
                            @elseif ($topProduct->is_best == 1)
                                <span class="wsus__new">{{__('user.Best')}}</span>
                            @endif

                            @if ($isCampaign)
                                <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                            @else
                                @if ($topProduct->offer_price != null)
                                    <span class="wsus__minus">-{{ $percentage }}%</span>
                                @endif
                            @endif
                            <a class="wsus__pro_link" href="{{ route('product-detail', $topProduct->slug) }}">
                                <img src="{{ asset($topProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                <img src="{{ asset($topProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                            </a>


                            <ul class="wsus__single_pro_icon">
                                <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $topProduct->id }}"><i class="fal fa-eye"></i></a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $topProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $topProduct->id }}')"><i class="far fa-random"></i></a>
                                </li>
                            </ul>
                            <div class="wsus__product_details">
                                <a class="wsus__category" href="{{ route('product',['category' => $topProduct->category->slug]) }}">{{ $topProduct->category->name }} </a>

                                @if ($reviewQty > 0)
                                    <p class="wsus__pro_rating">
                                        @for ($i = 1; $i <=5; $i++)
                                            @if ($i <= $reviewPoint)
                                                <i class="fas fa-star"></i>
                                            @elseif ($i> $reviewPoint )
                                                @if ($halfReview==true)
                                                <i class="fas fa-star-half-alt"></i>
                                                    @php
                                                        $halfReview=false
                                                    @endphp
                                                @else
                                                <i class="fal fa-star"></i>
                                                @endif
                                            @endif
                                        @endfor
                                        <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                    </p>
                                @endif

                                @if ($reviewQty == 0)
                                    <p class="wsus__pro_rating">
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <span>(0 {{__('user.review')}})</span>
                                    </p>
                                @endif

                                <a class="wsus__pro_name" href="{{ route('product-detail', $topProduct->slug) }}">{{ $topProduct->short_name }}</a>
                                @if ($isCampaign)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($topProduct->offer_price == null)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $topProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif
                                <a class="add_cart" onclick="addToCartMainProduct('{{ $topProduct->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($newProducts as $newProduct)
                    @php
                        $reviewQty = $newProduct->reviews->where('status',1)->count();
                        $totalReview = $newProduct->reviews->where('status',1)->sum('rating');

                        if ($reviewQty > 0) {
                            $average = $totalReview / $reviewQty;

                            $intAverage = intval($average);

                            $nextValue = $intAverage + 1;
                            $reviewPoint = $intAverage;
                            $halfReview=false;
                            if($intAverage < $average && $average < $nextValue){
                                $reviewPoint= $intAverage + 0.5;
                                $halfReview=true;
                            }
                        }
                    @endphp

                    @php
                        $variantPrice = 0;
                        $variants = $newProduct->variants->where('status', 1);
                        if($variants->count() != 0){
                            foreach ($variants as $variants_key => $variant) {
                                if($variant->variantItems->where('status',1)->count() != 0){
                                    $item = $variant->variantItems->where('is_default',1)->first();
                                    if($item){
                                        $variantPrice += $item->price;
                                    }
                                }
                            }
                        }

                        $isCampaign = false;
                        $today = date('Y-m-d H:i:s');

                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $newProduct->id])->first();
                        if($campaign){
                            $campaign = $campaign->campaign;
                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                $isCampaign = true;
                            }
                            $campaignOffer = $campaign->offer;
                            $productPrice = $newProduct->price;
                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                            $totalPrice = $productPrice;
                        }

                        $totalPrice = $newProduct->price;
                        if($newProduct->offer_price != null){
                            $offerPrice = $newProduct->offer_price;
                            $offer = $totalPrice - $offerPrice;
                            $percentage = ($offer * 100) / $totalPrice;
                            $percentage = round($percentage);
                        }
                    @endphp
                    <div class="col-xl-3 col-sm-6 col-md-6 col-lg-4 _new">
                        <div class="wsus__product_item wsus__after">
                            @if ($newProduct->new_product == 1)
                                <span class="wsus__new">{{__('user.New')}}</span>
                            @elseif ($newProduct->is_featured == 1)
                                <span class="wsus__new">{{__('user.Featured')}}</span>
                            @elseif ($newProduct->is_top == 1)
                                <span class="wsus__new">{{__('user.Top')}}</span>
                            @elseif ($newProduct->is_best == 1)
                                <span class="wsus__new">{{__('user.Best')}}</span>
                            @endif

                            @if ($isCampaign)
                                <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                            @else
                                @if ($newProduct->offer_price != null)
                                    <span class="wsus__minus">-{{ $percentage }}%</span>
                                @endif
                            @endif
                            <a class="wsus__pro_link" href="{{ route('product-detail', $newProduct->slug) }}">
                                <img src="{{ asset($newProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                <img src="{{ asset($newProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                            </a>

                            <ul class="wsus__single_pro_icon">
                                <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $newProduct->id }}"><i class="fal fa-eye"></i></a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $newProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $newProduct->id }}')"><i class="far fa-random"></i></a>
                                </li>
                            </ul>
                            <div class="wsus__product_details">
                                <a class="wsus__category" href="{{ route('product',['category' => $newProduct->category->slug]) }}">{{ $newProduct->category->name }} </a>

                                @if ($reviewQty > 0)
                                    <p class="wsus__pro_rating">
                                        @for ($i = 1; $i <=5; $i++)
                                            @if ($i <= $reviewPoint)
                                                <i class="fas fa-star"></i>
                                            @elseif ($i> $reviewPoint )
                                                @if ($halfReview==true)
                                                <i class="fas fa-star-half-alt"></i>
                                                    @php
                                                        $halfReview=false
                                                    @endphp
                                                @else
                                                <i class="fal fa-star"></i>
                                                @endif
                                            @endif
                                        @endfor
                                        <span>({{ $reviewQty }} {{__('user.review')}})</span>
                                    </p>
                                @endif

                                @if ($reviewQty == 0)
                                    <p class="wsus__pro_rating">
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <span>(0 {{__('user.review')}})</span>
                                    </p>
                                @endif

                                <a class="wsus__pro_name" href="{{ route('product-detail', $newProduct->slug) }}">{{ $newProduct->short_name }}</a>
                                @if ($isCampaign)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($newProduct->offer_price == null)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $newProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif
                                <a class="add_cart" onclick="addToCartMainProduct('{{ $newProduct->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            </div>
            @endif


            @php
                $bannerVisiblity = $visibilities->where('id',8)->first();
            @endphp
            @if ($bannerVisiblity->status == 1)
                <section id="wsus__single_banner">
                    <div class="">
                        <div class="row">
                            @php
                                $bannerOne = $banners->where('id',5)->first();
                                $bannerTwo = $banners->where('id',6)->first();
                            @endphp
                            <div class="col-xl-6 col-lg-6">
                                <div class="wsus__single_banner_content">
                                    <div class="wsus__single_banner_img">
                                        <img src="{{ $bannerOne->image }}" alt="banner" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__single_banner_text">
                                        <h6>{{ $bannerOne->description }}</h6>
                                        <h3>{{ $bannerOne->title }}</h3>
                                        <a class="shop_btn" href="{{ $bannerOne->link }}">{{__('user.shop now')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="wsus__single_banner_content">
                                    <div class="wsus__single_banner_img">
                                        <img src="{{ $bannerTwo->image }}" alt="banner" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__single_banner_text">
                                        <h6>{{ $bannerTwo->description }}</h6>
                                        <h3>{{ $bannerTwo->title }}</h3>
                                        <a class="shop_btn" href="{{ $bannerTwo->link }}">{{__('user.shop now')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </section>

    <!--============================
           HOT DEALS END
    ==============================-->



    <!--============================
        WEEKLY BEST ITEM START
    ==============================-->
@php
    $threeColVisible = $visibilities->where('id',9)->first();
@endphp
@if ($threeColVisible->status == 1)
    <section id="wsus__weekly_best">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-sm-6 col-md-6 col-lg-4">
                    <div class="wsus__section_header">
                        @php
                            $firstCategory = $columnCategories->where('id',$threeColumnCategory->category_id_one)->first();
                        @endphp
                        <h3>{{ $firstCategory ? $firstCategory->name : '' }}</h3>
                    </div>
                    <div class="row weekly_best">
                        @foreach ($threeColumnFirstCategoryProducts as $threeColfirstCatProduct)
                            <div class="col-xl-12">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $threeColfirstCatProduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ asset($threeColfirstCatProduct->thumb_image) }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $threeColfirstCatProduct->short_name }}</h5>
                                        @php
                                            $reviewQty = $threeColfirstCatProduct->reviews->where('status',1)->count();
                                            $totalReview = $threeColfirstCatProduct->reviews->where('status',1)->sum('rating');
                                            if ($reviewQty > 0) {
                                                $average = $totalReview / $reviewQty;
                                                $intAverage = intval($average);
                                                $nextValue = $intAverage + 1;
                                                $reviewPoint = $intAverage;
                                                $halfReview=false;
                                                if($intAverage < $average && $average < $nextValue){
                                                    $reviewPoint= $intAverage + 0.5;
                                                    $halfReview=true;
                                                }
                                            }
                                        @endphp

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $threeColfirstCatProduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }

                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $threeColfirstCatProduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $threeColfirstCatProduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $productPrice;
                                            }

                                            $totalPrice = $threeColfirstCatProduct->price;
                                            if($threeColfirstCatProduct->offer_price != null){
                                                $offerPrice = $threeColfirstCatProduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp

                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($threeColfirstCatProduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $threeColfirstCatProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif

                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-md-6 col-lg-4">
                    <div class="wsus__section_header">
                        @php
                            $secondCategory = $columnCategories->where('id',$threeColumnCategory->category_id_two)->first();
                        @endphp
                        <h3>{{ $secondCategory ? $secondCategory->name : '' }}</h3>
                    </div>
                    <div class="row weekly_best">
                        @foreach ($threeColumnSecondCategoryProducts as $threeColsecondCatProduct)
                            <div class="col-xl-12">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $threeColsecondCatProduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ asset($threeColsecondCatProduct->thumb_image) }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $threeColsecondCatProduct->short_name }}</h5>
                                        @php
                                            $reviewQty = $threeColsecondCatProduct->reviews->where('status',1)->count();
                                            $totalReview = $threeColsecondCatProduct->reviews->where('status',1)->sum('rating');
                                            if ($reviewQty > 0) {
                                                $average = $totalReview / $reviewQty;
                                                $intAverage = intval($average);
                                                $nextValue = $intAverage + 1;
                                                $reviewPoint = $intAverage;
                                                $halfReview=false;
                                                if($intAverage < $average && $average < $nextValue){
                                                    $reviewPoint= $intAverage + 0.5;
                                                    $halfReview=true;
                                                }
                                            }
                                        @endphp

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $threeColsecondCatProduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }

                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $threeColsecondCatProduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $threeColsecondCatProduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $productPrice;
                                            }

                                            $totalPrice = $threeColsecondCatProduct->price;
                                            if($threeColsecondCatProduct->offer_price != null){
                                                $offerPrice = $threeColsecondCatProduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp

                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($threeColsecondCatProduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $threeColsecondCatProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif

                                    </div>
                                </a>
                            </div>
                        @endforeach


                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-md-6 col-lg-4">
                    <div class="wsus__section_header">
                        @php
                            $threeCategory = $columnCategories->where('id',$threeColumnCategory->category_id_three)->first();
                        @endphp
                        <h3>{{ $threeCategory ? $threeCategory->name : ''}}</h3>
                    </div>
                    <div class="row weekly_best">
                        @foreach ($threeColumnThirdCategoryProducts as $threeColCatProduct)
                            <div class="col-xl-12">
                                <a class="wsus__hot_deals__single" href="{{ route('product-detail', $threeColCatProduct->slug) }}">
                                    <div class="wsus__hot_deals__single_img">
                                        <img src="{{ asset($threeColCatProduct->thumb_image) }}" alt="bag" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__hot_deals__single_text">
                                        <h5>{{ $threeColCatProduct->short_name }}</h5>
                                        @php
                                            $reviewQty = $threeColCatProduct->reviews->where('status',1)->count();
                                            $totalReview = $threeColCatProduct->reviews->where('status',1)->sum('rating');
                                            if ($reviewQty > 0) {
                                                $average = $totalReview / $reviewQty;
                                                $intAverage = intval($average);
                                                $nextValue = $intAverage + 1;
                                                $reviewPoint = $intAverage;
                                                $halfReview=false;
                                                if($intAverage < $average && $average < $nextValue){
                                                    $reviewPoint= $intAverage + 0.5;
                                                    $halfReview=true;
                                                }
                                            }
                                        @endphp

                                        @if ($reviewQty > 0)
                                            <p class="wsus__rating">
                                                @for ($i = 1; $i <=5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </p>
                                        @endif

                                        @if ($reviewQty == 0)
                                            <p class="wsus__rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </p>
                                        @endif

                                        @php
                                            $variantPrice = 0;
                                            $variants = $threeColCatProduct->variants->where('status', 1);
                                            if($variants->count() != 0){
                                                foreach ($variants as $variants_key => $variant) {
                                                    if($variant->variantItems->where('status',1)->count() != 0){
                                                        $item = $variant->variantItems->where('is_default',1)->first();
                                                        if($item){
                                                            $variantPrice += $item->price;
                                                        }
                                                    }
                                                }
                                            }

                                            $isCampaign = false;
                                            $today = date('Y-m-d H:i:s');
                                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $threeColCatProduct->id])->first();
                                            if($campaign){
                                                $campaign = $campaign->campaign;
                                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                    $isCampaign = true;
                                                }
                                                $campaignOffer = $campaign->offer;
                                                $productPrice = $threeColCatProduct->price;
                                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                $totalPrice = $productPrice;
                                            }

                                            $totalPrice = $threeColCatProduct->price;
                                            if($threeColCatProduct->offer_price != null){
                                                $offerPrice = $threeColCatProduct->offer_price;
                                                $offer = $totalPrice - $offerPrice;
                                                $percentage = ($offer * 100) / $totalPrice;
                                                $percentage = round($percentage);
                                            }
                                        @endphp

                                        @if ($isCampaign)
                                            <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($threeColCatProduct->offer_price == null)
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__tk">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $threeColCatProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif

                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
    <!--============================
        WEEKLY BEST ITEM END
    ==============================-->

        <!--============================
        LARGE BANNER  START
    ==============================-->

    @php
        $bannerVisibility = $visibilities->where('id',10)->first();
    @endphp
    @if ($bannerVisibility->status == 1)
    <section id="wsus__single_banner">
        <div class="container">
            <div class="row">
                @php
                    $bannerOne = $banners->where('id',7)->first();
                    $bannerTwo = $banners->where('id',8)->first();
                @endphp
                <div class="col-xl-6 col-lg-6">
                    <div class="wsus__single_banner_content">
                        <div class="wsus__single_banner_img">
                            <img src="{{ asset($bannerOne->image) }}" alt="banner" class="img-fluid w-100">
                        </div>
                        <div class="wsus__single_banner_text">
                            <h6>{{ $bannerOne->description }}</h6>
                            <h3>{{ $bannerOne->title }}</h3>
                            <a class="shop_btn" href="{{ $bannerOne->link }}">{{__('user.shop now')}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="wsus__single_banner_content">
                        <div class="wsus__single_banner_img">
                            <img src="{{ asset($bannerTwo->image) }}" alt="banner" class="img-fluid w-100">
                        </div>
                        <div class="wsus__single_banner_text">
                            <h6>{{ $bannerTwo->description }}</h6>
                            <h3>{{ $bannerTwo->title }}</h3>
                            <a class="shop_btn" href="{{ $bannerTwo->link }}">{{__('user.shop now')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--============================
        LARGE BANNER  END
    ==============================-->

    <!--============================
      HOME SERVOCES START
    ==============================-->
    @php
        $serviceVisibility = $visibilities->where('id',11)->first();
    @endphp
    @if ($serviceVisibility->status == 1)
    <section id="wsus__home_services">
        <div class="container">
            <div class="row">
                @foreach ($services as $service)
                <div class="col-xl-3 col-sm-6 col-lg-3">
                    <div class="wsus__home_services_single">
                        <i class="{{ $service->icon }}"></i>
                        <h5>{{ $service->title }}</h5>
                        <p>{{ $service->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!--============================
        HOME SERVOCES END
    ==============================-->


    <!--============================
        HOME BLOGS START
    ==============================-->
@php
    $blogVisibilty = $visibilities->where('id',12)->first();
@endphp
@if ($blogVisibilty->status == 1)
    <section id="wsus__blogs" class="home_blogs">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__section_header">
                        <h3>{{__('user.recent blogs')}}</h3>
                        <a class="see_btn" href="{{ route('blog') }}">{{__('user.see more')}} <i class="fas fa-caret-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row home_blog_slider">
                @php
                    $colorId=1;
                @endphp
                @foreach ($blogs as $index => $blog)
                @php
                    if($index %4 ==0){
                        $colorId=1;
                    }

                    $color="";
                    if($colorId==1){
                        $color="blue";
                    }else if($colorId==2){
                        $color="red";
                    }else if($colorId==3){
                        $color="orange";
                    }else if($colorId==4){
                        $color="green";
                    }
                @endphp
                <div class="col-xl-4">
                    <div class="wsus__single_blog">
                        <a class="wsus__blog_img" href="{{ route('blog-detail', $blog->slug) }}">
                            <img src="{{ asset($blog->image) }}" alt="blog" class="img-fluid w-100">
                        </a>
                        <a class="blog_top {{ $color }}" href="{{ route('blog-by-category',$blog->category->slug) }}">{{ $blog->category->name }}</a>
                        <div class="wsus__blog_text">
                            <div class="wsus__blog_text_center">
                                <a href="{{ route('blog-detail', $blog->slug) }}">{{ $blog->title }}</a>
                                <p class="date"><span>{{ $blog->created_at->format('d F, Y') }}</span> {{__('user.Hosted by')}} {{ $blog->admin->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $colorId ++;
                @endphp
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!--============================
        HOME BLOGS END
    ==============================-->

@endsection
