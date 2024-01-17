<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserValidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{
    /**
     * 
     *  @param Request $request;
     * @return User
     * 
     */

    public function createUser(UserValidate $request)
    {
        try {

            $user = User::create([
                "name" => $request["name"],
                "email" => $request["email"],
                "phone_number" => $request["phone_number"],
                "password" => Hash::make($request["password"]),
            ]);

            return response()->json([
                "status" => true,
                "message" => "User created success",
                "token" => $user->createToken("user_token")->plainTextToken,
                "user" => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }


    public function loginUser(UserValidate $request)
    {
        try {
            $user = User::where("email", $request['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => "Email does not match with our record."
                ]);
            }

            if (!Hash::check($request["password"], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => "Password does not match with our record."
                ]);
            }


            return response()->json([
                'status' => true,
                'message' => 'User logged In Successfull',
                'token' => $user->createToken('request_token')->plainTextToken,
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function logoutUser(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            "message" => "Logged out",
        ]);
    }

    public function update_user(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);
            if ($request->file('poster')) {
                $poster = $request->file('poster');

                $poster_format = $poster->getClientOriginalExtension();
                $poster_name = "image_" . Str::random(30) . "." . $poster_format;
                $save_poster = Image::make($poster);
                $save_poster->resize(300, 300, function ($constrains) {
                    $constrains->aspectRatio();
                });
                if ($user->poster_path) {
                    if (File::exists(storage_path('app/public/img/persons/' . $user->poster_path))) {
                        File::delete(storage_path('app/public/img/persons/' . $user->poster_path));
                    }
                }
                $save_poster->save(storage_path('app/public/img/persons/' . $poster_name));

                if($request->get('password') == null) {
                    $user->update([
                        "poster_path" => $poster_name,
                        "name" => $request->get('name'),
                        'email' => $request->get('email'),
                        "phone_number" => $request->get('phone_number'),
                        "password" => $user->password,
                        "role_of_user_id" => $request->get('role_id')
                    ]);
                } else {
                    $user->update([
                        "poster_path" => $poster_name,
                        "name" => $request->get('name'),
                        'email' => $request->get('email'),
                        "phone_number" => $request->get('phone_number'),
                        "password" => $request->get('password'),
                        "role_of_user_id" => $request->get('role_id')
                    ]);
                }
                
                // return "-1";
                return response()->json([
                    "status" => true,
                    "user" => $user
                ]);
            } else {
                if($request->get('password') == null) {
                    $user->update([
                        "poster_path" => $user->poster_path,
                        "name" => $request->get('name'),
                        'email' => $request->get('email'),
                        "phone_number" => $request->get('phone_number'),
                        "password" => $user->password,
                        "role_of_user_id" => $request->get('role_id')
                    ]);
                } else {
                    $user->update([
                        "poster_path" => $user->poster_path,
                        "name" => $request->get('name'),
                        'email' => $request->get('email'),
                        "phone_number" => $request->get('phone_number'),
                        "password" => $request->get('password'),
                        "role_of_user_id" => $request->get('role_id')
                    ]);
                }
                // return "1";
                return response()->json([
                    "status" => true,
                    "user" => $user
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }

    public function getToken(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response(['status' => false, "message" => "User Undefined"]);
        }
        return response(['status' => true, 'user' => $user]);
    }
}
