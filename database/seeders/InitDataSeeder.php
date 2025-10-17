<?php

namespace Database\Seeders;

use App\Services\InitDataSeederService;
use Illuminate\Database\Seeder;

class InitDataSeeder extends Seeder
{
    public function __construct(private InitDataSeederService $service){}
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countOrganizations = 5;
        $countPhones = 2;
        $countActivities = [
            1=> 2,
            2=> 1,
            3 => 2
        ];
        $this->service->createOrganization(
            countOrganizations: $countOrganizations,
            countPhonesFromOrganizations: $countPhones,
            countActivities: $countActivities
        );

    }
}
