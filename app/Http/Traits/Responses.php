<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait Responses
{
    public function SuccessResponse($response_data, $status_code=Response::HTTP_OK)
    {
        return response(['status' => 'OK' , 'data' => $response_data] , $status_code);
    }

    public function FailResponse($response_data, $status_code=Response::HTTP_BAD_REQUEST)
    {
        return response(['status' => 'ERROR' , 'data' => $response_data] , $status_code);
    }

    protected function SuccessRedirect($message, $route, $errors = [], $params=[])
    {
        session()->flash('message', $message);
        return redirect(route($route,$params))->withErrors($errors);
    }

    protected function SuccessRedirectUrl($message, $url, $errors = [], $params=[])
    {
        session()->flash('message', $message);
        return redirect($url)->withErrors($errors);
    }
}
