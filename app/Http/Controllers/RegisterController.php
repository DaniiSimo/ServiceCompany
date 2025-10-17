<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use OpenApi\Attributes as OA;

/**
 * Контроллер Регистрации
 */
class RegisterController extends Controller
{
    public function __construct(private AuthService $authService){}
    #[OA\Post(
        path: '/api/users/registration',
        tags: ['User'],
        summary: 'Регистрация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password', 'name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'test'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secretpassword'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/User')
                        ),
                    ],
                    type: 'object'
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
    public function store(RegisterUserRequest $request): JsonResponse
    {
        $dto = $request->dto();
        $result = $this->authService->registration(dto: $dto);

        return $result
            ->toResource()
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
}
