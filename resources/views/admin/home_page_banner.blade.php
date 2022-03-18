@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Home Page')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Home Page')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Home Page')}}</div>
            </div>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-3">
                                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link active" id="popular-category-tab" data-toggle="tab" href="#popularCategory" role="tab" aria-controls="popularCategory" aria-selected="true">{{__('admin.Popular Categories')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="three-column-category" data-toggle="tab" href="#threeColumnCategory" role="tab" aria-controls="threeColumnCategory" aria-selected="true">{{__('admin.Three Column Category')}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 col-sm-12 col-md-9">
                                    <div class="border rounded">
                                        <div class="tab-content no-padding" id="settingsContent">

                                            <div class="tab-pane fade show active" id="popularCategory" role="tabpanel" aria-labelledby="popular-category-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        <form action="{{ route('admin.update-popular-categoy') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Section Title')}} <span class="text-danger">*</span></label>
                                                                    <input type="text" name="title" class="form-control" value="{{ $popularCategory->title }}">
                                                                </div>

                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Product Qty')}} <span class="text-danger">*</span></label>
                                                                    <input type="text" name="product_qty" class="form-control" value="{{ $popularCategory->product_qty }}">
                                                                </div>


                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 1')}}:</h1>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_one" id="category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $categoryOne)
                                                                        <option {{ $categoryOne->id == $popularCategory->category_id_one ? 'selected' : '' }} value="{{ $categoryOne->id }}">{{ $categoryOne->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesOne = $subCategories->where('category_id',$popularCategory->category_id_one);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_one" id="sub_category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesOne as $subCategoryOne)
                                                                            <option {{ $subCategoryOne->id == $popularCategory->sub_category_id_one ? 'selected' : '' }} value="{{ $subCategoryOne->id }}">{{ $subCategoryOne->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesOne = $childCategories->where('sub_category_id',$popularCategory->sub_category_id_one);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_one" id="child_category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesOne as $childCategoryOne)
                                                                        <option {{ $childCategoryOne->id == $popularCategory->child_category_id_one ? 'selected' : '' }} value="{{ $childCategoryOne->id }}">{{ $childCategoryOne->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 2')}}:</h1>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_two" id="category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $categoryTwo)
                                                                        <option {{ $categoryTwo->id == $popularCategory->category_id_two ? 'selected' : '' }} value="{{ $categoryTwo->id }}">{{ $categoryTwo->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesTwo = $subCategories->where('category_id',$popularCategory->category_id_two);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_two" id="sub_category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesTwo as $subCategoryTwo)
                                                                            <option {{ $subCategoryTwo->id == $popularCategory->sub_category_id_two ? 'selected' : '' }} value="{{ $subCategoryTwo->id }}">{{ $subCategoryTwo->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesTwo = $childCategories->where('sub_category_id',$popularCategory->sub_category_id_two);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_two" id="child_category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesTwo as $childCategoryTwo)
                                                                        <option {{ $childCategoryTwo->id == $popularCategory->child_category_id_two ? 'selected' : '' }} value="{{ $childCategoryTwo->id }}">{{ $childCategoryTwo->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>


                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 3')}}:</h1>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_three" id="category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $categoryThree)
                                                                        <option {{ $categoryThree->id == $popularCategory->category_id_three ? 'selected' : '' }} value="{{ $categoryThree->id }}">{{ $categoryThree->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesThree = $subCategories->where('category_id',$popularCategory->category_id_three);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_three" id="sub_category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesThree as $subCategoryThree)
                                                                            <option {{ $subCategoryThree->id == $popularCategory->sub_category_id_three ? 'selected' : '' }} value="{{ $subCategoryThree->id }}">{{ $subCategoryThree->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesThree = $childCategories->where('sub_category_id',$popularCategory->sub_category_id_three);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_three" id="child_category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesThree as $childCategoryThree)
                                                                        <option {{ $childCategoryThree->id == $popularCategory->child_category_id_three ? 'selected' : '' }} value="{{ $childCategoryThree->id }}">{{ $childCategoryThree->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 4')}}:</h1>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}}<span class="text-danger">*</span></label>
                                                                    <select name="category_four" id="category_four" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $categoryFour)
                                                                        <option {{ $categoryFour->id == $popularCategory->category_id_four ? 'selected' : '' }} value="{{ $categoryFour->id }}">{{ $categoryFour->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesFour = $subCategories->where('category_id',$popularCategory->category_id_four);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_four" id="sub_category_four" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesFour as $subCategoryFour)
                                                                            <option {{ $subCategoryFour->id == $popularCategory->sub_category_id_four ? 'selected' : '' }} value="{{ $subCategoryFour->id }}">{{ $subCategoryFour->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesFour = $childCategories->where('sub_category_id',$popularCategory->sub_category_id_four);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_four" id="child_category_four" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesFour as $childCategoryFour)
                                                                        <option {{ $childCategoryFour->id == $popularCategory->child_category_id_four ? 'selected' : '' }} value="{{ $childCategoryFour->id }}">{{ $childCategoryFour->name }}</option>
                                                                        @endforeach
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

                                            <div class="tab-pane fade" id="threeColumnCategory" role="tabpanel" aria-labelledby="three-column-category">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        <form action="{{ route('admin.update-three-column-categoy') }}" method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 1')}}:</h1>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_one" id="three_col_category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $threeCategoryOne)
                                                                        <option {{ $threeCategoryOne->id == $threeColumnCategory->category_id_one ? 'selected' : '' }} value="{{ $threeCategoryOne->id }}">{{ $threeCategoryOne->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesOne = $subCategories->where('category_id',$threeColumnCategory->category_id_one);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_one" id="three_col_sub_category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesOne as $threeSubCategoryOne)
                                                                            <option {{ $threeSubCategoryOne->id == $threeColumnCategory->sub_category_id_one ? 'selected' : '' }} value="{{ $threeSubCategoryOne->id }}">{{ $threeSubCategoryOne->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesOne = $childCategories->where('sub_category_id',$threeColumnCategory->sub_category_id_one);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_one" id="three_col_child_category_one" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesOne as $threeChildCategoryOne)
                                                                        <option {{ $threeChildCategoryOne->id == $threeColumnCategory->child_category_id_one ? 'selected' : '' }} value="{{ $threeChildCategoryOne->id }}">{{ $threeChildCategoryOne->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 2')}}:</h1>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_two" id="three_col_category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $threeCategoryTwo)
                                                                        <option {{ $threeCategoryTwo->id == $threeColumnCategory->category_id_two ? 'selected' : '' }} value="{{ $threeCategoryTwo->id }}">{{ $threeCategoryTwo->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesTwo = $subCategories->where('category_id',$threeColumnCategory->category_id_two);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_two" id="three_col_sub_category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesTwo as $threeSubCategoryTwo)
                                                                            <option {{ $threeSubCategoryTwo->id == $threeColumnCategory->sub_category_id_two ? 'selected' : '' }} value="{{ $threeSubCategoryTwo->id }}">{{ $threeSubCategoryTwo->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesTwo = $childCategories->where('sub_category_id',$threeColumnCategory->sub_category_id_two);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_two" id="three_col_child_category_two" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesTwo as $threeChildCategoryTwo)
                                                                        <option {{ $threeChildCategoryTwo->id == $threeColumnCategory->child_category_id_two ? 'selected' : '' }} value="{{ $threeChildCategoryTwo->id }}">{{ $threeChildCategoryTwo->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h1 class="mb-3">{{__('admin.Category 3')}}:</h1>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                                    <select name="category_three" id="three_col_category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Category')}}</option>
                                                                        @foreach ($categories as $threeCategoryThree)
                                                                        <option {{ $threeCategoryThree->id == $threeColumnCategory->category_id_three ? 'selected' : '' }} value="{{ $threeCategoryThree->id }}">{{ $threeCategoryThree->name }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $subCategoriesThree = $subCategories->where('category_id',$threeColumnCategory->category_id_three);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Sub Category')}}</label>
                                                                    <select name="sub_category_three" id="three_col_sub_category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                                                        @foreach ($subCategoriesThree as $threeSubCategoryThree)
                                                                            <option {{ $threeSubCategoryThree->id == $threeColumnCategory->sub_category_id_three ? 'selected' : '' }} value="{{ $threeSubCategoryThree->id }}">{{ $threeSubCategoryThree->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                @php
                                                                    $childCategoriesThree = $childCategories->where('sub_category_id',$threeColumnCategory->sub_category_id_three);
                                                                @endphp
                                                                <div class="form-group col-12">
                                                                    <label>{{__('admin.Child Category')}}</label>
                                                                    <select name="child_category_three" id="three_col_child_category_three" class="form-control">
                                                                        <option value="">{{__('admin.Select Child Category')}}</option>
                                                                        @foreach ($childCategoriesThree as $threeChildCategoryThree)
                                                                        <option {{ $threeChildCategoryThree->id == $threeColumnCategory->child_category_id_three ? 'selected' : '' }} value="{{ $threeChildCategoryThree->id }}">{{ $threeChildCategoryThree->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <hr>
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
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $("#category_one").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#sub_category_one").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#child_category_one").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#sub_category_one").html(subCategoryHtml)
                $("#child_category_one").html(childCategoryHtml)
            }

        })

        $("#sub_category_one").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#child_category_one").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#child_category_one").html(childCategoryHtml)
            }

        })

        $("#category_two").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#sub_category_two").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#child_category_two").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#sub_category_two").html(subCategoryHtml)
                $("#child_category_two").html(childCategoryHtml)
            }

        })

        $("#sub_category_two").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#child_category_two").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#child_category_two").html(childCategoryHtml)
            }

        })


        $("#category_three").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#sub_category_three").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#child_category_three").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#sub_category_three").html(subCategoryHtml)
                $("#child_category_three").html(childCategoryHtml)
            }

        })

        $("#sub_category_three").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#child_category_three").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#child_category_three").html(childCategoryHtml)
            }

        })

        $("#category_four").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#sub_category_four").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#child_category_four").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#sub_category_four").html(subCategoryHtml)
                $("#child_category_four").html(childCategoryHtml)
            }

        })

        $("#sub_category_four").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#child_category_four").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#child_category_four").html(childCategoryHtml)
            }

        })

        $("#three_col_category_one").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#three_col_sub_category_one").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#three_col_child_category_one").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_sub_category_one").html(subCategoryHtml)
                $("#three_col_child_category_one").html(childCategoryHtml)
            }

        })

        $("#three_col_sub_category_one").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#three_col_child_category_one").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_child_category_one").html(childCategoryHtml)
            }

        })

        $("#three_col_category_two").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#three_col_sub_category_two").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#three_col_child_category_two").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_sub_category_two").html(subCategoryHtml)
                $("#three_col_child_category_two").html(childCategoryHtml)
            }

        })

        $("#three_col_sub_category_two").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#three_col_child_category_two").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_child_category_two").html(childCategoryHtml)
            }

        })

        $("#three_col_category_three").on("change", function(){
            var categoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                success:function(response){
                   $("#three_col_sub_category_three").html(response.subCategories)
                   var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                   $("#three_col_child_category_three").html(childCategoryHtml)
                },
                error:function(err){}
            })
            if(!categoryId){
                var subCategoryHtml = "<option value=''>Select Sub Category</option>"
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_sub_category_three").html(subCategoryHtml)
                $("#three_col_child_category_three").html(childCategoryHtml)
            }

        })

        $("#three_col_sub_category_three").on("change", function(){
            var subCategoryId = $(this).val();
            $.ajax({
                type:"get",
                url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+subCategoryId,
                success:function(response){
                   $("#three_col_child_category_three").html(response.childCategories)
                },
                error:function(err){}
            })
            if(!subCategoryId){
                var childCategoryHtml = "<option value=''>{{__('admin.Select Child Category')}}</option>"
                $("#three_col_child_category_three").html(childCategoryHtml)
            }

        })
    });

    })(jQuery);
</script>
@endsection
