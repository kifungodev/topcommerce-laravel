@extends('layout')
@section('title')
    <title>{{ $campaign->name }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $campaign->name }}">
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
                        <h4>{{__('user.Campaign')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('user.home')}}</a></li>
                            <li><a href="{{ route('campaign') }}">{{__('user.Campaign')}}</a></li>
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

                @if ($bannerOne->status == 1)
                <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="wsus__offer_details_banner">
                            <img src="{{ asset($bannerOne->image) }}" alt="offrt img" class="img-fluid w-100">
                            <div class="wsus__offer_details_banner_text">
                                <span>{{ $bannerOne->title }}</span>
                                <p><b>{{ $bannerOne->description }}</b></p>
                                <a href="{{ $bannerOne->link }}" class="shop_btn">{{__('user.shop now')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="wsus__offer_details_banner">
                            <img src="{{ asset($bannerTwo->image) }}" alt="offrt img" class="img-fluid w-100">
                            <div class="wsus__offer_details_banner_text">
                                <span>{{ $bannerTwo->title }}</span>
                                <p><b>{{ $bannerTwo->description }}</b></p>
                                <a href="{{ $bannerTwo->link }}" class="shop_btn">{{__('user.shop now')}}</a>
                            </div>
                        </div>
                    </div>
                </div>

                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="wsus__section_header rounded-0">
                            <h3>{{ $campaign->name }}</h3>
                            <div class="wsus__offer_countdown">
                                <span class="end_text">{{__('user.ends time')}} :</span>
                                <div class="simply-countdown campaign-details"></div>
                                @if (env('APP_VERSION') == 0)
                                    @php
                                        $demo_end = Carbon\Carbon::now()->addDays(3);
                                    @endphp
                                    <script>
                                        var campaign_end_year = {{ $demo_end->format('Y') }}
                                        var campaign_end_month = {{ $demo_end->format('m') }}
                                        var campaign_end_date = {{ $demo_end->format('d') }}
                                        var campaign_hour = {{ $demo_end->format('H') }}
                                        var campaign_min = {{ $demo_end->format('i') }}
                                        var campaign_sec = {{ $demo_end->format('s') }}
                                    </script>
                                @else
                                    <script>
                                        var campaign_end_year = {{ date('Y', strtotime($campaign->end_date)) }}
                                        var campaign_end_month = {{ date('m', strtotime($campaign->end_date)) }}
                                        var campaign_end_date = {{ date('d', strtotime($campaign->end_date)) }}
                                        var campaign_hour = {{ date('H', strtotime($campaign->end_date)) }}
                                        var campaign_min = {{ date('i', strtotime($campaign->end_date)) }}
                                        var campaign_sec = {{ date('s', strtotime($campaign->end_date)) }}
                                    </script>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    @foreach ($campaignProducts as $campaignProduct)
                        <div class="col-xl-3 col-sm-6 col-lg-3">
                            <div class="wsus__offer_det_single">
                                <div class="wsus__product_item wsus__before">
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
                                    <a class="wsus__pro_link" href="{{ route('product-detail', $campaignProduct->product->slug) }}">
                                        <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                        <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                                    </a>
                                    <ul class="wsus__single_pro_icon">
                                        <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $campaignProduct->product->id }}"><i class="fal fa-eye"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToWishlist('{{ $campaignProduct->product->id }}')"><i class="far fa-heart"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToCompare('{{ $campaignProduct->product->id }}')"><i class="far fa-random"></i></a></li>
                                    </ul>
                                    <div class="wsus__product_details">
                                        <a class="wsus__category" href="{{ route('product',['category' =>  $campaignProduct->product->category->slug]) }}">{{ $campaignProduct->product->category->name }} </a>

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
                        </div>
                    @endforeach


                </div>
            </div>
        </div>
    </section>
    <!--============================
        DAILY DEALS DETAILS END
    ==============================-->

@endsection
