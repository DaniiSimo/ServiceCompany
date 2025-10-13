<?php

use App\Http\Controllers\{AuthController, OrganizationController, RegisterController};
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(callback: function () {
	Route::post(uri: '/login', action: [AuthController::class, 'store']);
	Route::post(uri: '/registration', action:  [RegisterController::class, 'store']);
});

Route::middleware(['auth:sanctum'])->prefix('/get')->group(callback: function () {
	Route::get(uri: '/name', action: [OrganizationController::class, 'getByName']);
	Route::get(uri:'/name-activity',action: [OrganizationController::class, 'getByNameActivity']);
	Route::get(uri:'/building',action:[OrganizationController::class, 'getByBuilding']);
	Route::get(uri:'/area',action:[OrganizationController::class, 'getByArea']);
	Route::get(uri:'/activity',action:[OrganizationController::class, 'getByActivity']);
	Route::get(uri:'/{id}',action:[OrganizationController::class, 'get'])->whereNumber(parameters: 'id');
});
