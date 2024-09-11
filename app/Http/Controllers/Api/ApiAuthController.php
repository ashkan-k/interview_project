<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Http\Requests\Api\ApiVerifyRequest;
use App\Http\Traits\AuthHelpers;
use App\Http\Traits\Helpers;
use App\Http\Traits\Responses;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ApiAuthController extends Controller
{
    use Responses, AuthHelpers, Helpers;

    /**
     * @OA\Post(
     ** path="/api/login",
     *  tags={"Auth Api"},
     *  description="use for send verification sms to user",
     * @OA\RequestBody(
     *    required=true,
     * *         @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *           @OA\Property(
     *                  property="phone",
     *                  description="Enter phone number",
     *                  type="integer",
     *               ),
     *           example={"phone": "09396988720"}
     *     )
     *   )
     * ),
     *   @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   )
     *)
     **/
    public function login(ApiLoginRequest $request)
    {
        $user = User::firstOrCreate([
            'phone' => $request->phone,
        ]);

        $code = $this->SendCode($user);

        $response = [
            'message' => 'کد تایید حساب کاربری باموفقیت برای شما پیامک شد.',
            'user_phone' => $request->phone,
            'code' => $code,
        ];
        return $this->SuccessResponse($response);
    }

    /**
     * @OA\Post(
     ** path="/api/verify",
     *  tags={"Auth Api"},
     *  description="use for verify code which sent to user phone",
     * @OA\RequestBody(
     *    required=true,
     * *         @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *           @OA\Property(
     *                  property="phone",
     *                  description="Enter phone number",
     *                  type="integer",
     *               ),
     *           @OA\Property(
     *                  property="code",
     *                  description="Enter code number",
     *                  type="integer",
     *               ),
     *
     *           example={"phone": "09396988720", "code": 123456}
     *     )
     *   )
     * ),
     *   @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   )
     *)
     **/
    public function verify(ApiVerifyRequest $request)
    {
        $phoneNumber = $request->phone;

        // بررسی وضعیت مسدود بودن کاربر
        if (Cache::has('blocked_' . $phoneNumber)) {
            $timeRemaining = Cache::get('blocked_' . $phoneNumber) - now()->timestamp;
            return response()->json([
                'message' => 'حساب شما به دلیل وارد کردن کد اشتباه مسدود شده است. لطفاً بعد از ' . gmdate('i:s', $timeRemaining) . ' دقیقه دوباره تلاش کنید.',
            ], 403);
        }

        $user = User::wherePhone($request->phone)->firstOrFail();
        $this->verify_code($request->code, $user);
        $user->update(['phone_verified_at' => Carbon::now()]);
        $this->change_is_used($user);

        $token = auth('api')->login(User::find($user->id));

        return $this->respondWithToken($token, User::find($user->id), 'حساب کاربری شما با موفقیت تایید شد.');
    }

    /**
     * @OA\Post(
     ** path="/api/logout",
     *  tags={"Auth Api"},
     *  description="logout current loged in user",
     *  security={{ "bearerAuth":{} }},
     *   @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   )
     *)
     **/
    public function logout()
    {
        auth('api')->logout();
        return $this->SuccessResponse('با موفقیت از حساب کاربری خود خارج شدید.');
    }

    /**
     * @OA\Post(
     ** path="/api/token/refresh",
     *  tags={"Auth Api"},
     *  description="refresh current loged in user token (create new one)",
     *  security={{ "bearerAuth":{} }},
     *   @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   )
     *)
     **/
    public function refresh()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token, \auth('api')->user(), 'توکن حساب کاربری با موفقیت باز تولید شد.');
    }

    //

    protected function respondWithToken($token, $user, $message)
    {
        $response = [
            'message' => $message,
            'access_token' => $token,
            'type' => 'bearer',
            'user' => $user,
        ];
        return $this->SuccessResponse($response);
    }
}
