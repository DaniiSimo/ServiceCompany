<?php

namespace App\Services;

use App\Models\{Activity,Organization};
use Illuminate\Support\Collection;

/**
 * Сервис заполняющий сгенерированными данными для Организации, Деятельности, Номера телефона и Здания
 */
final class InitDataSeederService
{
    /**
     * Создаёт N организаций, каждой добавляет M телефонов и здание
     * генерирует дерево видов деятельности по уровням и
     * привязывает к каждой организации случайное подмножество этих деятельностей.
     *
     *
     * @param int $countOrganizations               Количество, создаваемых организаций.
     * @param int $countPhonesFromOrganizations     Количество телефонов, создаваемых для каждой организации.
     * @param array<int,int> $countActivities       Кол-во активностей на каждом уровне
     *                                              (ключ = уровень от 1, значение = кол-во узлов на уровне),
     *                                              например: [1 => 3, 2 => 2, 3 => 4].
     *
     * @return void
     */
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
            $ids  = $activitiesIds->random(number: $take)->all();
            $organization->activities()->attach(ids: $ids);
        });

    }

    /**
     * Рекурсивно генерирует дерево активностей (materialized path) по уровням
     * и возвращает коллекцию всех созданных узлов (включая корни и потомков).
     *
     *
     * @param Collection<int,Activity> $all Коллекция всех созданных узлов
     * @param array<int,int> $countsActivity Кол-во узлов на каждом уровне
     * @param int $depth Максимальная глубина дерева, начиная с 1.
     * @param Collection<int,Activity>|null $prevLayer Коллекция узлов предыдущего уровня.
     * @param int $currentDepth Текущий уровень (по умолчанию 1).
     *
     * @return Collection<int,Activity> Коллекция всех созданных Activity (упорядочение по уровням не гарантируется).
     */
    private function recursiveCreateActivities(Collection $all, array $countsActivity, int $depth,
                                               Collection $prevLayer = null, int $currentDepth = 1):Collection
    {
        if($currentDepth > $depth)
            return $all;

        $currentLayer = $currentDepth === 1
            ? Activity::factory()
                ->count(count: $countsActivity[$currentDepth] ?? 1)
                ->create()
            : $prevLayer->flatMap(callback: fn($p) =>
                Activity::factory()
                    ->count(count: $countsActivity[$currentDepth] ?? 1)
                    ->childOf(parent: $p)
                    ->create()
            );

        return $this->recursiveCreateActivities(
            all: $all->concat(source: $currentLayer),
            countsActivity: $countsActivity,
            depth: $depth,
            prevLayer: $currentLayer,
            currentDepth: ++$currentDepth
        );
    }
}
