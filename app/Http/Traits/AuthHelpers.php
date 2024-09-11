<?php

namespace App\Http\Traits;

use App\Models\ActivationCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

trait AuthHelpers
{
    use Helpers;

    private function verify_code($code, $user)
    {
        $code_obj = ActivationCode::where('code', $code)->where('user_id', $user->id)->first();
        if ($code_obj && !$code_obj->is_used) {
            return true;
        }

        $phoneNumber = $user->phone;

        // افزایش تعداد تلاش‌های اشتباه
        $attempts = Cache::get('attempts_' . $phoneNumber, 0) + 1;
        Cache::put('attempts_' . $phoneNumber, $attempts, now()->addMinutes(60));

        // بررسی تعداد تلاش‌های ناموفق
        if ($attempts >= 5) {
            // مسدود کردن حساب به مدت 1 ساعت
            Cache::put('blocked_' . $phoneNumber, now()->addHour()->timestamp, now()->addHour());

            $error_message = 'شما بیش از ۵ بار کد اشتباه وارد کرده‌اید. حساب شما به مدت ۱ ساعت مسدود شد.';
            $error_code = 403;
        } else {
            $error_message = 'کد نادرست است. شما ' . (5 - $attempts) . ' تلاش دیگر دارید.';
            $error_code = 400;
        }

        $this->throw_json_exception($error_message, $error_code);
    }

    public function SendCode($user, $do_check_code_sent = true)
    {
        $code = $this->CreateNewCode($user, $do_check_code_sent);

        #TODO sms sending code should be implemented here

        return $code->code;
    }

    private function check_code_sent($user)
    {
        $code = $user->activation_codes()->latest()->first();
        if ($code) {
            $diff_minutes = Carbon::parse($code->created_at)->diffInMinutes(Carbon::now());
            if ($diff_minutes < 1) {
                return false;
            }
        }
        return true;
    }

    private function change_is_used($user)
    {
        $user->activation_codes()->where('is_used', false)->update(['is_used' => true]);
    }

    private function CreateNewCode($user, $do_check_code_sent = true)
    {
        if ($do_check_code_sent && !$this->check_code_sent($user)) {

            try {
                throw ValidationException::withMessages(['code' => 'کد یکبار مصرف تایید حساب کاربری برای شما ارسال شده است ، پس از گذشت 1 دقیقه میتوانید دوباره درخواست کنید!'])->status(400);
            } catch (ValidationException $e) {
                $response = response()->json([
                    'status' => 'ERROR',
                    'data' => 'کد یکبار مصرف تایید حساب کاربری برای شما ارسال شده است ، پس از گذشت 1 دقیقه میتوانید دوباره درخواست کنید!'
                ], 400);

                throw new HttpResponseException($response);
            }

        }

        $user->activation_codes()->update(['is_used' => true]);

        $code = ActivationCode::create([
            'user_id' => $user->id
        ]);
        return $code;
    }

    public function SendVerifyCode($user)
    {
        if ($user && !$user->email_verified_at) {
            $this->SendCode($user);
            session()->flash('message', 'یک پیامک حاوی کد احراز هویت به شماره تلفن شما ارسال شده است.');
            return true;
        }
        return false;
    }
}
