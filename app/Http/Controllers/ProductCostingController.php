<?php 
namespace App\Http\Controllers;

use App\ProductCosting;
use App\Product; // Make sure you have this model
use App\BusinessLocation; // Make sure you have this model
use Illuminate\Http\Request;
use DB;

class ProductCostingController extends Controller
{
    public function index()
    {
        $productCostings = ProductCosting::all();
        return view('product_costings.index', compact('productCostings'));
    }

    public function create()
    {
        $products = Product::all();
        $locations = BusinessLocation::all();
        $last_inserted_column = ProductCosting::latest()->first();

        return view('product_costings.create', compact('products','locations','last_inserted_column'));
    }

    public function store(Request $request)
    {
        $productCosting = new ProductCosting();
        $productCosting->date = $request->date;
        $productCosting->container_number = $request->container_number;
        $productCosting->bn_number = $request->bn_number;
        $productCosting->location = $request->location;


        $productCosting->product_id = json_encode($request->product_id);
        $productCosting->box_in_container = json_encode($request->box_in_container);
        $productCosting->sqm_in_container = json_encode($request->sqm_in_container);
        $productCosting->sqm_in_box = json_encode($request->sqm_in_box);
        $productCosting->price_per_sqm = json_encode($request->price_per_sqm);
        $productCosting->exp1 = json_encode($request->exp1);
        $productCosting->exp2 = json_encode($request->exp2);
        $productCosting->exp3 = json_encode($request->exp3);
        $productCosting->exp4 = json_encode($request->exp4);
        $productCosting->exp5 = json_encode($request->exp5);
        $productCosting->exp6 = json_encode($request->exp6);
        $productCosting->exp7 = json_encode($request->exp7);
        $productCosting->exp8 = json_encode($request->exp8);
        $productCosting->exp9 = json_encode($request->exp9);
        $productCosting->total_exp = json_encode($request->total_exp);
        $productCosting->final_price = json_encode($request->final_price);
        $productCosting->total_final_exp = json_encode($request->total_final_exp);
        $productCosting->final_costing_price = json_encode($request->final_costing_price);
        $productCosting->column_names = json_encode($request->column_names);


        $productCosting->save();
        return redirect()->route('product-costings.index');
    }


    public function show($id)
    {

        $productCosting = ProductCosting::with('product')->find($id);

        if ($productCosting) {
            $productCostingData = $productCosting->toArray(); // Data from ProductCosting table

            // Check if the relationship exists before accessing it
            if ($productCosting->product) {
                $productData = $productCosting->product->toArray(); // Data from related Product table
            } else {
                // Handle case where the relationship doesn't exist
                $productData = null;
            }
        } else {
            // Handle case where the ProductCosting record with the specified ID doesn't exist
            $productCostingData = null;
            $productData = null;
        }

        // print_r($productCostingData);
        // print_r($productData);
        // die();

        return view('product_costings.show', compact('productCosting','productData'));
    }

    public function edit($id)
    {
        $productCosting = ProductCosting::findOrFail($id);
        $products = Product::all();
        $locations = BusinessLocation::all();
        return view('product_costings.edit', compact('productCosting', 'products', 'locations'));
    }

    public function update(Request $request, $id)
    {

        // print_r($request->all());
        // die();

        $productCosting = ProductCosting::findOrFail($id);

        $productCosting->date = $request->date;
        $productCosting->container_number = $request->container_number;
        $productCosting->bn_number = $request->bn_number;
        $productCosting->location = $request->location;

        $productCosting->product_id = json_encode($request->product_id);
        $productCosting->box_in_container = json_encode($request->box_in_container);
        $productCosting->sqm_in_container = json_encode($request->sqm_in_container);
        $productCosting->sqm_in_box = json_encode($request->sqm_in_box);
        $productCosting->price_per_sqm = json_encode($request->price_per_sqm);
        $productCosting->exp1 = json_encode($request->exp1);
        $productCosting->exp2 = json_encode($request->exp2);
        $productCosting->exp3 = json_encode($request->exp3);
        $productCosting->exp4 = json_encode($request->exp4);
        $productCosting->exp5 = json_encode($request->exp5);
        $productCosting->exp6 = json_encode($request->exp6);
        $productCosting->exp7 = json_encode($request->exp7);
        $productCosting->exp8 = json_encode($request->exp8);
        $productCosting->exp9 = json_encode($request->exp9);
        $productCosting->total_exp = json_encode($request->total_exp);
        $productCosting->final_price = json_encode($request->final_price);
        $productCosting->total_final_exp = json_encode($request->total_final_exp);
        $productCosting->final_costing_price = json_encode($request->final_costing_price);

        $productCosting->save();

        return redirect()->route('product-costings.index');
    }

    public function delete_casting($id)
    {
        $productCosting = ProductCosting::findOrFail($id);
        $productCosting->delete();
        return redirect()->route('product-costings.index');
    }
}

