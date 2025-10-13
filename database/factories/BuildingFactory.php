<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lat = fake()->latitude();
        $lon = fake()->longitude();

        return [
            'address' => fake()->address(),
            'geom'    => DB::raw(value: "ST_SetSRID(ST_MakePoint($lon, $lat), 4326)"),
            'created_at'=>now(),
            'updated_at'=>now()
        ];
    }
}
