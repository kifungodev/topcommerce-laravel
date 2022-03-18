@extends('user.layout')
@section('title')
    <title>{{__('user.Change Password')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content mt-2 mt-md-0">
        <h3><i class="fas fa-lock-alt"></i> {{__('user.Change Password')}}</h3>
        <div class="wsus__dashboard_profile">
          <div class="wsus__dash_pro_area">
            <form action="{{ route('user.update-password') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="wsus__dash_pro_single">
                            <i class="fas fa-unlock-alt"></i>
                            <input type="password" name="current_password" placeholder="{{__('user.Current Password')}}">
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="wsus__dash_pro_single">
                            <i class="fas fa-lock-alt"></i>
                            <input type="password" name="password" placeholder="{{__('user.New Password')}}">
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="wsus__dash_pro_single">
                            <i class="fas fa-lock-alt"></i>
                            <input type="password" name="password_confirmation" placeholder="{{__('user.Confirm Password')}}">
                        </div>
                    </div>
                    <div class="col-xl-12">
                    <button class="common_btn" type="submit">{{__('user.Update Password')}}</button>
                    </div>
                </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
@endsection
