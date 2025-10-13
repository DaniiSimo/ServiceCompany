<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use OpenApi\Attributes as OA;
/**
 * Контроллер авторизации
 */
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/user/login',
        tags: ['User'],
        summary: 'Авторизация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, description: 'OK',
                content: new OA\JsonContent(
                    type: 'string',
                    example: '1|fdsagfdasfdasfdsafdasf'
                )
            ),
            new OA\Response(
                response: 422, description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 422),
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["email" => "Ошибка валидации",  "password" => "Ошибка валидации"]),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 429, description: 'Превышен лимит попыток',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 429),
                        new OA\Property(property: 'description', type: 'string', example: 'Превышен лимит попыток'),
                        new OA\Property(property: 'errors', type: 'object', example: ["Слишком много попыток входа. Пожалуйста, попробуйте снова через n секунд."]),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(AuthRequest $request)
    {
        $request->authenticate();

        $user = auth()->user();
        $user->tokens()->delete();
        $token = $user->createToken(name:'api-token')->plainTextToken;

        return response()->json(data: $token);
    }
}
