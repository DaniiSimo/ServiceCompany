<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Building',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'address', type: 'string', example: 'ПАО ГаражХозТелеком'),
        new OA\Property(property: 'lon', type: 'number', format: 'double', minimum: -180, maximum: 180, example: 130.158708),
        new OA\Property(property: 'lat', type: 'number', format: 'double', minimum:  -90, maximum:  90, example:  34.104894),
    ]
)]
class BuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'lon' => $this->lon,
            'lat' => $this->lat
        ];
    }
}
