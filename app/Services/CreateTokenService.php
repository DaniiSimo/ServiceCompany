<?php

namespace App\Services;

use App\Models\User;

/**
 * Сервис генерации Bearer токенов
 */
final class CreateTokenService
{
    /**
     * Генерация токена
     *
     * @param User $user  Объект пользователя
     *
     * @return string Bearer токен
     */
    public function generate(User $user):string
    {
        $user->tokens()->delete();
        return $user->createToken(name:'api-token')->plainTextToken;
    }
}
