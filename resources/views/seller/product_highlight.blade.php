@extends('seller.master_layout')
@section('title')
<title>{{__('user.Product Highlight')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('user.Product Highlight')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('user.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('user.Product Highlight')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('user.Products')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('seller.update-product-highlight',$product->id) }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('user.Select Type')}} <span class="text-danger">*</span></label>
                                    <select name="product_type" class="form-control" id="product_type">
                                        <option {{ $product->is_undefine == 1 ? 'selected' : '' }} value="1">{{__('user.Undefine Product')}}</option>
                                        <option {{ $product->new_product == 1 ? 'selected' : '' }} value="2">{{__('user.New Arrival')}}</option>
                                        <option {{ $product->is_featured == 1 ? 'selected' : '' }} value="3">{{__('user.Featured Product')}}</option>
                                        <option {{ $product->is_top == 1 ? 'selected' : '' }} value="4">{{__('user.Top Product')}}</option>
                                        <option {{ $product->is_best == 1 ? 'selected' : '' }} value="5">{{__('user.Best Product')}}</option>
                                        <option {{ $product->is_flash_deal == 1 ? 'selected' : '' }} value="6">{{__('user.Flash Deal Product')}}</option>

                                    </select>
                                </div>
                                @if ($product->is_flash_deal == 1)
                                    <div class="form-group col-12" id="dateBox">
                                        <label for="">{{__('user.Enter Date')}}</label>
                                        <input type="text" name="date" class="form-control datepicker" value="{{ $product->flash_deal_date }}" autocomplete="off">
                                    </div>
                                @else
                                    <div class="form-group col-12 d-none" id="dateBox">
                                        <label for="">{{__('user.Enter Date')}}</label>
                                        <input type="text" name="date" class="form-control datepicker" autocomplete="off">
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('user.Update')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

<script>
    (function($) {
        "use strict";
        var specification = true;
        $(document).ready(function () {
            $("#product_type").on("change",function(){
                var productType = $(this).val();
                if(productType == 6){
                    $("#dateBox").removeClass('d-none');
                }else{
                    $("#dateBox").addClass('d-none');
                }
            })
        });
    })(jQuery);


</script>


@endsection
