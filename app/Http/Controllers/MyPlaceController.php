<?php

namespace App\Http\Controllers;

use App\Models\CarouselPoster;
use App\Models\Category;
use App\Models\Product;
use App\Models\WishList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MyPlaceController extends Controller
{

    public function get_product_image($image)
    {
        //
        $url = storage_path("app/public/img/products/" . $image);
        if (File::exists($url)) {
            return response()->file($url);
        }
    }

    public function get_category_image($image)
    {
        $url = storage_path("app/public/img/categories/" . $image);
        if (File::exists($url)) {
            return response()->file($url);
        } else {
            return response()->file(public_path("default_image/person.png"));
        }
    }

    public function get_user_image($image)
    {
        $url = storage_path("app/public/img/persons/" . $image);
        if (File::exists($url)) {
            return response()->file($url);
        }
    }

    public function get_carousel_image($image)
    {
        $url = storage_path("app/public/img/carousel/" . $image);
        if (File::exists($url)) {
            return response()->file($url);
        }
    }
    

    public function get_products(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $products = Product::with(['inFavorite' => function ($sql) use ($user) {
                $sql->where('user_id', $user->id);
            }]);

            if ($request->get('limit')) {
                $products->limit($request->get('limit'));
            } else if ($request->get('search')) {
                $products->where("name", 'like', "%{$request->get('search')}%");
            }
            $products = $products
                ->orderBy($request->get('order_name'), $request->get('method'))
                ->paginate();

            return response()->json([
                'success' => true,
                "products" => $products
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 200);
        }
    }


    public function get_products_by_category(Request $request, $category_id)
    {
        $products = Product::where('category_id', $category_id)->get();

        return response()->json([
            'success' => true,
            "products" => $products
        ]);
    }
    public function get_categories(Request $request)
    {
        $categories = null;
        if ($request->get('limit')) {
            $categories = Category::limit($request->get('limit'))->get();
        } else {
            $categories = Category::get();
        }

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function getFavoriteProducts(Request $request)
    {
        try {
            $favorite_products = WishList::where('user_id', $request['user_id'])->with('product')->get();

            return response()->json([
                "success" => true,
                "favorite_products" => $favorite_products
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function toggleFavoriteProducte(Request $request)
    {
        $favoritList = WishList::where("user_id", $request['user_id'])
            ->where('product_id', $request->get('product_id'))
            ->pluck('id')->toArray();

        if ($favoritList && $request->get('deleting') == false) {
            WishList::whereIn('id', $favoritList)->delete();
            return response()->json([
                "success" => true,
                "message" => $favoritList
            ]);
        } else {
            if (!$favoritList) {
                WishList::create([
                    "user_id" => $request['user_id'],
                    "product_id" => $request['product_id']
                ]);
            }
            return response()->json([
                "success" => true,
                "message" => "added"
            ]);
        }
    }
}
