<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Activity',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'quo dolor', maxLength: 255),
        new OA\Property(
            property: 'path',
            type: 'string',
            example: 'quo_dolor',
            description: 'Путь по которому происходит поиск активности строится через trim(string: Str::slug(title: Str::ascii(value: $value),separator: \'_\'))'
        ),
        new OA\Property(
            property: 'level',
            type: 'smallint',
            example: '1',
            minimum: 1,
            maximum: 3,
            readOnly: true,
            description: 'Поле выражения, которое описывает глубину деятельности'
        ),
    ]
)]
class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'path' => $this->path
        ];
    }
}
