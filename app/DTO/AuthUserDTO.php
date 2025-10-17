<?php

namespace App\DTO;

final readonly class AuthUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $ip
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email:  $data['email'],
            password:  $data['password'],
            ip:  $data['ip']
        );
    }
}
