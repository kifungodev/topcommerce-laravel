<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function wholesales(){
        return $this->hasMany(Wholesell::class)->orderBy('minimum_product','asc');
    }

    public function seller(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function gallery(){
        return $this->hasMany(ProductGallery::class);
    }

    public function specifications(){
        return $this->hasMany(ProductSpecification::class);
    }

    public function reviews(){
        return $this->hasMany(ProductReview::class);
    }


    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function returnPolicy(){
        return $this->belongsTo(ReturnPolicy::class);
    }

    public function tax(){
        return $this->belongsTo(ProductTax::class);
    }

    public function variantItems(){
        return $this->hasMany(ProductVariantItem::class);
    }






}
