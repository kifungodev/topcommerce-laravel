@if ($order->order_status == 0)
    <div class="col-xl-12">
        <ul class="progtrckr" data-progtrckr-steps="4">
            <li class="progtrckr_done icon_one check_mark">{{__('user.Order pending')}}</li>
            <li class="icon_two">{{__('user.Order Processing')}}</li>
            <li class="icon_three">{{__('user.on the way')}}</li>
            <li class="icon_four">{{__('user.Completed')}}</li>
        </ul>
    </div>
@elseif ($order->order_status == 1)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('user.Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('user.Order Processing')}}</li>
        <li class="icon_three">{{__('user.on the way')}}</li>
        <li class="icon_four">{{__('user.Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 2)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('user.Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('user.Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('user.on the way')}}</li>
        <li class="icon_four">{{__('user.Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 3)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('user.Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('user.Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('user.on the way')}}</li>
        <li class="icon_four progtrckr_done check_mark">{{__('user.Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 4)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('user.Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('user.Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('user.on the way')}}</li>
        <li class="icon_four red_mark">{{__('user.Declined')}}</li>
    </ul>
</div>
@endif

<div class="col-xl-12">
    <a href="{{ route('user.order') }}" class="common_btn"><i class="fas fa-chevron-left"></i> {{__('user.back to order')}}</a>
</div>
