<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Excel;
use App\Exports\UserExport;

class UserController extends Controller
{
    public function userList() {
        return User::orderBy("id", "DESC")->paginate(30);
    }

    public function addOperator(Request $request) {
        $this->validate($request, [
            "name" => "required",
            "email" => "required|email",
            "mobile" => "required",
            "password" => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/",
        ]);

        $add = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "password" => bcrypt($request->password),
            "identification_code" => intval(rand()),
            "personal_id" => intval(rand()),
            "permission" => "operator"
        ]);

        if($add) {
            return response()->json([
                "success" => "დაემატა."
            ], 200);
        }else {
            return response()->json([
                "error" => "ვერ დაემატა."
            ], 422);
        }
    }

    public function searchUser(Request $request) {
        $user = User::orderBy('id', 'DESC');
        
        $request->company_name && $user->where("company_name","like","%".trim($request->company_name)."%");
        $request->name && $user->where("name","like","%".trim($request->name)."%");
        $request->name && $request->company && $user->where("name","like","%".trim($request->name)."%")->where("company_name","like","%".trim($request->company)."%");
        $request->name && $request->mobile && $user->where("name","like","%".trim($request->name)."%")->where("mobile","like","%".trim($request->mobile)."%");
        $request->mobile && $user->where("mobile","like","%".trim($request->mobile)."%");
        $request->mobile && $request->company && $user->where("mobile","like","%".trim($request->mobile)."%")->where("company_name","like","%".trim($request->company)."%");
        $request->personal_id && $request->mobile && $user->where("personal_id","like","%".trim($request->personal_id)."%")->where("mobile","like","%".trim($request->mobile)."%");
        $request->personal_id && $user->where("personal_id","like","%".trim($request->personal_id)."%");
        $request->personal_id && $request->email && $user->where("personal_id","like","%".trim($request->personal_id)."%")->where("email","like","%".trim($request->email)."%");
        $request->personal_id && $request->mobile && $user->where("personal_id","like","%".trim($request->personal_id)."%")->where("mobile","like","%".trim($request->mobile)."%");
        $request->email && $user->where("email","like","%".trim($request->email)."%");
        $request->id_code && $user->where("identification_code","like","%".trim($request->id_code)."%");
        $request->id_code && $request->email && $user->where("identification_code","like","%".trim($request->id_code)."%")->where("email","like","%".trim($request->email)."%");
        $request->permission && $user->where("permission","like","%".trim($request->permission)."%");
        $request->status && $user->where("status","like","%".trim($request->status)."%");
        $request->permission && $request->status && $user->where("permission","like","%".trim($request->permission)."%")->where("status","like","%".trim($request->status)."%");
        $request->permission && $request->mobile && $user->where("permission","like","%".trim($request->permission)."%")->where("mobile","like","%".trim($request->mobile)."%");

        return $user->paginate(30);
    }

    public function getUser(int $id) {
        return User::find($id);
    }

    public function change(int $id) {
        try {
            $user = User::find($id);
            $user->status = ($user->status == "active") ? "pending" : "active";
            $user->save();

            return response()->json([
                "success" => "ავტორიზირდა.",
                "users" => User::orderBy("id", "DESC")->paginate(30)
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "error" => "ვერ ავტორიზირდა."
            ], 422); 
        }
    }

    public function editUser(Request $request, int $id) {
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_name = $request->company_name;
        $user->identification_code = $request->identification_code;
        $user->legal_address = $request->legal_address;
        $user->actual_address = $request->actual_address;
        $user->personal_id = $request->personal_id;
        $user->mobile = $request->mobile;
        
        if($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        if($user) {
            return response()->json([
                "success" => "რეგისტრაცია განხორციელდა."
            ], 200);
        }else {
            return response()->json([
                "error" => "რეგისტრაცია ვერ განხორციელდა."
            ], 422);
        }
    }

    public function operators() {
        return User::where("permission", "like", "%operator%")->get();
    }

    public function userReport(Request $request) {
        return Excel::download(new UserExport(), "users.xlsx");
    }
}
