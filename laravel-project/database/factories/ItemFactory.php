<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Condition;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'condition_id' => Condition::factory()->create()->id,
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(100, 20000),
            'img_url' => $this->faker->imageUrl(640, 480, 'items', true),
            'description' => $this->faker->sentence,
        ];
    }
}
