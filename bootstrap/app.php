<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Resources\ErrorResource;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(middleware: ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(
            using: function (ThrottleRequestsException $e, $request) {
                event(new Lockout(request: $request));
                $response = ErrorResource::make((object)[
                    'description' => 'Превышен лимит попыток',
                    'errors' => [$e->getMessage()]
                ])->response(request: $request)->setStatusCode(code: 429);

                $headersException = $e->getHeaders();

                return isset($headersException['availableSeconds'])
                    ? $response->header(key: 'Retry-After', values: $headersException['availableSeconds'])
                    : $response;
            }
        );
        $exceptions->render(using: fn (AuthenticationException $e, $request) =>
            ErrorResource::make((object)[
                'description' => 'Ошибка аутентификации',
                'errors' => [
                    $e->getMessage() === 'Unauthenticated.'
                        ? 'Доступ запрещён, необходим токен авторизации'
                        : $e->getMessage()
                ]
            ])
            ->response(request: $request)
            ->setStatusCode(code: 401)
        );
    })->create();
