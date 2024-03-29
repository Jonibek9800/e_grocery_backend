<?php

namespace App\Http\Controllers;

use App\Models\CarouselPoster;
use App\Models\Category;
use App\Models\Check;
use App\Models\Product;
use App\Models\RoleOfUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class AdminController extends Controller
{

    public function getUsers()
    {
        $user = auth('sanctum')->user();
        $users = null;
        if ($user->role_of_user_id == 2) {
            $users = User::where("id", "!=", $user->id)->get();
        }
        return response()->json([
            "success" => true,
            "users" => $users
        ]);
    }

    public function getUserRole()
    {
        RoleOfUsers::get();
    }
    public function createUser(Request $request)
    {
        try {
            $user = User::where("email", $request->get("email"))->first();
            if ($user) {
                return response()->json([
                    "success" => false,
                    "message" => "A user with this email already exists",
                    "users" => $user
                ]);
            } else {
                if ($request->file('poster')) {
                    $poster = $request->file('poster');

                    $poster_format = $poster->getClientOriginalExtension();

                    $poster_name = "image_" . Str::random(30) . "." . $poster_format;
                    $save_poster = Image::make($poster);

                    $save_poster->resize(300, 250, function ($constrains) {
                        $constrains->aspectRatio();
                    });
                    $save_poster->save(storage_path('app/public/img/persons/' . $poster_name));

                    $user = User::create([
                        'name' => $request->get('name'),
                        'phone_number' => $request->get('phone_number'),
                        'email' => $request->get('email'),
                        'password' => Hash::make($request->get('password')),
                        'poster_path' => $poster_name,
                        'role_of_user_id' => $request->get('role_id'),
                    ]);
                    if ($user == null) {
                        return response()->json([
                            "success" => false,
                            "message" => "User not created please check your details and try again"
                        ]);
                    } else {
                        return response()->json([
                            "success" => true,
                            "message" => "user created successfully",
                            "user" => $user
                        ]);
                    }
                } else {
                    $user = User::create([
                        'name' => $request->get('name'),
                        'phone_number' => $request->get('phone_number'),
                        'email' => $request->get('email'),
                        'password' => Hash::make($request->get('password')),
                        'poster_path' => null,
                        'role_of_user_id' => $request->get('role_id'),
                    ]);

                    if ($user == null) {
                        return response()->json([
                            "success" => false,
                            "message" => "User not created please check your details and try again"
                        ]);
                    } else {
                        return response()->json([
                            "success" => true,
                            "message" => "user created successfully",
                            "user" => $user
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function removeUser($id)
    {
        $user = User::find($id);
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            "success" => true,
            "message" => "dropped user successfully"
        ]);
    }

    public function createProduct(Request $request)
    {
        if ($request->file('poster')) {
            $poster = $request->file('poster');

            $poster_format = $poster->getClientOriginalExtension();

            $poster_name = "image_" . Str::random(30) . "." . $poster_format;
            $save_poster = Image::make($poster);

            $save_poster->resize(300, 300, function ($constrains) {
                $constrains->aspectRatio();
            });
            $save_poster->save(storage_path('app/public/img/products/' . $poster_name));

            $product = Product::create([
                "poster_path" => $poster_name,
                "name" => $request->get('name'),
                "price" => $request->get('price'),
                "description" => $request->get('description'),
                "category_id" => $request->get('category_id'),
            ]);
            return response(['success' => true, "product" => $product,]);
        } else {
            return response(['success' => false, 'message' => 'no images1']);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);
        try {
            if ($request->file('poster')) {
                $poster = $request->file('poster');

                $poster_format = $poster->getClientOriginalExtension();

                $poster_name = "image_" . Str::random(30) . "." . $poster_format;
                $save_poster = Image::make($poster);

                $save_poster->resize(300, 300, function ($constrains) {
                    $constrains->aspectRatio();
                });
                if ($product->poster_path) {
                    if (File::exists(storage_path('app/public/img/products/' . $product->poster_path))) {
                        File::delete(storage_path('app/public/img/products/' . $product->poster_path));
                    }
                }
                $save_poster->save(storage_path('app/public/img/products/' . $poster_name));
                $product->update([
                    "poster_path" => $request->get('poster'),
                    "name" => $request->get('name'),
                    "price" => $request->get('price'),
                    "description" => $request->get('description'),
                    "category_id" => $request->get('category_id'),
                ]);

                return response()->json([
                    "success" => true,
                    "product" => $product
                ]);
            } else {
                $product->update([
                    "poster_path" => $product->poster_path,
                    "name" => $request->get('name'),
                    "price" => $request->get('price'),
                    "description" => $request->get('description'),
                    "category_id" => $request->get('category_id'),
                ]);

                return response()->json([
                    "success" => true,
                    "product" => $product
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function removeProduct($id)
    {
        try {
            $product = Product::find($id);
            if ($product->poster_path) {
                if (File::exists(storage_path('app/public/img/products/' . $product->poster_path))) {
                    File::delete(storage_path('app/public/img/products/' . $product->poster_path));
                }
            }
            $product->delete();

            return response()->json([
                "success" => true,
                "message" => "droped product successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function createCategory(Request $request)
    {
        try {
            if ($request->file('poster')) {
                $poster = $request->file('poster');

                $poster_format = $poster->getClientOriginalExtension();

                $poster_name = "image_" . Str::random(30) . "." . $poster_format;
                $save_poster = Image::make($poster);

                $save_poster->resize(300, 300, function ($constrains) {
                    $constrains->aspectRatio();
                });
                $save_poster->save(storage_path('app/public/img/categories/' . $poster_name));

                $category = Category::create([
                    "poster_path" => $poster_name,
                    "title" => $request->get('title'),
                ]);
                return response([
                    'success' => true,
                    "category" => $category,
                ]);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Photo is required to add'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);
        if ($request->file('poster')) {
            $poster = $request->file('poster');

            $poster_format = $poster->getClientOriginalExtension();

            $poster_name = "image_" . Str::random(30) . "." . $poster_format;
            $save_poster = Image::make($poster);

            $save_poster->resize(300, 300, function ($constrains) {
                $constrains->aspectRatio();
            });
            if ($category->poster_path) {
                if (File::exists(storage_path('app/public/img/categories/' . $category->poster_path))) {
                    File::delete(storage_path('app/public/img/categories/' . $category->poster_path));
                }
            }
            $save_poster->save(storage_path('app/public/img/categories/' . $poster_name));

            $category->update([
                "poster_path" => $poster_name,
                "title" => $request->get("title"),
            ]);
            return response()->json([
                "success" => true,
                "message" => "category updating successfully",
                "category" => $category
            ]);
        } else {
            $category->update([
                "poster_path" => $category->poster_path,
                "title" => $request->get("title"),
            ]);
            return response()->json([
                "success" => true,
                "message" => "category updating successfully",
                "category" => $category
            ]);
        }
    }
    public function removeCategory($id)
    {
        $category = Category::find($id);
        if ($category->poster_path) {
            if (File::exists(storage_path('app/public/img/categories/' . $category->poster_path))) {
                File::delete(storage_path('app/public/img/categories/' . $category->poster_path));
            }
        }
        $category->delete();

        return response()->json([
            "success" => true,
            "message" => "droped category successfully"
        ]);
    }

    public function getSliders()
    {
        $carousel_poster = CarouselPoster::get();
        return response()->json([
            "success" => true,
            "carousel_poster" => $carousel_poster
        ]);
    }
    public function addCarouselPoster(Request $request)
    {
        if ($request->file('poster')) {
            $poster = $request->file('poster');

            $poster_format = $poster->getClientOriginalExtension();

            $poster_name = "image_" . Str::random(30) . "." . $poster_format;
            $save_poster = Image::make($poster);

            $save_poster->resize(300, 250, function ($constrains) {
                $constrains->aspectRatio();
            });
            $save_poster->save(storage_path('app/public/img/carousel/' . $poster_name));

            $poster = CarouselPoster::create([
                "poster_path" => $poster_name,
                "start_date" => $request->get('start_date'),
                "expiration_date" => $request->get('expiration_date'),
            ]);
            return response()->json([
                'success' => true,
                "poster" => $poster
            ]);
        } else {
            return response(['success' => false, 'message' => 'no images']);
        }
    }

    public function updateCarouselPoster(Request $request, $id)
    {
        try {
            $poster = CarouselPoster::find($id);
            if ($request->file('poster')) {
                $poster_path = $request->file('poster');

                $poster_format = $poster_path->getClientOriginalExtension();

                $poster_name = "image_" . Str::random(30) . "." . $poster_format;
                $save_poster = Image::make($poster_path);

                $save_poster->resize(300, 250, function ($constrains) {
                    $constrains->aspectRatio();
                });
                if ($poster->poster_path) {
                    if (File::exists(storage_path('app/public/img/persons/' . $poster->poster_path))) {
                        File::delete(storage_path('app/public/img/persons/' . $poster->poster_path));
                    }
                }
                $save_poster->save(storage_path('app/public/img/carousel/' . $poster_name));

                $poster->update([
                    "poster_path" => $poster_name,
                    "start_date" => $request->get('start_date'),
                    "expiration_date" => $request->get('expiration_date'),
                ]);
                return response()->json(['success' => true, "poster" => $poster]);
            } else {
                $poster->update([
                    "poster_path" => $poster->poster_path,
                    "start_date" => $request->get('start_date'),
                    "expiration_date" => $request->get('expiration_date'),
                ]);
                return response()->json([
                    'success' => true,
                    "poster" => $poster,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function removePoster($id)
    {
        try {
            $poster = CarouselPoster::find($id);
            if ($poster != null) {
                $poster->delete();
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "droped poster error"
                ]);
            }
            return response()->json([
                "success" => true,
                "message" => "droped poster successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getOrders(Request $request)
    {
        try {
            $checks = Check::with([
                'details' => function ($element) {
                    $element->with('product');

                }
            ])
                ->get();
            if ($checks != null) {
                return response()->json([
                    'success' => true,
                    'checks' => $checks
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ckecks is not defined'
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        try {
            $check = Check::find($id);
            if ($check != null) {
                $check->update([
                    'order_status' => $request->get('order_status'),
                ]);
                return response()->json([
                    'success' => true,
                    'check' => $check
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Check is not found'
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}