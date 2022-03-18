@extends('seller.master_layout')
@section('title')
<title>{{__('user.Products')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('user.Products')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('user.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('user.Products')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('user.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('user.SN')}}</th>
                                    <th width="30%">{{__('user.Name')}}</th>
                                    <th width="10%">{{__('user.Price')}}</th>
                                    <th width="15%">{{__('user.Photo')}}</th>
                                    <th width="15%">{{__('user.Type')}}</th>
                                    <th width="10%">{{__('user.Status')}}</th>
                                    <th width="15%">{{__('user.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a href="{{ route('product-detail', $product->slug) }}">{{ $product->short_name }}</a></td>
                                        <td>{{ $setting->currency_icon }}{{ $product->price }}</td>
                                        <td> <img class="rounded-circle" src="{{ asset($product->thumb_image) }}" alt="" width="80px"></td>
                                        <td>
                                            @if ($product->is_undefine == 1)
                                            {{__('user.Undefine Product')}}
                                            @elseif ($product->new_product == 1)
                                            {{__('user.New Arrival')}}
                                            @elseif ($product->is_featured == 1)
                                            {{__('user.Featured Product')}}
                                            @elseif ($product->is_top == 1)
                                            {{__('user.Top Product')}}
                                            @elseif ($product->is_best == 1)
                                            {{__('user.Best Product')}}
                                            @elseif ($product->is_flash_deal == 1)
                                            {{__('user.Flash Deal')}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status == 1)
                                            <span class="badge badge-success">{{__('user.Active')}}</span>
                                            @else
                                            <span class="badge badge-danger">{{__('user.Inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                        <a href="{{ route('seller.product.edit',$product->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        @php
                                            $existOrder = $orderProducts->where('product_id',$product->id)->count();
                                        @endphp
                                        @if ($existOrder == 0)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $product->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @else
                                            <a href="javascript:;" data-toggle="modal" data-target="#canNotDeleteModal" class="btn btn-danger btn-sm" disabled><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif

                                        <div class="dropdown d-inline">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class="fas fa-cog"></i>
                                            </button>

                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                              <a class="dropdown-item has-icon" href="{{ route('seller.product-gallery',$product->id) }}"><i class="far fa-image"></i> {{__('user.Image Gallery')}}</a>

                                              <a class="dropdown-item has-icon" href="{{ route('seller.product-highlight',$product->id) }}"><i class="fas fa-lightbulb"></i> {{__('user.Highlight')}}</a>

                                              <a class="dropdown-item has-icon" href="{{ route('seller.product-variant',$product->id) }}"><i class="fas fa-cog"></i> {{__('user.Product Variant')}}</a>
                                            </div>
                                          </div>

                                        </td>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                      <div class="modal-body">
                          {{__('user.You can not delete this product. Because there are one or more order has been created in this product.')}}
                      </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('user.Close')}}</button>
                </div>
            </div>
        </div>
    </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("seller/product/") }}'+"/"+id)
    }
</script>
@endsection
