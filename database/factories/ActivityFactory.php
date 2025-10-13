<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $name = fake()->unique()->words(nb: 2,asText:  true);

        return [
            'name' => $name,
            'path' => trim(string:
                Str::slug(title:
                    Str::ascii(value: $name),separator: '_'
                ))
            ,
            'created_at'=>now(),
            'updated_at'=>now()
        ];
    }
    public function childOf(Activity $parent): self
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'path' => $parent->path.'.'.trim(string: Str::slug(title: Str::ascii(value: $attributes['name']),separator: '_')),
            ];
        });
    }
}
