@extends('seller.master_layout')
@section('title')
<title>{{__('user.Product Review')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('user.Product Review')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('user.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('seller.product.index') }}">{{__('user.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('user.Product Review')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product-review') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('user.Product Review')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                           <tr>
                               <td>{{__('user.User Name')}}</td>
                               <td>{{ $review->user->name }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.User Email')}}</td>
                               <td>{{ $review->user->email }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.Product')}}</td>
                               <td><a href="{{ route('seller.product.edit', $review->product->id) }}">{{ $review->product->name }}</a></td>
                           </tr>
                           <tr>
                               <td>{{__('user.Rating')}}</td>
                               <td>{{ $review->rating }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.Review')}}</td>
                               <td>{{ $review->review }}</td>
                           </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

@endsection
