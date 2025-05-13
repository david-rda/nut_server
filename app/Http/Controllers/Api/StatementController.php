<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Statement;
use App\Models\StatementProduct;
use App\Models\StatementLog;
use App\Models\ChangeOperatorLog;
use App\Models\Product;
use App\Models\Attachements;
use App\Models\User;
use App\Exports\StatementExport;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;
use Excel;
use Mail;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @method GET
     * @return json
     */
    public function index(int $id = null)
    {
        if(Auth::user()->permission == "company") {
            return Statement::where("user_id", Auth::id())->orderBy("id", "DESC")->paginate(30)->through(function($item) {
                return $item->makeHidden(["logs", "files", "statement_products", "created_at", "updated_at", "attachement_id", "comment", "deleted_at"]);
            });;
        }

        if(Auth::user()->permission == "coordinator") {
            return Statement::orderBy("id", "DESC")->paginate(30)->through(function($item) {
                return $item->makeHidden(["logs", "files", "statement_products", "created_at", "updated_at", "attachement_id", "comment", "deleted_at"]);
            });
        }

        if(Auth::user()->permission == "operator") {
            return Statement::where("operator_id", Auth::id())->orderBy("id", "DESC")->paginate(30)->through(function($item) {
                return $item->makeHidden(["logs", "files", "statement_products", "created_at", "updated_at", "attachement_id", "comment", "deleted_at"]);
            });;
        }
    }

    /**
     * Store a newly created resource in storage.
     * @method POST
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

        if(Statement::where("overhead_number", "like", "%" . $request->overhead_number . "%")->get()->count() > 0) {
            return response()->json([
                "errors" => [
                    "error" => [
                        "მითითებული ზედნადების ნომრით განაცხადი უკვე დამატებულია."
                    ]
                ]
            ], 422);
        }

        if(sizeof($request["files"]) == 0) {
            return response()->json([
                "errors" => [
                    "error" => [
                        "გთხოვთ ატვირთოთ ფაილი"
                    ]
                ]
            ], 422);
        }

        try {
            DB::transaction(function() use($request) {
                $statement = new Statement();

                $statement->user_id = Auth::id();
                $statement->status = "new";
                $statement->overhead_number = $request->overhead_number;
                $statement->overhead_date = $request->overhead_date;
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
        $statement = Statement::where("id", Attachements::find($id)?->statement_id)->first();
        $delete = Attachements::find($id)?->delete();

        if($delete) {
            return response()->json([
                "success" => "წაიშალა.",
                "data" => $statement
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

            if($data->getClientOriginalExtension() == "pdf" || $data->getClientOriginalExtension() == "jpg" || $data->getClientOriginalExtension() == "png") {
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
     * @method GET
     */
    public function show(int $id)
    {
        $data = "";

        if(Auth::user()->permission == "company") {
            $data = Statement::where("id", $id)->where("user_id", Auth::id())->first();
        }

        if(Auth::user()->permission == "coordinator" || Auth::user()->permission == "operator") {
            $data = Statement::find($id);
        }

        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        // if(Auth::user()->permission == "company" && $statement->status == "rejected") {
            $this->validate($request, [
                "overhead_number" => "required",
                "overhead_date" => "required",
                "store_address" => "required",
                "beneficiary_name" => "required",
                "card_number" => "required|min:4|max:4",
                "full_amount" => "required",
            ]);

            if(Statement::find($id)?->files == null) {
                if(sizeof($request["file"]) == 0) {
                    return response()->json([
                        "errors" => [
                            "error" => [
                                "გთხოვთ ატვირთოთ ფაილი"
                            ]
                        ]
                    ], 422);
                }
            }
                        
            try {
                DB::transaction(function() use($request, $id) {    
                    $statement = Statement::find($id);
                    $statement->user_id = Auth::id();
                    $statement->status = "new";
                    $statement->overhead_number = $request->overhead_number;
                    $statement->overhead_date = $request->overhead_date;
                    $statement->store_address = $request->store_address;
                    $statement->beneficiary_name = $request->beneficiary_name;
                    $statement->full_amount = $request->full_amount;
                    $statement->card_number = $request->card_number;
                    $statement->status = "new";
    
                    $statement->save();
                    
                    foreach($request["file"] as $file_id) {
                        $attachement = Attachements::find($file_id);
                        $attachement->statement_id = $id;
                        $attachement->save();
                    }
                    
                    StatementProduct::where("statement_id", $id)->delete();                    
                    foreach($request->statement_products as $key => $products) {            
                        StatementProduct::insert([
                            "product_id" => $products["product_id"],
                            "statement_id" => $id,
                            "price" => $products["price"],
                        ]);
                    }
                });
                
                DB::commit();
    
                return response()->json([
                    "success" => "განაცხადი დარედაქტირდა."
                ], 200);
            }catch(Exception $e) {
                return response()->json([
                    "error" => "განაცხადი ვერ დარედაქტირდა."
                ], 422);
            }
        // }
    }

    /**
     * @method POST
     * @return json
     * @param Reuest
     * 
     * განაცხადების ძებნის მეთოდი
     */
    public function filterStatement(Request $request) {
        $statement = Statement::orderBy('id', 'DESC');

        $requestProperties = ["overhead_number", "overhead_date", "card_number", "store_address", "full_amount", "beneficiary_name", "status"];
        
        if(Auth::user()->permission == "company") {
            if($request->company_name != null) {
                $statement->whereHas("company_user", function($query) use($request) {
                    $query->where("company_name", "like", "%" . trim($request->company_name) . "%")->where("user_id", Auth::id());
                });
            }

            for($i = 0; $i < sizeof($requestProperties); $i++) {
                if($request->{$requestProperties[$i]} != null) {
                    $statement->where($requestProperties[$i], "like", "%" . trim($request->{$requestProperties[$i]}) . "%")->where("user_id", Auth::id());
                }
            }
        }else if(Auth::user()->permission == "operator") {
            if($request->company_name != null) {
                $statement->whereHas("company_user", function($query) use($request) {
                    $query->where("company_name", "like", "%" . trim($request->company_name) . "%")->where("operator_id", Auth::id());
                });
            }

            for($i = 0; $i < sizeof($requestProperties); $i++) {
                if($request->{$requestProperties[$i]} != null) {
                    $statement->where($requestProperties[$i], "like", "%" . trim($request->{$requestProperties[$i]}) . "%")->where("operator_id", Auth::id());
                }
            }
        }else {
            if($request->company_name != null) {
                $statement->whereHas("company_user", function($query) use($request) {
                    $query->where("company_name", "like", "%" . trim($request->company_name) . "%");
                });
            }

            for($i = 0; $i < sizeof($requestProperties); $i++) {
                if($request->{$requestProperties[$i]} != null) {
                    $statement->where($requestProperties[$i], "like", "%" . trim($request->{$requestProperties[$i]}) . "%");
                }
            }
        }

        return $statement->paginate(30);
    }

    public function changeStatus(Request $request, int $id) {
        if($request->status != "approved") {
            $this->validate($request, [
                "comment" => "required"
            ]);
        }
        
        try {
            $change = Statement::find($id);

            $log = new StatementLog();
            $log->user_id = Auth::id();
            $log->operator_id = $change->operator_id;
            $log->comment = ($request->status == "approved") ? "" : $request->comment;
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

                $targetEmail = User::find($change->user_id);

                Mail::send("mail.notify", ["comment" => $request->comment, "statement_id" => $id], function($message) use($request, $id, $targetEmail) {
                    $message->to($targetEmail->email);
                    $message->from("info@rda.gov.ge", "სოფლის განვითარების სააგენტო - (RDA)");
                    $message->subject("განაცხადის დახარვეზება");
                });
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
                "user" => Auth::user(),
            ];
        }

        if(Auth::user()->permission == "coordinator") {
            return [
                "rejected" => Statement::where("status", "rejected")->get()->count(),
                "approved" => Statement::where("status", "approved")->get()->count(),
                "stopped" => Statement::where("status", "stopped")->get()->count(),
                "new" => Statement::where("status", "new")->get()->count(),
                "user" => Auth::user(),
            ];
        }

        if(Auth::user()->permission == "company") {
            return [
                "rejected" => Statement::where("status", "rejected")->where("user_id", Auth::id())->get()->count(),
                "approved" => Statement::where("status", "approved")->where("user_id", Auth::id())->get()->count(),
                "stopped" => Statement::where("status", "stopped")->where("user_id", Auth::id())->get()->count(),
                "new" => Statement::where("status", "new")->where("user_id", Auth::id())->get()->count(),
                "user" => Auth::user(),
            ];
        }
    }

    public function changeMassiveStatus(Request $request) {        
        if($request->operator_id != null && $request->statements != null) {
            foreach($request->statements as $id) {
                $log = new ChangeOperatorLog();
                $log->coordinator_id = Auth::id();
                $log->statement_id = $id;
                $log->operator_before = (is_null(Statement::where("id", $id)->first()?->operator_id)) ? "" : Statement::where("id", $id)->first()?->operator_id;
                $log->operator_after = $request->operator_id;
                $log->save();
                
                $change = Statement::where("id", $id)->first();
                $change->operator_id = $request->operator_id;
                $change->status = "operator";
                $change->save();
            }
        }
    }

    public function downloadExcel($from = null, $to = null, $user_id = null) {
        return Excel::download(new StatementExport($from, $to, $user_id), "data.xlsx");
    }
}