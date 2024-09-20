<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model=Comment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph,
            'administrator_id'=>Admin::factory(),
            'profile_id'=> Profile::factory(),
            //
        ];
    }
}
