<?php

namespace App\DTO;

final readonly class SearchOrganizationDTO
{
    public function __construct(
        public ?string $nameActivity = null,
        public bool   $shouldTakeDescendants = false,
        public ?string $addressBuilding = null,
        public ?string $nameOrganization = null,
        public ?array  $polygon = null,
        public ?float  $lat = null,
        public ?float  $lon = null,
        public ?float  $radius = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nameActivity:     $data['name_activity']     ?? null,
            shouldTakeDescendants:  $data['should_take_descendants'] ?? false,
            addressBuilding:  $data['addressBuilding']  ?? null,
            nameOrganization: $data['nameOrganization'] ?? null,
            polygon:          $data['polygon']          ?? null,
            lat:              isset($data['lat'])    ? (float)$data['lat']    : null,
            lon:              isset($data['lon'])    ? (float)$data['lon']    : null,
            radius:           isset($data['radius']) ? (float)$data['radius'] : null,
        );
    }
}
