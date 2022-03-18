@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Campaign')}}</h1>
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
                        <form action="{{ route('admin.campaign.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Image Preview')}}</label>
                                    <div>
                                        <img id="preview-img" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="image" onchange="previewThumnailImage(event)">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control"  name="slug" value="{{ old('slug') }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ old('title') }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('admin.Offer')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control" name="offer" value="{{ old('offer') }}">
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Start Time')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="date" class="form-control datetimepicker_mask"  name="start_date" autocomplete="off">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.End Time')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="datee" class="form-control datetimepicker_mask"  name="end_date" autocomplete="off">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Show Homepage?')}} <span class="text-danger">*</span></label>
                                    <select name="show_homepage" class="form-control">
                                        <option value="0">{{__('admin.No')}}</option>
                                        <option value="1">{{__('admin.Yes')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
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
