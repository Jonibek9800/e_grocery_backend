<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\CheckDetails;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function check($user_id, $total_quantity, $total_cost, $order_status)
    {
        $chek = Check::create([
            "user_id" => $user_id,
            "total_quantity" =>  $total_quantity,
            "total_cost" => $total_cost,
            "order_status" => $order_status
        ]);

        return $chek;
    }

    public function check_details(Request $request)
    {
        try {
            $check = $this->check(
                $request['user_id'],
                $request['total_quantity'],
                $request['total_cost'],
                $request['order_status'],
            );
    
            $array_of_col = $request['products'];
    
            foreach ($array_of_col as $product) {
                CheckDetails::create([
                    "product_id" => $product['product_id'],
                    "poster_path" => $product['poster_path'],
                    "product_name" => $product['name'],
                    "price" => $product['price'],
                    "check_id" =>  $check->id,
                    "total_cost" => $product['total_cost'],
                    "quantity" => $product['quantity']
                ]);
            }
                
    
            return response()->json([
                "success" => true,
                "message" => "product Success created"
                
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function get_checks(Request $request) 
    {
        $checks = Check::where('user_id', $request['user_id'])
        ->with(['details' => function($element) {
            $element->with('product');
        
        }])
        ->get();

        if($checks == null) return response()->json(["success" => false, 'message' => "checks is not found"]);

        return response()->json([
            'success' => true,
            "checks" => $checks
        ]);
    }
}
