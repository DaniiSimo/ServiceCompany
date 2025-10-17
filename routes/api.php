<?php

use App\Http\Controllers\{AuthController, OrganizationController, RegisterController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ErrorResource;

Route::prefix('users')->group(callback: function () {
	Route::post(uri: '/login', action: [AuthController::class, 'store']);
	Route::post(uri: '/registration', action:  [RegisterController::class, 'store']);
});

Route::middleware(['auth:sanctum'])->prefix('organizations')->group(callback: function () {
    Route::get(uri: '/{organization}', action: [OrganizationController::class, 'show'])
        ->missing(
            missing: fn(Request $request) =>
            ErrorResource::make((object)[
                'description' => 'Organization not found',
                'errors' => []
            ])->response(request: $request)->setStatusCode(code: 404)
        );
    Route::get(uri: '/', action: [OrganizationController::class, 'index']);
});
