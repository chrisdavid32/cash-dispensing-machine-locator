<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\emailValidateRequest;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class sendEmailController extends Controller
{
    public function sendEmail(emailValidateRequest $request)
    {
        $phonenumber = $request->phone;
        $email = $request->email;

        try {
            //check if mail exist
            $isEmailExist = $this->duplicateEmail($email);
            if ($isEmailExist) {
                return $this->badrequest("The input email already taken");
            }
            //query if phone number is verify
            $userEmail = User::wherePhone($phonenumber)->wherePhone_verification('verify')->first();

            //save and update email field
            if ($userEmail) {
                $userEmail->update([
                    'email' => $email
                ]);
            }
            return $this->success("email save successfully");
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function resendEmail(emailValidateRequest $request)
    {
        $phonenumber = $request->phone;
        $email = $request->email;

        try {
            //check if email exist
            $isDuplicateEmail = $this->duplicateEmail($email);
            if (!$isDuplicateEmail) {
                return $this->notfound("No record found");
            }

            //check for phone verification
            $isPhoneVerify = User::wherePhone($phonenumber)->wherePhone_verification('verify')->first();
            $title = "wellcome,";
            $userinfo = [
                'phone' => $phonenumber,
                'email' => $email,
            ];
            // return $this->success()
            $emailToken = $this->generateEmailLink();
            $activationLink = Config::get('app.fe_url') . '' . $emailToken;
            $sendmail = Mail::to($userinfo['email'])->send(new SendMail($title, $userinfo, $activationLink));
            if (empty($sendmail)) {
                $isPhoneVerify->update([
                    'email' => $email,
                ]);
            }
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    private function duplicateEmail($email)
    {
        return User::whereEmail($email)->count();
    }

    private function generateEmailLink()
    {
        $verification_string_one = Str::uuid();
        $verification_string_two = Str::uuid();
        return str_shuffle($verification_string_one . '-' . $verification_string_two);
    }
}
