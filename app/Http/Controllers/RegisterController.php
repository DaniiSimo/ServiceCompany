<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

/**
 * Контроллер Регистрации
 */
class RegisterController extends Controller
{
    #[OA\Post(
        path: '/api/user/registration',
        tags: ['User'],
        summary: 'Регистрация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password', 'name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'test'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, description: 'OK',
                content: new OA\JsonContent(
                    type: 'object',
                    ref:  '#/components/schemas/User'
                )
            ),
            new OA\Response(
                response: 422, description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 422),
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["name" => "Ошибка валидации","email" => "Ошибка валидации",  "password" => "Ошибка валидации"]),
                    ],
                    type: 'object'
                )
            )
        ]
    )]
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
