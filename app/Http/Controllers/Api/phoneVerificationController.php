<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\phoneValidationRequest;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;

class phoneVerificationController extends Controller
{
    public function initial(phoneValidationRequest $request)
    {
        $phoneNumber= $request->phone;
        try {
            //check for phone number duplicate
            $isduplicatePhone= $this->duplicatePhoneNumber($phoneNumber);
            if($isduplicatePhone){
                return $this->badrequest("Phone Number already exist");
            }
            //save phone number if not exist
            User::create(['phone'=>$phoneNumber]);
            return $this->resendToken($request);

        } catch (\Exception $e) {
            return $this->severerror($e->getMessage()); 
        }

    }

    //resend token function
    public function resendToken(phoneValidationRequest $request)
    {
        
        try {
            $phoneNumber = $request->phone;
            //check if phone  number exist in database
            $isDuplicate = $this->duplicatePhoneNumber($phoneNumber);
            if (!$isDuplicate) {
                return  $this->notfound("No record found");
            }
            // check if phone number is already verified
            $isVerifyPhone = User::wherePhone($phoneNumber)->wherePhone_verification('verify')->first();
            if($isVerifyPhone){
                return $this->badrequest("Phone number is already verified."); 
            }

            //delete the old token
            $ivExists = Verification::wherePhone($phoneNumber)->first();
            if ($ivExists) {
                $ivExists->delete();
            }

            //create a new token
            $token = $this->generateToken();
            
            //save the token 
            Verification::create(['phone' => $phoneNumber, 'code' => $token]);

            //send SMS
            $this->sendToken($phoneNumber, $token);

            //return succeess message
            return $this->created([
                "verification_token" => $token
            ]);
            

        } catch (\Exception $e) {
            $this->severerror($e->getMessage());
        }
    }

    private function generateToken()
    {
        return sprintf("%0.5s", str_shuffle(rand(20, 90000) * time()));
    }

    private function sendToken($phone, $token)
    {
        //send sms
        return true;
    }

    private function duplicatePhoneNumber($phone)
    {
        return User::wherePhone($phone)->count();
    }
}
