<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Support\Collection;

class InitDataSeederService
{
    public function createOrganization(int $countOrganizations, int $countPhonesFromOrganizations, array $countActivities)
    {
        $organizations = Organization::factory(count: $countOrganizations)
                ->hasPhones(count: $countPhonesFromOrganizations)
                ->create();

        $activitiesIds = $this->recursiveCreateActivities(
            all: collect([]),
            countsActivity:  $countActivities,
            depth: count($countActivities))
            ->pluck(value: 'id');

        $organizations->each(function (Organization $organization) use ($activitiesIds) {
            $take = fake()->numberBetween(int1: 1, int2: $activitiesIds->count());
            $ids  = $activitiesIds->random($take)->all();
            $organization->activities()->attach($ids);
        });

    }

    private function recursiveCreateActivities(Collection $all, array $countsActivity, int $depth,
                                               Collection $prevLayer = null, int $currentDepth = 1):Collection
    {
        if($currentDepth > $depth)
            return $all;

        $currentLayer = $currentDepth === 1
            ? Activity::factory()
                ->count(count: $countsActivity[$currentDepth] ?? 1)
                ->create()
            : $prevLayer->flatMap(fn($p) =>
                Activity::factory()
                    ->count(count: $countsActivity[$currentDepth] ?? 1)
                    ->childOf(parent: $p)
                    ->create()
            );

        return $this->recursiveCreateActivities(
            all: $all->concat($currentLayer),
            countsActivity: $countsActivity,
            depth: $depth,
            prevLayer: $currentLayer,
            currentDepth: ++$currentDepth
        );
    }
}
