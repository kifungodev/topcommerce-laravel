@extends('layout')
@section('title')
    <title>{{__('user.Track Order')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('user.Track Order')}}">
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
                        <h4>{{__('user.Track Order')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('user.home')}}</a></li>
                            <li><a href="{{ route('track-order') }}">{{__('user.Track Order')}}</a></li>
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
        TRACKING ORDER START
    ==============================-->
    <section id="wsus__login_register">
        <div class="container">
            <div class="wsus__track_area">
                <div class="row">
                    <div class="col-xl-5 col-md-10 col-lg-8 m-auto">
                        <form id="orderTrackForm" class="tack_form">
                            <h4 class="text-center">{{__('user.Order Tracking')}}</h4>
                            <p class="text-center">{{__('user.Tracking your order status')}}</p>
                            <div class="wsus__track_input">
                                <label class="d-block mb-2">{{__('user.order id')}}*</label>
                                <input type="text" name="order_id" id="order_id" required placeholder="4785**541">
                            </div>
                            <button type="submit" class="common_btn">{{__('user.track')}}</button>
                        </form>
                    </div>
                </div>
                <div class="row" id="ajax_response">

                </div>
            </div>
        </div>
    </section>
    <!--============================
        TRACKING ORDER END
    ==============================-->




    <script>
        (function($) {
            "use strict";
            $(document).ready(function () {
                $("#orderTrackForm").on('submit', function(e){
                    e.preventDefault();
                    let orderId = $("#order_id").val();
                    $.ajax({
                        type: 'get',
                        url: "{{ url('track-order-response') }}"+ "/" + orderId,
                        success: function (response) {
                            if(response.status == 0){
                                toastr.error(response.message);
                            }else{
                                $("#ajax_response").html(response);

                            }

                        },
                        error: function(err) {
                        }
                    });
                })
            });
        })(jQuery);
    </script>


@endsection
