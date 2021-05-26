<?php

use App\Http\Controllers\Api\phoneVerificationController;
use App\Http\Controllers\Api\sendEmailController;
use App\Http\Controllers\Api\verifyTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/tests', function (Request $request) {
    return 'pass test';
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/initial', [phoneVerificationController::class, 'initial']);
    Route::post('/resendToken', [phoneVerificationController::class, 'resendToken']);
    Route::post('/verifyToken', [verifyTokenController::class, 'verifyToken']);
    Route::Post('/resendEmail', [sendEmailController::class, 'resendEmail']);
    Route::Post('/sendEmail', [sendEmailController::class, 'sendEmail']);


});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
