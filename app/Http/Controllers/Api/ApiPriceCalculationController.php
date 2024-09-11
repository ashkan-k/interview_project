<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiPriceCalculationRequest;
use App\Http\Traits\Helpers;
use App\Http\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use function Webmozart\Assert\Tests\StaticAnalysis\float;

class ApiPriceCalculationController extends Controller
{
    use Responses, Helpers;

    /**
     * @OA\Post(
     ** path="/api/calculate-distance",
     *  tags={"PriceCalculation Api"},
     *  description="use for calculating orogin/destination distance amount",
     * @OA\RequestBody(
     *    required=true,
     * *         @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *           @OA\Property(
     *                  property="origin_lat",
     *                  description="Enter origin_lat number",
     *                  type="integer",
     *               ),
     *           @OA\Property(
     *                  property="origin_lang",
     *                  description="Enter origin_lang number",
     *                  type="integer",
     *               ),
     *           @OA\Property(
     *                  property="destination_lat",
     *                  description="Enter destination_lat number",
     *                  type="integer",
     *               ),
     *           @OA\Property(
     *                  property="destination_lang",
     *                  description="Enter destination_lang number",
     *                  type="integer",
     *               ),
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
    public function calculate_distance(ApiPriceCalculationRequest $request)
    {
        $web_service_access_token = 'SLDWoaFrBA8wABeAuJURloz5F9EOnedf41ZcDNPNiGff44GBeh0FygK0Mqs3y39Y';
        $web_service_url = sprintf('https://api.distancematrix.ai/maps/api/distancematrix/json?origins=%s,%s&destinations=%s,%s&key=%s', $request->origin_lat, $request->origin_lang, $request->destination_lat, $request->destination_lang, $web_service_access_token);

        $response = Http::get($web_service_url);

        if ($response->status() != 200) {
            return $this->FailResponse($response->json());
        }

        $response_json = $response->json();

        // Calculate Distances Expenses
        $distance_in_km = $response_json['rows'][0]['elements'][0]['distance']['text'];

        $distance_km_number = floatval(trim(str_replace("km", "", $distance_in_km)));

        // Check Distance Min Limitation
        $this->check_distance_min_limit($distance_km_number);

        $travel_expenses = ($distance_km_number / 10) * 2;

        $response = [
            'locations_information' => $response_json,
            'travel_expenses' => $travel_expenses,
        ];
        return $this->SuccessResponse($response);
    }
}
