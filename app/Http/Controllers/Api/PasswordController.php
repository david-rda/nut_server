<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\User;
use Mail;
use Hash;
use Str;

class PasswordController extends Controller
{
    /**
     * @method POST
     * @param Request
     * @return json
     * 
     * პაროლის აღდგენისათვის კოდის გაგზავნის მეთოდი
     */
    public function sendReset(Request $request) {
        $this->validate($request, [
            "email" => "required|email",
        ]);

        try {
            $token = str_replace("/", "", Hash::make(Str::random(10)));

            Mail::send("mail.template", ["token" => $token, "email" => $request->email], function($message) use($request, $token) {
                $message->to(strtolower($request->email));
                $message->from("info@mailgun.rda.gov.ge", "სოფლის განვითარების სააგენტო - (RDA)");
                $message->subject("პაროლის აღდგენა");
            });

            $passwordReset = new PasswordReset();
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->save();

            return response()->json([
                "success" => [
                    "message" => [
                        "success" => [
                            "კოდი გაიგზავნა მითითებულ ელ. ფოსტაზე"
                        ]
                    ],
                ]
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "errors" => [
                    "error" => [
                        "დაფიქსირდა შეცდომა."
                    ]
                ]
            ], 422);
        }
    }

    /**
     * @method GET
     * @return json
     * @param null
     */
    public function check(string $token, string $email) {
        $reset_data = PasswordReset::where("token", $token)->where("email", $email)->orderBy("id", "DESC")->first();

        if(($token == $reset_data->token) && ($reset_data->email == $email)) {
            return response()->json([
                "status" => "Ok"
            ], 200);
        }else {
            return response()->json([
                "status" => "failed"
            ], 422);
        }
    }

    /**
     * @method POST
     * @return json
     * @param null
     * 
     * პაროლის აღდგენის მეთოდი
     */
    public function reset(Request $request) {
        $this->validate($request, [
            "new" => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/",
            "confirm" => "required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[!@#?$%&*)(]/|same:new"
        ]);

        try {
            $user = User::where("email", $request->email)->first();
            $user->password = Hash::make($request->new);
            $user->save();

            $delete_token = PasswordReset::where("email", $request->email)->where("token", $request->token)->orderBy("id", "DESC")->first()->delete();

            return response()->json([
                "success" => [
                    "message" => [
                        "success" => [
                            "პაროლის აღდგენა განხორციელდა."
                        ]
                    ],
                ]
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "errors" => [
                    "error" => [
                        "დაფიქსირდა შეცდომა."
                    ]
                ]
            ], 422);
        }
    }
}