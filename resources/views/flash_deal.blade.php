@extends('layout')
@section('title')
    <title>{{ $seoSetting->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $seoSetting->seo_description }}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('user.Flash Deal')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('user.Home')}}</a></li>
                            <li><a href="{{ route('flash-deal') }}">{{__('user.Flash Deal')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->


    <!--============================
        DAILY DEALS DETAILS START
    ==============================-->
    <section id="wsus__daily_deals">
        <div class="container">
            <div class="wsus__offer_details_area">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="wsus__section_header rounded-0">
                            <h3>{{__('user.Flash Deal')}}</h3>
                        </div>
                    </div>
                </div>

                @php
                    $productIds = [];
                    $productYears = [];
                    $productMonths = [];
                    $productDays = [];

                    if(env('APP_VERSION') == 0){
                        $demo_end = Carbon\Carbon::now()->addDays(3);
                        foreach ($products as $key => $product) {
                            $productIds[] = $product->id;
                            $productYears[] = $demo_end->format('Y');
                            $productMonths[] = $demo_end->format('m');;
                            $productDays[] = $demo_end->format('d');
                        }
                    }else {
                        foreach ($products as $key => $product) {
                            $productIds[] = $product->id;
                            $productYears[] = date('Y', strtotime($product->flash_deal_date));
                            $productMonths[] = date('m', strtotime($product->flash_deal_date));
                            $productDays[] = date('d', strtotime($product->flash_deal_date));
                        }
                    }
                @endphp
                <script>
                    var productIds = <?= json_encode($productIds)?>;
                    var productYears = <?= json_encode($productYears)?>;
                    var productMonths = <?= json_encode($productMonths)?>;
                    var productDays = <?= json_encode($productDays)?>;
                </script>


                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-xl-4 col-sm-6 col-lg-4">
                            <div class="wsus__offer_det_single">
                                @php
                                    $variantPrice = 0;
                                    $variants = $product->variants->where('status', 1);
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
                                    $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
                                    if($campaign){
                                        $campaign = $campaign->campaign;
                                        if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                            $isCampaign = true;
                                        }
                                        $campaignOffer = $campaign->offer;
                                        $productPrice = $product->price;
                                        $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                        $totalPrice = $product->price;
                                    }

                                    $totalPrice = $product->price;
                                    if($product->offer_price != null){
                                        $offerPrice = $product->offer_price;
                                        $offer = $totalPrice - $offerPrice;
                                        $percentage = ($offer * 100) / $totalPrice;
                                        $percentage = round($percentage);
                                    }
                                @endphp
                                <div class="wsus__product_item">
                                    @if ($isCampaign)
                                        <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                                    @else
                                        @if ($product->offer_price != null)
                                            <span class="wsus__minus">-{{ $percentage }}%</span>
                                        @endif
                                    @endif


                                    <a class="wsus__pro_link" href="{{ route('product-detail', $product->slug) }}">
                                        <img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                        <img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                                    </a>
                                    <ul class="wsus__single_pro_icon">
                                        <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $product->id }}"><i class="fal fa-eye"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToWishlist('{{ $product->id }}')"><i class="far fa-heart"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToCompare('{{ $product->id }}')"><i class="far fa-random"></i></a></li>
                                    </ul>

                                    @php
                                        $reviewQty = $product->reviews->where('status',1)->count();
                                        $totalReview = $product->reviews->where('status',1)->sum('rating');

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
                                    <div class="wsus__product_details">
                                        <a class="wsus__category" href="{{ route('product',['category' => $product->category->slug]) }}">{{ $product->category->name }} </a>

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


                                        <a class="wsus__pro_name" href="{{ route('product-detail', $product->slug) }}">{{ $product->short_name }}</a>

                                        @if ($isCampaign)
                                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($product->offer_price == null)
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                        <a class="add_cart" onclick="addToCartMainProduct('{{ $product->id }}')" href="javascript:;">{{__('user.Add to Cart')}}</a>
                                    </div>
                                </div>
                                <div class="wsus__offer_time">
                                    <div class="simply-countdown flash-deal-product-{{ $product->id }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-xl-12">
                        {{ $products->links('custom_paginator') }}
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!--============================
        DAILY DEALS DETAILS END
    ==============================-->

@endsection
