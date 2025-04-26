<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StatementController;
use App\Models\User;
// use App\Models\Product;
// use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Facades\Excel;
// use Carbon\Carbon;

Route::post("/signin", [AuthController::class, "signin"]); // ავტორიზაციის მარშუტი
Route::post("/signup", [AuthController::class, "signup"]); // რეგისტრაციის მარშუტი
Route::get("/signout", [AuthController::class, "signout"]); // სისტემიდან გასვლის მარშუტი

Route::post("/change_password", [AuthController::class, "changePassword"])->middleware("auth:api"); // პაროლის ცვლილების მარშუტი

Route::get("/operator/list", [UserController::class, "operators"])->middleware("auth:api"); // ოპერატორების მონაცემების მარშუტი

Route::get("/user/list", [UserController::class, "userList"])->middleware("auth:api"); // სისტემაში რეგისტრირებულ მომხმარებელთა ინფორმაციის გამოტანის მარშუტი
Route::get("/user/get/{id}", [UserController::class, "getUser"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // კონკრეტული მომხმარებლის ინფორმაციის გამოტანის მარშუტი
Route::post("/user/edit/{id}", [UserController::class, "editUser"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // კონკრეტული მომხმარებლის ინფორმაციის რედაქტირების მარშუტი
Route::post("/search/user", [UserController::class, "searchUser"])->middleware("auth:api"); // მომხმარებელთა ფილტრაციის მარშუტი
Route::post("/operator/add", [UserController::class, "addOperator"])->middleware("auth:api"); // სისტემაში ოპერატორის დამატების მარშუტი
Route::get("/user/change/status/{id}", [UserController::class, "change"])->middleware("auth:api"); // მომხმარებლისთვის სტატუსის ცვლილების მარშუტი

// პროდუქტების მარშუტები
Route::post("/product/add", [ProductController::class, "store"])->middleware("auth:api"); // პროდუქტის დამატების მარშუტი
Route::post("/product/edit/{id}", [ProductController::class, "update"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // პროდუქტის დარედაქტირების მარშუტი
Route::get("/product/get/{id}", [ProductController::class, "show"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // კონკრეტული პროდუქტის ინფორმაციის გამოტანის მარშუტი
Route::get("/product/list", [ProductController::class, "index"])->middleware("auth:api"); // ბაზაში არსებული პროდუქტების გამოტანის მარშუტი v_select მენიუსთვის
Route::get("/products", [ProductController::class, "byStatus"])->middleware("auth:api"); // ბაზაში არსებული პროდუქტების გამოტანის მარშუტი

// განაცხადების მარშუტები
Route::post("/statement/add", [StatementController::class, "store"])->middleware("auth:api"); // განაცხადის დამატების მარშუტი
Route::post("/statement/edit/{id}", [StatementController::class, "update"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // განაცხადის დარედაქტირების მარშუტი
Route::get("/statement/get/{id}", [StatementController::class, "show"])->where(["id" => "[0-9]+"])->middleware("auth:api"); // კონკრეტული განაცხადის გამოტანის მარშუტი
Route::get("/statement/list", [StatementController::class, "index"])->middleware("auth:api"); // განაცხადების გამოტანის მარშუტი
Route::post("/statement/search", [StatementController::class, "filterStatement"])->middleware("auth:api"); // განაცხადების ფილტრაციის მარშუტი
Route::post("/statement/file/upload", [StatementController::class, "uploadFile"]); // განაცხადისთვის ფაილის ატვირთვის მარშუტი
Route::get("/statement/file/delete/{id}", [StatementController::class, "deleteFile"]); // განაცხადზე მიმაგრებული ფაილის წაშლის მარშუტი
// Route::get("/statement/pdf/{id}", [StatementController::class, "generatePdf"]);
Route::post("/statement/change/status/{id}", [StatementController::class, "changeStatus"])->middleware("auth:api"); // განაცხადისთვის სტატუსის ცვლილების მარშუტი

Route::get("/statement/statistic", [StatementController::class, "statistic"])->middleware("auth:api"); // განაცხადების სტატუსების სტატისტიკის მარშუტი
Route::post("/statement/change/massive", [StatementController::class, "changeMassiveStatus"])->middleware("auth:api");// განაცხად(ებ)ისთვის სტატუსის ცვლილების მარშუტი
Route::get("/statement/report/{from?}/{to?}/{user_id?}", [StatementController::class, "downloadExcel"]); // განაცხადების რეპორტის მარშუტი

// Route::get("/import", function() {

//     Product::where("status", "enabled")->update([
//         "status" => "disabled"
//     ]);

//     $data = Excel::toCollection("datas", public_path("import_products.xlsx"));

//     for($i = 1; $i < sizeof($data[0]); $i++) {
//         Product::insert([
//             "name" => trim($data[0][$i][0]),
//             "status" => "enabled",
//             "created_at" => Carbon::now()
//         ]);
//     }
// });