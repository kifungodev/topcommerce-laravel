@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Advertisement')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Advertisement')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Advertisement')}}</div>
            </div>
          </div>

          <div class="section-body">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-3">
                                <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link active" id="error-tab-1" data-toggle="tab" href="#errorTab-1" role="tab" aria-controls="errorTab-1" aria-selected="true">{{__('admin.Mega Menu Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="one-column-banner-tab" data-toggle="tab" href="#oneColumnBanner" role="tab" aria-controls="oneColumnBanner" aria-selected="true">{{__('admin.Home Page One Column Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="two-column-banner-first" data-toggle="tab" href="#twoColumnBannerFirst" role="tab" aria-controls="twoColumnBannerFirst" aria-selected="true">{{__('admin.Home Page First Two Column Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="two-column-banner-second" data-toggle="tab" href="#twoColumnBannerSecond" role="tab" aria-controls="twoColumnBannerSecond" aria-selected="true">{{__('admin.Home Page Second Two Column Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="two-column-banner-third" data-toggle="tab" href="#twoColumnBannerThird" role="tab" aria-controls="twoColumnBannerThird" aria-selected="true">{{__('admin.Home Page Third Two Column Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="shop-page" data-toggle="tab" href="#shopPage" role="tab" aria-controls="shopPage" aria-selected="true">{{__('admin.Shop Page Banner')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="product-details" data-toggle="tab" href="#productDetails" role="tab" aria-controls="productDetails" aria-selected="true">{{__('admin.Product Details')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="cart-bottom-banner" data-toggle="tab" href="#cartBottomBanner" role="tab" aria-controls="cartBottomBanner" aria-selected="true">{{__('Shopping Cart Bottom')}}</a>
                                    </li>

                                    <li class="nav-item border rounded mb-1">
                                        <a class="nav-link" id="campaign-page" data-toggle="tab" href="#campaignPage" role="tab" aria-controls="campaignPage" aria-selected="true">{{__('Campaign Page')}}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-12 col-md-9">
                                <div class="border rounded">
                                    <div class="tab-content no-padding" id="settingsContent">
                                        <div class="tab-pane fade show active" id="errorTab-1" role="tabpanel" aria-labelledby="error-tab-1">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.mega-menu-banner-update') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Current Banner')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($megaMenuBanner->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image" class="form-control-file">
                                                            </div>


                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title" class="form-control" value="{{ $megaMenuBanner->title }}">
                                                            </div>



                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description" class="form-control" value="{{ $megaMenuBanner->description }}">
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link" class="form-control" value="{{ $megaMenuBanner->link }}">
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Text')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_text" class="form-control" value="{{ $megaMenuBanner->button_text }}">
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                                                <select name="status" class="form-control">
                                                                    <option {{ $megaMenuBanner->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                                                    <option {{ $megaMenuBanner->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="oneColumnBanner" role="tabpanel" aria-labelledby="one-column-banner-tab">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-home-page-one-column-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Current Banner')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($oneColumnBanner->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title" class="form-control" value="{{ $oneColumnBanner->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description" class="form-control" value="{{ $oneColumnBanner->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link" class="form-control" value="{{ $oneColumnBanner->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="twoColumnBannerFirst" role="tabpanel" aria-labelledby="two-column-banner-first">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-home-page-first-two-column-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner One')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($firstTwoColumnBannerOne->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_one" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_one" class="form-control" value="{{ $firstTwoColumnBannerOne->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_one" class="form-control" value="{{ $firstTwoColumnBannerOne->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_one"  class="form-control" value="{{ $firstTwoColumnBannerOne->link }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner Two')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($firstTwoColumnBannerTwo->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_two" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_two" class="form-control" value="{{ $firstTwoColumnBannerTwo->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_two" class="form-control" value="{{ $firstTwoColumnBannerTwo->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_two" class="form-control" value="{{ $firstTwoColumnBannerTwo->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="tab-pane fade" id="twoColumnBannerSecond" role="tabpanel" aria-labelledby="two-column-banner-second">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-home-page-second-two-column-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner One')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($secondTwoColumnBannerOne->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_one" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_one" class="form-control" value="{{ $secondTwoColumnBannerOne->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_one" class="form-control" value="{{ $secondTwoColumnBannerOne->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_one"  class="form-control" value="{{ $secondTwoColumnBannerOne->link }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner Two')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($secondTwoColumnBannerTwo->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_two" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_two" class="form-control" value="{{ $secondTwoColumnBannerTwo->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_two" class="form-control" value="{{ $secondTwoColumnBannerTwo->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_two" class="form-control" value="{{ $secondTwoColumnBannerTwo->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="twoColumnBannerThird" role="tabpanel" aria-labelledby="two-column-banner-third">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-home-page-third-two-column-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner One')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($thirdTwoColumnBannerOne->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_one" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_one" class="form-control" value="{{ $thirdTwoColumnBannerOne->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_one" class="form-control" value="{{ $thirdTwoColumnBannerOne->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_one"  class="form-control" value="{{ $thirdTwoColumnBannerOne->link }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner Two')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($thirdTwoColumnBannerTwo->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_two" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_two" class="form-control" value="{{ $thirdTwoColumnBannerTwo->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_two" class="form-control" value="{{ $thirdTwoColumnBannerTwo->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_two" class="form-control" value="{{ $thirdTwoColumnBannerTwo->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="shopPage" role="tabpanel" aria-labelledby="shop-page">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-shop-page') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Visibility Status')}}</label>
                                                            <div>
                                                                @if ($shop_page->status == 1)
                                                                    <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="Enable" data-off="Disable" data-onstyle="success" data-offstyle="danger" name="status">
                                                                    @else
                                                                    <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="Enable" data-off="Disable" data-onstyle="success" data-offstyle="danger" name="status">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Existing Banner')}}</label>
                                                            <div>
                                                                <img src="{{ asset($shop_page->banner) }}" width="200px" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.New Banner')}}</label>
                                                            <input type="file" class="form-control-file" name="banner">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Header One')}}</label>
                                                            <input type="text" class="form-control" name="header_one" value="{{ $shop_page->header_one }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Header Two')}}</label>
                                                            <input type="text" class="form-control" name="header_two" value="{{ $shop_page->header_two }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Title One')}}</label>
                                                            <input type="text" class="form-control" name="title_one" value="{{ $shop_page->title_one }}">
                                                        </div>

                                                         <div class="form-group">
                                                            <label for="">{{__('admin.Title Two')}}</label>
                                                            <input type="text" class="form-control" name="title_two" value="{{ $shop_page->title_two }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Link')}}</label>
                                                            <input type="text" class="form-control" name="link" value="{{ $shop_page->link }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">{{__('admin.Button Text')}}</label>
                                                            <input type="text" class="form-control" name="button_text" value="{{ $shop_page->button_text }}">
                                                        </div>

                                                        <button class="btn btn-primary" type="submit">{{__('admin.Update')}}</button>
                                                     </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="productDetails" role="tabpanel" aria-labelledby="product-details">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-product-detail-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">

                                                            <div class="form-group col-12">
                                                                <label for="">{{__('admin.Banner Status')}}</label>
                                                                <div>
                                                                    @if ($banner->status == 1)
                                                                        <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @else
                                                                        <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Existing Banner')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($banner->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title" class="form-control" value="{{ $banner->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description" class="form-control" value="{{ $banner->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link"  class="form-control" value="{{ $banner->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="cartBottomBanner" role="tabpanel" aria-labelledby="cart-bottom-banner">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-cart-bottom-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label for="">{{__('admin.Banner Status')}}</label>
                                                                <div>
                                                                    @if ($shoppingCartBannerOne->status == 1)
                                                                        <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @else
                                                                        <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner One')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($shoppingCartBannerOne->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_one" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_one" class="form-control" value="{{ $shoppingCartBannerOne->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_one" class="form-control" value="{{ $shoppingCartBannerOne->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_one"  class="form-control" value="{{ $shoppingCartBannerOne->link }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner Two')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($shoppingCartBannerTwo->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_two" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_two" class="form-control" value="{{ $shoppingCartBannerTwo->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_two" class="form-control" value="{{ $shoppingCartBannerTwo->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_two" class="form-control" value="{{ $shoppingCartBannerTwo->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="campaignPage" role="tabpanel" aria-labelledby="campaign-page">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <form action="{{ route('admin.update-campaign-page-banner') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label for="">{{__('admin.Banner Status')}}</label>
                                                                <div>
                                                                    @if ($campaignBannerOne->status == 1)
                                                                        <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @else
                                                                        <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner One')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($campaignBannerOne->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_one" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_one" class="form-control" value="{{ $campaignBannerOne->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_one" class="form-control" value="{{ $campaignBannerOne->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_one"  class="form-control" value="{{ $campaignBannerOne->link }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Banner Two')}}</label>
                                                                <div>
                                                                    <img src="{{ asset($campaignBannerTwo->image) }}" alt="" width="200px">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.New Banner')}}</label>
                                                                <input type="file" name="banner_image_two" class="form-control-file">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title_two" class="form-control" value="{{ $campaignBannerTwo->title }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="description_two" class="form-control" value="{{ $campaignBannerTwo->description }}">
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <label>{{__('admin.Button Link')}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="button_link_two" class="form-control" value="{{ $campaignBannerTwo->link }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>







                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
