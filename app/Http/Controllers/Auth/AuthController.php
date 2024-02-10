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
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'company_name' => 'required',
            'identification_code' => 'required|unique:users',
            'legal_address' => 'required',
            'actual_address' => 'required',
            'personal_id' => 'required',
            'password' => 'required',
            "mobile" => "required|min:9|max:9"
        ]);

        $data["password"] = bcrypt($request->password);

        $user = User::create($data);

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

    // public function signout(Request $request) {
    //     return Auth::user();

    //     Auth::logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     return response()->json([
    //         "success" => "სისტემიდან გასვლა განხორციელდა."
    //     ], 200);
    // }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => "required|same:new_password"
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
