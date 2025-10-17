<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Organization',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'ПАО ГаражХозТелеком', maxLength: 255),
        new OA\Property(property: 'building', ref: '#/components/schemas/Building'),
        new OA\Property(property: 'phones', type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Phone'
            )
        ),
        new OA\Property(property: 'activites', type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Activity'
            )
        )
    ]
)]
class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'building' => BuildingResource::make($this->whenLoaded(relationship: 'building')),
            'phones' => PhoneResource::collection($this->whenLoaded(relationship: 'phones')),
            'activities' => ActivityResource::collection($this->whenLoaded(relationship: 'activities')),
        ];
    }
}
