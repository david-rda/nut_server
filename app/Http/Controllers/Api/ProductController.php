<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {        
        if(!empty($request->input("query"))) {
            return Product::where("name", "like", "%" . trim($request->input("query")) . "%")->orderBy("id", "DESC")->paginate(20);
        }

        return Product::orderBy("id", "DESC")->paginate(20);
    }

    public function byStatus() {
        return Product::where("status", "enabled")->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => "required",
            "status" => "required"
        ]);

        $product = new Product();
        $product->name = $request->title;
        $product->status = $request->status;
        $product->save();

        if($product) {
            return response()->json([
                "success" => "პროდუქტის დამატება განხორციელდა."
            ], 200);
        }else {
            return response()->json([
                "error" => "პროდუქტის დამატება ვერ განხორციელდა."
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            "title" => "required",
            "status" => "required"
        ]);

        $product = Product::find($id);
        $product->name = $request->title;
        $product->status = $request->status;
        $product->save();

        if($product) {
            return response()->json([
                "success" => "პროდუქტის დარედაქტირება განხორციელდა."
            ], 200);
        }else {
            return response()->json([
                "error" => "პროდუქტის დარედაქტირება ვერ განხორციელდა."
            ], 422);
        }
    }
}
