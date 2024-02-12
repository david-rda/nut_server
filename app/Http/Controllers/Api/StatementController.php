<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Statement;
use App\Models\StatementProduct;
use App\Models\StatementLog;
use App\Models\Product;
use App\Models\Attachements;
use App\Models\User;
use Auth;
use DB;
use Carbon\Carbon;
use PDF;
use Excel;
use App\Exports\StatementExport;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id = null)
    {
        if(Auth::user()->permission == "company") {
            return Statement::where("user_id", Auth::id())->orderBy("id", "DESC")->paginate(30);
        }

        if(Auth::user()->permission == "coordinator") {
            return Statement::orderBy("id", "DESC")->paginate(30);
        }

        if(Auth::user()->permission == "operator") {
            return Statement::where("operator_id", Auth::id())->orderBy("id", "DESC")->paginate(30);
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
            "full_amount" => "required",
        ]);

        try {
            DB::transaction(function() use($request) {
                $statement = new Statement();

                $statement->user_id = Auth::id();
                $statement->status = "new";
                $statement->overhead_number = $request->overhead_number;
                $statement->overhead_date = Carbon::now();
                $statement->store_address = $request->store_address;
                $statement->beneficiary_name = $request->beneficiary_name;
                $statement->full_amount = $request->full_amount;
                $statement->card_number = $request->card_number;

                $statement->save();

                foreach($request["files"] as $id) {
                    $attachement = Attachements::find($id);
                    $attachement->statement_id = $statement->id;
                    $attachement->save();
                }

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

    public function deleteFile(int $id) {
        $delete = Attachements::find($id)->delete();

        if($delete) {
            return response()->json([
                "success" => "წაიშალა."
            ], 200); 
        }else {
            return response()->json([
                "error" => "ვერ წაიშალა."
            ], 422);
        }
    }

    public function uploadFile(Request $request) {
        if($request->hasFile("files")) {

            $data = $request->file("files");

            if($data->getClientOriginalExtension() == "pdf") {
                $file = $data->getRealPath();
                $originalName = $data->getClientOriginalName();

                $content = file_get_contents($file);

                $base64 = base64_encode($content);

                $attachement = new Attachements();
                $attachement->file = $base64;
                $attachement->name = $originalName;
                $attachement->save();

                return [
                    "id" => $attachement->id,
                    "name" => $attachement->name,
                ];
            }else {
                return response()->json([
                    "errors" => [
                        "error" => [
                            "დასაშვებია მხოლოდ pdf ფორმატის ფაილის მიმაგრება"
                        ]
                    ]
                ], 422);
            }
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
        $statement = Statement::find($id);

        if(Auth::user()->permission == "company" && $statement->status == "rejected") {
            $this->validate($request, [
                "overhead_number" => "required",
                "overhead_date" => "required",
                "store_address" => "required",
                "beneficiary_name" => "required",
                "card_number" => "required",
                "full_amount" => "required",
            ]);
    
            try {
                DB::transaction(function() use($request, $statement) {    
                    $statement->user_id = Auth::id();
                    $statement->status = "new";
                    $statement->overhead_number = $request->overhead_number;
                    $statement->overhead_date = Carbon::now();
                    $statement->store_address = $request->store_address;
                    $statement->beneficiary_name = $request->beneficiary_name;
                    $statement->full_amount = $request->full_amount;
                    $statement->card_number = $request->card_number;
    
                    $statement->save();
                    
                    foreach($request["files"] as $id) {
                        $attachement = Attachements::find($id);
                        $attachement->statement_id = $statement->id;
                        $attachement->save();
                    }

                    StatementProduct::where("statement_id", $id)->delete();
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
    }

    public function filterStatement(Request $request) {
        $statement = Statement::orderBy('id', 'DESC');
        
        if($request->company != null) {
            $statement->where("overhead_number", "like", "%" . $request->overhead_number . "%");
        }
        
        if($request->overhead_date != null) {
            $statement->where("overhead_date", $request->overhead_date);
        }

        if($request->store_address != null) {
            $statement->where("store_address", "like", "%" . $request->store_address . "%");
        }

        if($request->full_amount != null) {
            $statement->where("full_amount", "like", "%" . $request->full_amount . "%");
        }
        
        if($request->beneficiary_name != null) {
            $statement->where("beneficiary_name", "like", "%" . $request->beneficiary_name . "%");
        }
        
        if($request->status != null) {
            $statement->where("status", "like", "%" . $request->status . "%");
        }

        return $statement->paginate(30);
    }

    public function generatePdf(int $id) {
        $data = Statement::find($id);

        return PDF::loadView("pdf.statement", [ "data" => $data ])->setOption(['defaultFont' => 'sans-serif'])->stream("statement.pdf");
    }

    public function changeStatus(Request $request, int $id) {        
        try {
            $change = Statement::find($id);

            $log = new StatementLog();
            $log->user_id = Auth::id();
            $log->operator_id = $change->operator_id;
            $log->comment = $request->comment;
            $log->statement_id = $id;
            $log->save();

            if($request->status == "approved") {
                $change->status = "approved";
                $change->operator_id = $change->operator_id;
            }

            if($request->status == "rejected") {
                $change->status = "rejected";
                $change->comment = $request->comment;
                $change->operator_id = $change->operator_id;
            }
            
            if($request->status == "stopped") {
                $change->status = "stopped";
                $change->comment = $request->comment;
                $change->operator_id = $change->operator_id;
            }
            
            $change->save();

            return response()->json([
                "success" => "შეიცვალა."
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "error" => "ვერ შეიცვალა."
            ], 422);
        }
    }

    public function statistic() {
        if(Auth::user()->permission == "operator") {
            return [
                "rejected" => Statement::where("status", "rejected")->where("operator_id", Auth::id())->get()->count(),
                "approved" => Statement::where("status", "approved")->where("operator_id", Auth::id())->get()->count(),
                "stopped" => Statement::where("status", "stopped")->where("operator_id", Auth::id())->get()->count(),
                "new" => Statement::where("status", "new")->where("operator_id", Auth::id())->get()->count(),
            ];
        }

        if(Auth::user()->permission == "coordinator") {
            return [
                "rejected" => Statement::where("status", "rejected")->get()->count(),
                "approved" => Statement::where("status", "approved")->get()->count(),
                "stopped" => Statement::where("status", "stopped")->get()->count(),
                "new" => Statement::where("status", "new")->get()->count(),
            ];
        }

        if(Auth::user()->permission == "company") {
            return [
                "rejected" => Statement::where("status", "rejected")->where("user_id", Auth::id())->get()->count(),
                "approved" => Statement::where("status", "approved")->where("user_id", Auth::id())->get()->count(),
                "stopped" => Statement::where("status", "stopped")->where("user_id", Auth::id())->get()->count(),
                "new" => Statement::where("status", "new")->where("user_id", Auth::id())->get()->count(),
            ];
        }
    }

    public function changeMassiveStatus(Request $request) {
        if($request->operator_id != null && $request->statements != null) {
            $change = Statement::whereIn("id", $request->statements)->where("status", "new")->first();
            $change->operator_id = $request->operator_id;
            $change->save();
        }
    }

    public function downloadExcel($from = null, $to = null, $status = null, $user_id = null) {
        if($user_id != null && User::find($user_id)->permission == "company") {
            return Excel::download(new StatementExport($from, $to, $status, $user_id), "data.xlsx");
        }

        return Excel::download(new StatementExport($from, $to, $status), "data.xlsx");
    }
}