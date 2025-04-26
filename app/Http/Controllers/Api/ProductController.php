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
    public function index()
    {
        return Product::paginate(20);
    }

    /**
     * @method GET
     * @param null
     * @return json
     * 
     * მოცემული მეთოდის დახმარებით ხდება ბაზიდან პროდუქტების/პესტიციდების გამოტანა
     * აქტიური სტატუსის მიხედვით, 
     */
    public function byStatus() {
        return Product::where("status", "enabled")->get();
    }

    /**
     * @method POST
     * @param Request
     * @return json
     * 
     * მოცემული მეთოდის დახმარებით ხდება პროდუქტის/პესტიციდის დამატება
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
     * @method GET
     * @param int
     * @return json
     * 
     * მოცემული მეთოდის დახმარებით ხდება
     * კონკრეტული პროდუქტის/პესტიციდის ინფორმაციის გამოტანა
     */
    public function show(int $id)
    {
        return Product::find($id);
    }

    /**
     * @method POST
     * @param Request, int $id
     * @return json
     * 
     * მოცემული მეთოდის დახმარებით ხდება პროდუქტის/პესტიციდის დარედაქტირება
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
