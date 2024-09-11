<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ApiPriceCalculationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'origin_lat' => 'required|string',
            'origin_lang' => 'required|string',
            'destination_lat' => 'required|string',
            'destination_lang' => 'required|string',
        ];
    }
}
