<?php

namespace App;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'Organization API', version: '1.0.0', contact: new OA\Contact(name: 'Данила', url: 'https://github.com/DaniiSimo'))]
#[OA\Server(url: 'http/localhost/api')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
	description: 'Вводите токен без "Bearer " — UI подставит префикс сам'
)]
class OpenApi
{

}
