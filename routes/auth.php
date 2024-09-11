<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], function () {
    // Login/Register and Verify
    Route::post('login', [ApiAuthController::class, 'login'])->name('api_login');
    Route::post('verify', [ApiAuthController::class, 'verify'])->name('api_verify');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('logout');
});
