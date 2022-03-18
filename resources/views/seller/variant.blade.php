@extends('seller.master_layout')
@section('title')
<title>{{__('user.Product Variant')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('user.Product Variant')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('user.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('seller.product.index') }}">{{__('user.Products')}}</a></div>
              <div class="breadcrumb-item">{{__('user.Product Variant')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('user.Go Back')}}</a>

            <a href="javascript:;" data-toggle="modal" data-target="#createVariant" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('user.Add New')}}</a>

            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                      <div class="card-header">
                          <h1>{{__('user.Product')}} : {{ $product->short_name }}</h1>
                      </div>
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="10%">{{__('user.SN')}}</th>
                                    <th width="30%">{{__('user.Name')}}</th>
                                    <th width="30%">{{__('user.Status')}}</th>
                                    <th width="30%">{{__('user.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($variants as $index => $variant)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $variant->name }}</td>
                                        <td>
                                            @if($variant->status == 1)
                                            <a href="javascript:;" onclick="changeVariantStatus({{ $variant->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('user.Active')}}" data-off="{{__('user.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeVariantStatus({{ $variant->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('user.Active')}}" data-off="{{__('user.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td>
                                        <td>

                                        <a href="{{ route('seller.product-variant-item',['product_id'=>$product->id,'variant_id'=>$variant->id]) }}" class="btn btn-success btn-sm"><i class="fas fa-cog" aria-hidden="true"></i> {{__('user.manage options')}}</a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#editVariant-{{ $variant->id }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        @if ($variant->variantItems->count() == 0)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $variant->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
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

      <!--Create Modal -->
      <div class="modal fade" id="createVariant" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                      <div class="modal-header">
                              <h5 class="modal-title">{{__('user.Create Variant')}}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                          </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                        <form action="{{ route('seller.store-product-variant') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('user.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name">
                                    <input type="hidden" id="product_id" class="form-control"  name="product_id" value="{{ $product->id }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('user.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('user.Active')}}</option>
                                        <option value="0">{{__('user.Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">{{__('user.Save')}}</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('user.Close')}}</button>
                                </div>
                            </div>
                        </form>
                      </div>
                  </div>

              </div>
          </div>
      </div>

      {{-- edit modal --}}
      @foreach ($variants as $index => $variant)
            <div class="modal fade" id="editVariant-{{ $variant->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                    <h5 class="modal-title">{{__('user.Create Variant')}}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <form action="{{ route('seller.update-product-variant',$variant->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="form-group col-12">
                                            <label>{{__('user.Name')}} <span class="text-danger">*</span></label>
                                            <input type="text" id="name" class="form-control"  name="name" value="{{ $variant->name }}">
                                            <input type="hidden" id="product_id" class="form-control"  name="product_id" value="{{ $product->id }}">
                                        </div>
                                        <div class="form-group col-12">
                                            <label>{{__('user.Status')}} <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control">
                                                <option {{ $variant->status == 1 ? 'selected' : '' }} value="1">{{__('user.Active')}}</option>
                                                <option {{ $variant->status == 0 ? 'selected' : '' }} value="0">{{__('user.Inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary">{{__('user.Update')}}</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('user.Close')}}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
      @endforeach

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("seller/delete-product-variant/") }}'+"/"+id)
    }
    function changeVariantStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/seller/product-variant-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response)
            },
            error:function(err){
                console.log(err);

            }
        })
    }
</script>
@endsection
