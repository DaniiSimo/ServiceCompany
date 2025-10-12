<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер Регистрации
 */
class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $validated = $request->safe()->only(keys: ['name','email','password']);

        return response()->json(data:
            User::create(attributes:[
                'name'=> $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make(value: $validated['password'])
            ])
        );
    }
}
