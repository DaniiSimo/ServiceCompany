<?php

namespace App\Services;

use App\Models\Organization;
use App\DTO\SearchOrganizationDTO;
use Illuminate\Support\Collection;

/**
 * Сервис отвечающий за поиск информации по организациям
 */
final class SearchOrganizationService
{
    /**
     * Производит поиск организаций
     *
     * @param SearchOrganizationDTO $dto  Данные о поиске
     *
     * @return Collection
     */
    public function search(SearchOrganizationDTO $dto):Collection
    {
        return Organization::query()
            ->named($dto->nameOrganization)
            ->withinRadius($dto->lat,$dto->lon,$dto->radius)
            ->withinPolygon($dto->polygon)
            ->address($dto->addressBuilding)
            ->namedActivity($dto->nameActivity, $dto->shouldTakeDescendants)
            ->with(['building','phones','activities'])
            ->get();
    }
}
