<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
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
