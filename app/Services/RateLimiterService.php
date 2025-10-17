<?php

namespace App\Services;

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Сервис ограничения попыток ввода
 */
final class RateLimiterService
{
    /**
     * Добавление попытки по ключу
     *
     * @param array $dataKey Данные для ключа, по которому будет искаться количество попыток
     *
     * @return void
     */
    public function add(array $dataKey): void
    {
        RateLimiter::hit(
            key: $this->generateKey(data: $dataKey),
            decaySeconds: config(key: 'auth.decay_seconds', default: 240)
        );
    }

    /**
     * Очистить все попытки по ключу
     *
     * @param array $dataKey Данные для ключа, по которому будет искаться количество попыток
     *
     * @return void
     */
    public function clear(array $dataKey):void
    {
        RateLimiter::clear(key: $this->generateKey(data: $dataKey));
    }

    /**
     * Проверка превышения количества попыток по ключу
     *
     * @param array $dataKey Данные для ключа, по которому будет искаться количество попыток
     *
     * @throws ThrottleRequestsException Количество попыток превышено, дополнительные можно сделать через n секунд
     *
     * @return void
     */
    public function ensureIsNotRateLimited(array $dataKey):void
    {
        $key = $this->generateKey(data: $dataKey);
        if (
            RateLimiter::tooManyAttempts(
                key: $key,
                maxAttempts: config(key: 'auth.count_attempts_password', default: 5)
            )
        ) {
            $availableSeconds = RateLimiter::availableIn(key: $key);
            throw new ThrottleRequestsException(
                message: __(
                    key: 'auth.throttle',
                    replace: [
                        'seconds' => $availableSeconds,
                    ]
                ),
                headers: ['availableSeconds' => $availableSeconds]
            );
        }
    }

    /**
     * Генерация ключа
     *
     * @param array $dataKey Данные для генерации ключа
     *
     * @return string Ключ
     */
    private function generateKey(array $data):string{
        return Str::lower(value:implode(separator: '|', array: $data));
    }

}
