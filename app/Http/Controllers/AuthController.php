<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;

/**
 * Контроллер авторизации
 */
class AuthController extends Controller
{
    public function store(AuthRequest $request)
    {
        $request->authenticate();

        $user = auth()->user();
        $user->tokens()->delete();
        $token = $user->createToken(name:'api-token')->plainTextToken;

        return response()->json(data: $token);
    }
}
