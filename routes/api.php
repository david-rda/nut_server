<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StatementController;

Route::post("/signin", [AuthController::class, "signin"]);
Route::post("/signup", [AuthController::class, "signup"]);

// Route::post("/signout", [AuthController::class, "signout"])->middleware("auth:api");
Route::post("/change_password", [AuthController::class, "changePassword"])->middleware("auth:api");

Route::get("/user/list", [UserController::class, "userList"])->middleware("auth:api");
Route::get("/user/get/{id}", [UserController::class, "getUser"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::put("/user/edit/{id}", [UserController::class, "editUser"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::post("/search/user", [UserController::class, "searchUser"])->middleware("auth:api");
Route::post("/operator/add", [UserController::class, "addOperator"])->middleware("auth:api");

// პროდუქტების მარსუტები
Route::post("/product/add", [ProductController::class, "store"])->middleware("auth:api");
Route::put("/product/edit/{id}", [ProductController::class, "update"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::get("/product/get/{id}", [ProductController::class, "show"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::get("/product/list", [ProductController::class, "index"])->middleware("auth:api");

// განაცხადების მარსუტები
Route::post("/statement/add", [StatementController::class, "store"])->middleware("auth:api");
Route::put("/statement/edit/{id}", [StatementController::class, "update"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::get("/statement/get/{id}", [StatementController::class, "show"])->where(["id" => "[0-9]+"])->middleware("auth:api");
Route::get("/statement/list", [StatementController::class, "index"])->middleware("auth:api");