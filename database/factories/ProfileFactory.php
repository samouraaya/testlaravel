<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = \App\Models\Profile::class;

 /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'last_name' => $this->faker->name,
            'administrator_id' => \App\Models\Admin::factory(),
            'first_name' => $this->faker->firstName,
            'image' => 'images/default.png',
            'status' => 'active',
        ];
    }
}
