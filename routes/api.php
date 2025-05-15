<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StatementController;
use App\Http\Controllers\Api\PasswordController;

Route::post("/signin", [AuthController::class, "signin"]); // ავტორიზაციის მარსუტი
Route::post("/signup", [AuthController::class, "signup"]); // რეგისტრაციის მარშუტი
Route::get("/signout", [AuthController::class, "signout"]); // სისტემიდან გასვლის მარშუტი

Route::post("/change_password", [AuthController::class, "changePassword"])->middleware("auth:api"); // პაროლის ცვლილების მარშუტი

// მომხმარებლებთან დაკავშირებული მარშუტები
Route::group(["prefix" => "user", "middleware" => "auth:api"], function() {
    Route::get("/list", [UserController::class, "userList"]); // სისტემაში რეგისტრირებულ მომხმარებელთა ინფორმაციის გამოტანის მარშუტი
    Route::get("/get/{id}", [UserController::class, "getUser"])->where(["id" => "[0-9]+"]); // კონკრეტული მომხმარებლის ინფორმაციის გამოტანის მარშუტი
    Route::post("/edit/{id}", [UserController::class, "editUser"])->where(["id" => "[0-9]+"]); // კონკრეტული მომხმარებლის ინფორმაციის რედაქტირების მარშუტი
    Route::get("/change/status/{id}", [UserController::class, "change"]); // მომხმარებლისთვის სტატუსის ცვლილების მარშუტი
    Route::get("/report", [UserController::class, "userReport"]); // მომხმარებლების რეპორტის ჩამოტვირთვის მარშუტი
    Route::post("/search/user", [UserController::class, "searchUser"]); // მომხმარებელთა ფილტრაციის მარშუტი
    Route::post("/operator/add", [UserController::class, "addOperator"]); // სისტემაში ოპერატორის დამატების მარშუტი
    Route::get("/operator/list", [UserController::class, "operators"]); // ოპერატორების მონაცემების მარშუტი
});

// პროდუქტების მარშუტები
Route::group(["prefix" => "product", "middleware" => "auth:api"], function() {
    Route::post("/add", [ProductController::class, "store"]); // პროდუქტის დამატების მარშუტი
    Route::post("/edit/{id}", [ProductController::class, "update"])->where(["id" => "[0-9]+"]); // პროდუქტის დარედაქტირების მარშუტი
    Route::get("/get/{id}", [ProductController::class, "show"])->where(["id" => "[0-9]+"]); // კონკრეტული პროდუქტის ინფორმაციის გამოტანის მარშუტი
    Route::get("/list", [ProductController::class, "index"]); // ბაზაში არსებული პროდუქტების გამოტანის მარშუტი v_select მენიუსთვის
    Route::get("/products", [ProductController::class, "byStatus"]); // ბაზაში არსებული პროდუქტების გამოტანის მარშუტი
});

// განაცხადების მარსუტები
Route::group(["prefix" => "statement", "middleware" => "auth:api"], function() {
    Route::post("/add", [StatementController::class, "store"]); // განაცხადის დამატების მარშუტი
    Route::post("/edit/{id}", [StatementController::class, "update"])->where(["id" => "[0-9]+"]); // განაცხადის დარედაქტირების მარშუტი
    Route::get("/get/{id}", [StatementController::class, "show"])->where(["id" => "[0-9]+"]); // კონკრეტული განაცხადის გამოტანის მარშუტი
    Route::get("/list", [StatementController::class, "index"]); // განაცხადების გამოტანის მარშუტი
    Route::post("/search", [StatementController::class, "filterStatement"]); // განაცხადების ფილტრაციის მარშუტი
    Route::post("/change/status/{id}", [StatementController::class, "changeStatus"]); // განაცხადისთვის სტატუსის ცვლილების მარშუტი
    Route::get("/statistic", [StatementController::class, "statistic"]); // განაცხადების სტატუსების სტატისტიკის მარშუტი
    Route::post("/change/massive", [StatementController::class, "changeMassiveStatus"]);// განაცხად(ებ)ისთვის სტატუსის ცვლილების მარშუტი
    Route::get("/report/{from?}/{to?}/{user_id?}", [StatementController::class, "downloadExcel"]); // განაცხადების რეპორტის მარშუტი
});

// ფაილების ატვირთვა/წაშლის მარშუტები
Route::group(["prefix" => "statement"], function() {
    Route::post("/file/upload", [StatementController::class, "uploadFile"]); // განაცხადისთვის ფაილის ატვირთვის მარშუტი
    Route::get("/file/delete/{id}", [StatementController::class, "deleteFile"]); // განაცხადზე მიმაგრებული ფაილის წაშლის მარშუტი
});

// პაროლის აღდგენის მაღშუტები
Route::group(["prefix" => "password"], function() {
    Route::post("/send/reset", [PasswordController::class, "sendReset"]);
    Route::post("/reset", [PasswordController::class, "reset"]);
    Route::get("/reset/check/{token}/{email}", [PasswordController::class, "check"]);
});

// Route::get("/inserts", function() {
    // $data = Excel::toCollection("data", "old.xlsx");

    // for($i = 1; $i < sizeof($data[0]); $i++) {
    //     Product::where("id", $data[0][$i][0])->update([
    //         "name" => $data[0][$i][1]
    //     ]);
    // }

    // $arr = [];

    // $data = Excel::toCollection("data", "import_products.xlsx");

    // foreach(Product::where("status", "disabled")->where("created_at", ">", "2025-04-25")->get() as $key => $value) {
    //     array_push($arr, $value->name . " -- " . $data[0][$key + 1][0]);
    // }

    // dd($arr);

    // $data = Excel::toCollection("data", "products.xlsx");

    // // Product::where("status", "enabled")->update([
    // //     "status" => "disabled"
    // // ]);

    // $arr = [];

    // for($i = 0; $i < sizeof($data[0]); $i++) {
    //     array_push($arr, $data[0][$i][0]);
    // }

    // foreach($arr as $key) {
    //     Product::insert([
    //         "name" => $key,
    //         "status" => "enabled",
    //         "created_at" => \Carbon\Carbon::now(),
    //         "updated_at" => \Carbon\Carbon::now(),
    //     ]);
    // }
// });