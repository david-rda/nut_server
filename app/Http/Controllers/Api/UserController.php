<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function userList() {
        return User::all();
    }

    public function addOperator(Request $request) {
        $this->validate($request, [
            "name" => "required",
            "email" => "required|email",
            "mobile" => "required",
            "password" => "required",
        ]);

        $add = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "password" => bcrypt($request->password),
            "identification_code" => intval(rand()),
            "personal_id" => intval(rand())
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
        $user = User::orderBy('id','desc');
        
        if($request->company != null) {
            $user->where("company_name", "like", "%" . $request->company . "%");
        }
        
        if($request->name != null) {
            $user->where("name", "like", "%" . $request->name . "%");
        }

        if($request->mobile != null) {
            $user->where("mobile", "like", "%" . $request->mobile . "%");
        }

        if($request->personal_id != null) {
            $user->where("personal_id", "like", "%" . $request->personal_id . "%");
        }
        
        if($request->email != null) {
            $user->where("email", "like", "%" . $request->email . "%");
        }
        
        if($request->id_code != null) {
            $user->where("identification_code", "like", "%" . $request->id_code . "%");
        }
        
        if($request->permission != null) {
            $user->where("permission", "like", "%" . $request->permission . "%");
        }
        
        if($request->status != null) {
            $user->where("status", "like", "%" . $request->status . "%");
        }

        return $user->paginate(30);
    }

    public function getUser(int $id) {
        return User::find($id);
    }

    public function editUser(Request $request, int $id) {
        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email',
        //     'company_name' => 'required',
        //     'identification_code' => 'required',
        //     'legal_address' => 'required',
        //     'actual_address' => 'required',
        //     'personal_id' => 'required',
        //     "mobile" => "required|min:9|max:9"
        // ]);

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
}