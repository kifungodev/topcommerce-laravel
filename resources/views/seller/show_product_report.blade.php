@extends('seller.master_layout')
@section('title')
<title>{{__('user.Product report')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('user.Product report')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('user.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('seller.product.index') }}">{{__('user.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('user.Product report')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product-report') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('user.Product report')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                           <tr>
                               <td>{{__('user.User Name')}}</td>
                               <td>{{ $report->user->name }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.User Email')}}</td>
                               <td>{{ $report->user->email }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.Product')}}</td>
                               <td><a href="{{ route('seller.product.edit', $report->product->id) }}">{{ $report->product->name }}</a></td>
                           </tr>
                           <tr>
                               <td>{{__('user.Product Status')}}</td>
                               <td>
                                   @if ($report->product->status == 1)
                                    <span class="badge badge-success">{{__('user.Active')}}</span>
                                    @else
                                    <span class="badge badge-danger">{{__('user.Inactive')}}</span>
                                   @endif
                               </td>
                           </tr>

                           <tr>
                               <td>{{__('user.Total reports under this product')}}</td>
                               <td><a href="">{{ $totalReport }}</a></td>
                           </tr>

                           <tr>
                               <td>{{__('user.Subject')}}</td>
                               <td>{{ $report->subject }}</td>
                           </tr>
                           <tr>
                               <td>{{__('user.Description')}}</td>
                               <td>{{ $report->description }}</td>
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
