<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/get')->group(callback: function () {
	Route::get(uri: '/name', action: [OrganizationController::class, 'getByName']);
	Route::get(uri:'/name-activity',action: [OrganizationController::class, 'getByNameActivity']);
	Route::get(uri:'/building',action:[OrganizationController::class, 'getByBuilding']);
	Route::get(uri:'/area',action:[OrganizationController::class, 'getByArea']);
	Route::get(uri:'/activity',action:[OrganizationController::class, 'getByActivity']);
	Route::get(uri:'/{id}',action:[OrganizationController::class, 'get']);
});
