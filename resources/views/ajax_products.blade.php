<div class="tab-pane fade {{ $page_view == 'grid_view' ? 'show active' : '' }} " id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
    @if ($products->count() == 0)
        <div class="row">
            <div class="col-12 text-center">
                <h3 class="text-danger mt-5">{{__('user.Product not found')}}</h3>
            </div>
        </div>
    @endif
    <div class="row">
        @foreach ($products as $product)

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
        <div class="col-xl-4  col-sm-6">
            <div class="wsus__product_item">
                @if ($product->new_product == 1)
                    <span class="wsus__new">{{__('user.New')}}</span>
                @elseif ($product->is_featured == 1)
                    <span class="wsus__new">{{__('user.Featured')}}</span>
                @elseif ($product->is_top == 1)
                    <span class="wsus__new">{{__('user.Top')}}</span>
                @elseif ($product->is_best == 1)
                    <span class="wsus__new">{{__('user.Best')}}</span>
                @endif

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
                    <a class="wsus__pro_name" href="{{ route('product-detail',$product->slug) }}">{{ $product->short_name }}</a>

                    @if ($isCampaign)
                        <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                    @else
                        @if ($product->offer_price == null)
                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                        @else
                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                        @endif
                    @endif

                    <a class="add_cart" onclick="addToCartMainProduct('{{ $product->id }}')" href="javascript:;">{{__('user.add to cart')}}</a>
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-xl-12">
            {{ $products->links('ajax_custom_paginator') }}
        </div>
    </div>
</div>
<div class="tab-pane fade {{ $page_view == 'list_view' ? 'show active' : '' }}" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
    @if ($products->count() == 0)
        <div class="row">
            <div class="col-12 text-center">
                <h3 class="text-danger mt-5">{{__('user.Product not found')}}</h3>
            </div>
        </div>
    @endif
    <div class="row">
        @foreach ($products as $product)

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
        <div class="col-xl-12">
            <div class="wsus__product_item wsus__list_view">
                @if ($product->new_product == 1)
                    <span class="wsus__new">{{__('user.New')}}</span>
                @elseif ($product->is_featured == 1)
                    <span class="wsus__new">{{__('user.Featured')}}</span>
                @elseif ($product->is_top == 1)
                    <span class="wsus__new">{{__('user.Top')}}</span>
                @elseif ($product->is_best == 1)
                    <span class="wsus__new">{{__('user.Best')}}</span>
                @endif

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
                    <a class="wsus__pro_name" href="{{ route('product-detail',$product->slug) }}">{{ $product->short_name }}</a>
                    @if ($isCampaign)
                        <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                    @else
                        @if ($product->offer_price == null)
                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                        @else
                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                        @endif
                    @endif

                    <p class="list_description">{{ $product->short_description }}</p>
                    <ul class="wsus__single_pro_icon">
                        <li><a class="add_cart" onclick="addToCartMainProduct('{{ $product->id }}')" href="javascript:;">{{__('user.add to cart')}}</a></li>
                        <li><a href="javascript:;" onclick="addToWishlist('{{ $product->id }}')"><i class="far fa-heart"></i></a></li>
                        <li><a href="javascript:;" onclick="addToCompare('{{ $product->id }}')"><i class="far fa-random"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
        <div class="col-xl-12">
            {{ $products->links('ajax_custom_paginator') }}
        </div>
    </div>
</div>
