@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create ')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.campaign.index') }}">{{__('admin.Campaign')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Campaign')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.campaign.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Campaign')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.campaign.update',$campaign->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Image Preview')}}</label>
                                    <div>
                                        <img id="preview-img" class="admin-img" src="{{ asset($campaign->image) }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="image" onchange="previewThumnailImage(event)">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name" value="{{ $campaign->name }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control"  name="slug" value="{{ $campaign->slug }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ $campaign->title }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Offer')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control" name="offer" value="{{ $campaign->offer }}">
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Start Date')}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datetimepicker_mask"  name="start_date" value="{{ $campaign->start_date }}" autocomplete="off">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.End Date')}} <span class="text-danger">*</span></label>
                                    <input type="text"  class="form-control datetimepicker_mask"  name="end_date" value="{{ $campaign->end_date }}" autocomplete="off">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Show Homepage?')}} <span class="text-danger">*</span></label>
                                    <select name="show_homepage" class="form-control">
                                        <option {{ $campaign->show_homepage == 0 ? 'selected' : '' }} value="0">{{__('admin.No')}}</option>
                                        <option {{ $campaign->show_homepage == 1 ? 'selected' : '' }} value="1">{{__('admin.Yes')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $campaign->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $campaign->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
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

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })
        });
    })(jQuery);

    function convertToSlug(Text)
        {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
        }

    function previewThumnailImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };
</script>
@endsection
