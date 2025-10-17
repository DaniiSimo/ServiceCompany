<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserRequest;
use App\Http\Resources\BearerTokenResource;
use App\Http\Resources\ErrorResource;
use App\Services\AuthService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Контроллер авторизации
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService){}

    #[OA\Post(
        path: '/api/users/login',
        tags: ['User'],
        summary: 'Авторизация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password'],
                properties: [
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
                        new OA\Property(ref:  '#/components/schemas/BearerToken'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 401, description: 'Ошибка аутентификации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка аутентификации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["Неправильно указан логин или пароль, повторите попытку."]),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422, description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
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
                        new OA\Property(property: 'description', type: 'string', example: 'Превышен лимит попыток'),
                        new OA\Property(property: 'errors', type: 'object', example: ["Слишком много попыток входа. Пожалуйста, попробуйте снова через n секунд."]),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(AuthUserRequest $request): JsonResponse
    {
        $dto = $request->dto();
        $result = $this->authService->authenticate(dto: $dto);

        return BearerTokenResource::make((object)['token' => $result])
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
}
