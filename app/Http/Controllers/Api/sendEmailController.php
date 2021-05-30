<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\emailValidateRequest;
use App\Models\User;
use App\Mail\SendMail;
use App\Models\Email_verify;
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
            return $this->resendEmail($request);
        } catch (\Exception $e) {
            return $this->severerror($e->getMessage());
        }
    }

    public function resendEmail(emailValidateRequest $request)
    {
        $phonenumber = $request->phone;
        $email = $request->email;

        try {
            //check if phone exist
            $isPhoneExit = $this->duplicatePhone($phonenumber);
            if (!$isPhoneExit) {
                return $this->notfound("No record found");
            }

            //delete existing mail token
            $ivExists = Email_verify::wherePhone($phonenumber)->first();
            if ($ivExists) {
                $ivExists->delete();
            }
            $isPhoneExist = User::wherePhone($phonenumber)->wherePhone_verification('verify')->first();
            if (!$isPhoneExist) {
                return $this->badrequest('The phone number is not verify');
            }
            //check for phone verification
            $isPhoneVerify = User::wherePhone($phonenumber)->whereEmail_verify('not_verify')->first();
            if (!$isPhoneVerify) {
                return $this->badrequest('the provided mail is already verified');
            }
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
            //save token detail to Email_verify model
            Email_verify::create(['phone' => $phonenumber, 'email' => $email, 'token' => $emailToken]);
            return $this->success("Email added successfully. Activation link was send to the registered mail");
        } catch (\Exception $e) {
            return $this->severerror($e->getMessage());
        }
    }

    private function duplicatePhone($phonenumber)
    {
        return User::wherePhone($phonenumber)->count();
    }

    private function generateEmailLink()
    {
        $verification_string_one = Str::uuid();
        $verification_string_two = Str::uuid();
        return str_shuffle($verification_string_one . '-' . $verification_string_two);
    }
}
