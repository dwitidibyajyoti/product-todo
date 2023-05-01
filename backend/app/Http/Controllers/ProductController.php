<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'product_image' => 'required|array',
            'product_image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }




        $productImages = [];
        $image = $request->file('product_images');
        foreach ($image as $key => $value) {
            $imageName = time() . '.' . $value->getClientOriginalExtension();
            $productImages[$key] = $imageName;
            $value->move(public_path('uploads'), $imageName);
            // return 'Image uploaded successfully.';
        }
        Log::info($productImages);

        try {
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->product_image = json_encode($productImages);
            $product->save();
        } catch (\Throwable $th) {
            return response()->json(['errors' => 'Unable to Store product'], 401);
        }

        return response()->json(['success' => 'Store product successfully'], 201);




    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
