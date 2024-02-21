<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\MyPlaceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::get('/get/token', [UserController::class, 'getToken']);

Route::get("/get/sliders", [AdminController::class, 'getSliders']);
Route::get("/get/carousel/posters/{image}", [MyPlaceController::class, 'getCarouselImage']);

Route::get('/get/categories', [MyPlaceController::class, 'getCategories']);
Route::get('/get/category/image/{image}', [MyPlaceController::class, 'getCategoryImage']);

Route::get('/get/products', [MyPlaceController::class, 'getProducts']);
Route::get('/get/products/{category_id}', [MyPlaceController::class, 'getProductsByCategory']);
Route::get('/get/product/image/{image}', [MyPlaceController::class, 'getProductImage']);
 
Route::get("/get/user/image/{image}", [MyPlaceController::class, "getUserImage"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/logout", [UserController::class, "logoutUser"]);
    Route::post("/create/check_details", [OrderController::class, "checkDetails"]);
    Route::get("/get/checks", [OrderController::class, "getChecks"]);
    Route::get("/get/favorite", [MyPlaceController::class, "getFavoriteProducts"]);
    Route::post("/toggle/favorite", [MyPlaceController::class, "toggleFavoriteProducte"]);

    Route::post("/add/product", [AdminController::class, "createProduct"]);
    Route::post("/update/product/{id}", [AdminController::class, "updateProduct"]);
    Route::delete("/delete/product/{id}", [AdminController::class, "removeProduct"]);

    Route::post("/add/category", [AdminController::class, "createCategory"]);
    Route::post("/update/category/{id}", [AdminController::class, "updateCategory"]);
    Route::delete("/delete/category/{id}", [AdminController::class, "removeCategory"]);

    Route::post("/add/slider/poster", [AdminController::class, "addCarouselPoster"]);
    Route::post("/update/slider/poster/{id}", [AdminController::class, "updateCarouselPoster"]);
    Route::delete("/delete/slider/poster/{id}", [AdminController::class, "removePoster"]);

    Route::get("/get/users", [AdminController::class, "getUsers"]);
    Route::get("/get/user/role", [AdminController::class, "getUserRole"]);
    Route::post("/update/{user_id}", [UserController::class, "updateUser"]);
    Route::post("/create/user", [AdminController::class, "createUser"]);
    Route::delete("/delete/user/{id}", [AdminController::class, "removeUser"]);

    Route::get("/get/checks", [AdminController::class, "getOrders"]);
    Route::post("/update/check/{id}", [AdminController::class, "updateOrder"]);
});
