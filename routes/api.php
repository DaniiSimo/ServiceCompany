<?php

use App\Http\Middleware\WhiteListMiddleware;
use App\Models\IpAddressWhiteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
