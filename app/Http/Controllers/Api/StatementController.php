<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Statement;
use App\Models\StatementProduct;
use App\Models\Product;
use App\Models\Attachements;
use App\Models\User;
use Auth;
use DB;
use Carbon\Carbon;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id = null)
    {
        if(User::whereId(Auth::id())->first()->permission == "company") {
            return Statement::where("user_id", Auth::id())->paginate(30);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "overhead_number" => "required",
            "overhead_date" => "required",
            "store_address" => "required",
            "beneficiary_name" => "required",
            "card_number" => "required",
        ]);

        try {

            DB::transaction(function() use($request) {

                $statement = new Statement();

                $statement->user_id = Auth::id();
                $statement->status = "new";
                $statement->attachement_id = $request->attachement_id;
                $statement->overhead_number = $request->overhead_number;
                $statement->overhead_date = Carbon::now();
                $statement->store_address = $request->store_address;
                $statement->beneficiary_name = $request->beneficiary_name;
                $statement->full_amount = $request->full_amount;
                $statement->card_number = $request->card_number;

                $statement->save();

                // foreach($request->file("files") as $data) {
                //     $file = $data->getRealPath();
            
                //     $content = file_get_contents($file);
    
                //     $base64 = base64_encode($content);

                //     $attachement = new Attachements();
                //     $attachement->statement_id = $statement->id;
                //     $attachement->file = $base64;
                //     $attachement->save();
                // }

                foreach($request->products as $products) {
                    $product = new StatementProduct();
                    $product->product_id = $products["product_id"];
                    $product->statement_id = $statement->id;
                    $product->price = $products["product_price"];
                    $product->save();
                }
            });
            
            DB::commit();

            return response()->json([
                "success" => "განაცხადი დაემატა."
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "error" => "განაცხადი ვერ დაემატა."
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Statement::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        //
    }
}
