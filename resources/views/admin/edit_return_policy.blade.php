@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Return Policy')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Return Policy')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.return-policy.index') }}">{{__('admin.Return Policy')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Return Policy')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.return-policy.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Return Policy')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.return-policy.update',$returnPolicy->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="title" value="{{ $returnPolicy->title }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Details')}} <span class="text-danger">*</span></label>
                                    <textarea name="details" id="" cols="30" rows="10" class="form-control text-area-5">{{ $returnPolicy->details }}</textarea>
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $returnPolicy->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $returnPolicy->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
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
          </div>
        </section>
      </div>
@endsection
