<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\tokenValidationRequest;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;


class verifyTokenController extends Controller
{
    public function verifyToken(tokenValidationRequest $request)
    {
        //get token from request
        $token = $request->token;
        try {
            $token_detail= $this->validateToken($token);
            if(!$token_detail){
                return $this->notfound("Invalid token Detail");
            }
            //if yes. updated user detail
            $user_details= User::wherePhone($token_detail->phone)->first();
            //return $user_details;
            if ($user_details) {
                $user_details->update([
                    'phone_verification' => 'verify'
                ]);
            }

                //delete the used token
                $token_detail->delete(); 

                //return success response
                return $this->success("phone number verified successfully");
            

        } catch (\Exception $e) {
            return $this->severerror($e->getMessage());  
        }
    }

    private function validateToken($token)
    {
        return Verification::whereCode($token)->first();
    }


}
