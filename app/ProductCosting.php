<?php

// app/Models/ProductCosting.php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'date','location', 'container_number', 'bn_number', 'product_id', 'box_in_container', 
        'sqm_in_container', 'sqm_in_box', 'price_per_sqm', 'exp1', 'exp2', 'exp3', 
        'exp4', 'exp5', 'exp6', 'exp7', 'exp8', 'exp9', 'total_exp', 'final_price', 'total_final_exp', 'final_costing_price', 'column_names', 'product_image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
