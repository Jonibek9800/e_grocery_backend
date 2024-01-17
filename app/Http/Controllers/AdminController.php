<?php

namespace App\Http\Controllers;

use App\Models\CarouselPoster;
use App\Models\Category;
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

    public function get_users()
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

    public function get_user_role()
    {
        RoleOfUsers::get();
    }
    public function create_user(Request $request)
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

    public function remove_user($id)
    {
        // $user = new User();
        // return $user;
        $user = User::find($id);
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            "success" => true,
            "message" => "dropped user successfully"
        ]);
    }

    public function create_product(Request $request)
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

            Product::create([
                "poster_path" => $poster_name,
                "name" => $request->get('name'),
                "price" => $request->get('price'),
                "description" => $request->get('description'),
                "category_id" => $request->get('category_id'),
            ]);
            return response(['success' => true]);
        } else {
            return response(['success' => false, 'message' => 'no images']);
        }
    }

    public function update_product(Request $request, $product_id)
    {
        $product = Product::find($product_id);
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
                "poster_path" => $request->get('poster_path'),
            ]);
        }
    }

    public function remove_product($product_id)
    {
        $product = Product::find($product_id);
        $product->delete();

        return response()->json([
            "success" => true,
            "message" => "droped product successfully"
        ]);
    }

    public function create_category(Request $request)
    {
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
    }

    public function update_category(Request $request, $category_id)
    {
        $category = Category::find($category_id);
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
                "message" => "category updating successfully"
            ]);
        } else {
            $category->update([
                "poster_path" => $category->poster_path,
                "title" => $request->get("title"),
            ]);
        }
    }
    public function remove_category($category_id)
    {
        $category = Product::find($category_id);
        $category->delete();

        return response()->json([
            "success" => true,
            "message" => "droped category successfully"
        ]);
    }

    public function get_carousel_poster(Request $request)
    {
        $carousel_poster = CarouselPoster::get();
        return response()->json([
            "success" => true,
            "carousel_poster" => $carousel_poster
        ]);
    }
    public function add_carousel_poster(Request $request)
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

            CarouselPoster::create([
                "poster_path" => $poster_name,
                "start_date" => $request->get('start_date'),
                "expiration_date" => $request->get('expiration_date'),
            ]);
            return response(['success' => true]);
        } else {
            return response(['success' => false, 'message' => 'no images']);
        }
    }

    public function update_carousel_poster(Request $request, $poster_id)
    {
        $poster = CarouselPoster::find($poster_id);
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

            $poster::update([
                "poster_path" => $poster_name,
                "start_date" => $request->get('start_date'),
                "expiration_date" => $request->get('expiration_date'),
            ]);
            return response(['success' => true]);
        } else {
            return response(['success' => false, 'message' => 'no images']);
        }
    }

    public function remove_poster($poster_id)
    {
        $poster = CarouselPoster::find($poster_id);
        if ($poster != null) {
            $poster->delete();
        }
        return response()->json([
            "success" => true,
            "message" => "droped poster successfully"
        ]);
    }
}
