<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class statusController extends Controller
{
    public function success($message)
    {
        $response = [
            "message" => "success",
            "data" =>  $message
        ];
        return response()->json($response, 200);
    }

    public function created($message)
    {
        $response = [
            "message" => "created",
            "data" =>  $message
        ];
        return response()->json($response, 201);
    }

    public function badrequest($message)
    {
        $response = [
            "message" => "bad request",
            "data" => ['error' => $message]
        ];
        return response()->json($response, 400);
    }

    public function notfound($message)
    {
        $response = [
            "message" => "not found",
            "data" => ['error' => $message]
        ];
        return response()->json($response, 404);
    }

    public function severerror($message)
    {
        return response()->json(['error' => $message], 500);
    }
}
