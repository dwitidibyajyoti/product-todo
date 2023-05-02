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
        $task = Product::where('deleted_at',null)->get();

        return response()->json(['data' => json_decode($task)], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info($request->all());

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'product_images' => 'required|array',
            'product_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {


            return response()->json(['errors' => $validator->errors()], 422);
        }




        $productImages = [];
        $image = $request->file('product_images');
        foreach ($image as $key => $value) {
            $imageName = time() .'-'. str_replace(" ","-",$value->getClientOriginalName()) . '.' . $value->getClientOriginalExtension();
            $productImages[$key] = $imageName;
            $value->move(public_path('uploads'), $imageName);
            // return 'Image uploaded successfully.';
        }


        try {
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->product_price = $request->product_price;
            $product->product_image = json_encode($productImages);
            $product->save();
            Log::info($request->all());

        } catch (\Throwable $th) {
            return response()->json(['errors' => 'Unable to Store product'], 400);


        }

        return response()->json(['success' => 'Store product successfully'], 201);




    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Product::where('id',$id)->first();

        return response()->json(['data' => json_decode($task)], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $id)
    {
        Log::info($request->all());

        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_description' => 'required',
            'product_price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateArry = [];
            if ($request->product_name) {
                $updateArry['product_name'] = $request->product_name;
            }
            if ($request->product_description) {
                $updateArry['product_description'] = $request->product_description;
            }

            if ($request->product_price) {
                $updateArry['product_price'] = $request->product_price;
            }

            if (array_key_exists("product_images", $request->all())) {

                $productImages = [];
                $image = $request->file('product_images');
                foreach ($image as $key => $value) {
                    $imageName = time() . '-' . str_replace(" ", "-", $value->getClientOriginalName()) . '.' . $value->getClientOriginalExtension();
                    $productImages[$key] = $imageName;
                    $value->move(public_path('uploads'), $imageName);
                    // return 'Image uploaded successfully.';
                }

                $updateArry['product_image'] = json_encode($productImages);
            }
        } catch (\Throwable $th) {
            return response()->json(['errors' => 'Unable to update product'], 401);
        }

        Product::where("id", $id->id)->update($updateArry);
        return response()->json(['success' => 'UPdate product successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $id)
    {
        try {
            Product::find($id->id)->delete();
       } catch (\Throwable $th) {
           return response()->json(['errors' => 'error while delete table'], 401);
       }


       return response()->json(['errors' => 'Product deleted successfully'], 200);
    }
}
