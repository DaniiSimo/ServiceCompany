<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Phone',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(
            property: 'phone',
            type: 'string',
            description: 'Формат: X-XXX-XXX(-XX-XX). Уникален внутри организации.',
            pattern: '^[1-9]-[0-9]{3}-[0-9]{3}(-[0-9]{2}-[0-9]{2})?$',
            example: '8-923-666-13-13',
            minLength: 9,
            maxLength: 15
        ),
    ]
)]
class PhoneResource extends JsonResource
{
    public function toArray(Request $request):array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone
        ];
    }
}
