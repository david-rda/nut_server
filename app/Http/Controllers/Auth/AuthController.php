<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Hash;

class AuthController extends Controller
{
    public function signin(Request $request) {
        $data = $this->validate($request, [
            "email" => "required|email",
            "password" => "required"
        ]);

        if(Auth::attempt($data)) {
            $token = Auth::user()->createToken("TOKEN")->accessToken;

            return response()->json([
                "user" => Auth::user(),
                "token" => $token
            ], 200);
        }else {
            return response()->json([
                "errors" => [
                    "error" => [
                        "ელ.ფოსტა ან პაროლი არასწორია"
                    ]
                ]
            ], 422);
        }
    }

    public function signup(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'company_name' => 'required',
            'identification_code' => 'required',
            'legal_address' => 'required',
            'actual_address' => 'required',
            'personal_id' => 'required|unique:users',
            "password" => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/",
            "mobile" => "required|min:9|max:9"
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'identification_code' => $request->identification_code,
            'legal_address' => $request->legal_address,
            'actual_address' => $request->actual_address,
            'personal_id' => $request->personal_id,
            'password' => bcrypt($request->password),
            "mobile" => $request->mobile,
        ]);

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

    public function changePassword(Request $request) {
        $this->validate($request, [
            'current_password' => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/",
            'new_password' => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/",
            'confirm_password' => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/|same:new_password"
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                "errors" => [
                    "error" => [
                        "მიმდინარე პაროლი მიუთითეთ სწორად!"
                    ]
                ]
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            "success" => [
                "message" => [
                    "success" => [
                        "Successfully logged in!"
                    ]
                ],
            ]
        ], 200);
    }

    public function signout(Request $request) {
        Auth::logout();

        return response()->json([
            "success" => "logged out!"
        ], 200);
    }
}
