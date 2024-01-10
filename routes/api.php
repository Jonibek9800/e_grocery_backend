<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\MyPlaceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::get('/get/token', [UserController::class, 'getToken']);

Route::get('/get/categories', [MyPlaceController::class, 'get_categories']);
Route::get('/get/category/image/{image}', [MyPlaceController::class, 'get_category_image']);

Route::get('/get/products', [MyPlaceController::class, 'get_products']);
Route::get('/get/products/{category_id}', [MyPlaceController::class, 'get_products_by_category']);
Route::get('/get/product/image/{image}', [MyPlaceController::class, 'get_product_image']);

Route::get("/get/user/image/{image}", [MyPlaceController::class, "get_user_image"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/logout", [UserController::class, "logoutUser"]);
    Route::post("/update/{user_id}", [UserController::class, "update_user"]);
    Route::post("/create/check_details", [OrderController::class, "check_details"]);
    Route::get("/get/checks", [OrderController::class, "get_checks"]);
    Route::get("/get/favorite", [MyPlaceController::class, "getFavoriteProducts"]);
    Route::post("/toggle/favorite", [MyPlaceController::class, "toggleFavoriteProducte"]);

    Route::post("/create/product", [AdminController::class, "create_product"]);
    Route::post("/update/product", [AdminController::class, "update_product"]);

    Route::post("/create/category", [AdminController::class, "create_category"]);
    Route::post("/update/product/{category_id}", [AdminController::class, "update_category"]);

    Route::get("/get/users", [AdminController::class, "get_users"]);
    Route::get("get/user/role", [AdminController::class, "get_user_role"]);
    Route::post("/create/user", [AdminController::class, "create_user"]);

});