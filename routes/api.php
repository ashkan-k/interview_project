<?php

use App\Http\Controllers\Api\ApiPriceCalculationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::group(['middleware' => ['auth:api', 'throttle:10,1']], function () { استفاده از روش throttle برای مدیریت محدودیت درخواست
Route::group(['middleware' => ['auth:api', 'custom_rate_limit']], function () { //  استفاده از روش middleware شخصی برای مدیریت محدودیت درخواست
    Route::post('calculate-distance', [ApiPriceCalculationController::class, 'calculate_distance'])->middleware();
});

require __DIR__.'/auth.php';
