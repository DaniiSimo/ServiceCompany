<?php

namespace Database\Seeders;

use App\Services\InitDataSeederService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitDataSeeder extends Seeder
{
    public function __construct(private InitDataSeederService $service){}
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countOrganizations = fake()->numberBetween(int1: 1, int2: 3);
        $countPhones = fake()->numberBetween(int1: 1, int2: 3);
        $countDepth = fake()->numberBetween(int1: 1, int2: 4);
        $countActivities = [];
        for ($i=1; $i <= $countDepth; $i++)
            $countActivities[$i] = fake()->numberBetween(int1: 1, int2: 5);

        $this->service->createOrganization(
            countOrganizations: $countOrganizations,
            countPhonesFromOrganizations: $countPhones,
            countActivities: $countActivities
        );

    }
}
