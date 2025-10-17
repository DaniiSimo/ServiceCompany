<?php

namespace App\Services;

use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Auth\AuthenticationException;
use App\DTO\{AuthUserDTO,RegisterUserDTO};
use App\Models\User;

/**
 * Сервис авторизации
 */
final class AuthService
{
    public function __construct(
        private RateLimiterService $rateLimiterService,
        private CreateTokenService $createTokenService
    ){}

    /**
     * Регистрация пользователя
     *
     * @param RegisterUserDTO $dto  Данные для регистрации
     *
     * @return User Объект пользователя
     */
    public function registration(RegisterUserDTO $dto):User
    {
        return User::create(attributes:[
            'name'=> $dto->name,
            'email' => $dto->email,
            'password' => Hash::make(value: $dto->password),
        ]);
    }
    /**
     * Авторизаци пользователя
     *
     * @param AuthUserDTO $dto  Данные для авторизации
     *
     * @throws AuthenticationException Неверные данные авторизации
     *
     * @return string Bearer токен
     */
    public function authenticate(AuthUserDTO $dto):string
    {
        $this->rateLimiterService->ensureIsNotRateLimited(dataKey: [$dto->email,$dto->ip]);

        $user = User::where(column: 'email', operator: '=', value: $dto->email)->first();

        if (
            is_null(value: $user)
            || !Hash::check(value: $dto->password, hashedValue: $user?->password)
        ) {
            $this->rateLimiterService->add(dataKey: [$dto->email,$dto->ip]);
            throw new AuthenticationException(message:  __(key: 'auth.failed'));
        }

        $this->rateLimiterService->clear(dataKey: [$dto->email,$dto->ip]);

        Auth::login(user: $user);

        return $this->createTokenService->generate(user: $user);
    }
}
