<?php


namespace App\Http\Traits;


use App\Jobs\SendSmsJob;
use App\Models\NotificationMessage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

trait Helpers
{
    public function throw_json_exception($error_message, $error_code=400)
    {
        $response = response()->json([
            'status' => 'ERROR',
            'message' => $error_message,
        ], $error_code);
        throw new HttpResponseException($response);
    }

    public function check_distance_min_limit($distance_km_number)
    {
        if ($distance_km_number < 100){
            $this->throw_json_exception('فاصله بین مبدا تا مقصد باید حداقل 100 کیلومتر باشد.');
        }
    }
}
