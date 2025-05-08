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
Route::get("/signout", [AuthController::class, "signout"])->middleware("auth:api"); // სისტემიდან გასვლის მარშუტი

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

Route::post("/statement/file/upload", [StatementController::class, "uploadFile"]); // განაცხადისთვის ფაილის ატვირთვის მარშუტი
Route::get("/statement/file/delete/{id}", [StatementController::class, "deleteFile"]); // განაცხადზე მიმაგრებული ფაილის წაშლის მარშუტი

// პაროლის აღდგენის მაღშუტები
Route::group(["prefix" => "password"], function() {
    Route::post("/send/reset", [PasswordController::class, "sendReset"]);
    Route::post("/reset", [PasswordController::class, "reset"]);
    Route::get("/reset/check/{token}/{email}", [PasswordController::class, "check"]);
});