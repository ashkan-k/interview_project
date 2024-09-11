<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiVerifyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'min:11',
                'max:11',
                'regex:/(^\+?(09|98|0)?(9([0-9]{9}))$)/',
                'exists:users,phone',
            ],
            'code' => 'required|digits:6'
        ];
    }
}
