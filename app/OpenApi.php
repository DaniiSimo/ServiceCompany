<?php

namespace App;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'My API', version: '1.0.0')]
#[OA\Server(url: '/')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum'
)]
class OpenApi
{

}
