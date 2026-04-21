<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'keywords' => $this->faker->words(5),
            'image_path' => 'products/'.$this->faker->word().'.jpg',
            'price' => $this->faker->randomFloat(2, 5, 500),
            'feature_name' => 'Größe',
            'feature_values' => ['Klein', 'Mittel', 'Groß'],
        ];
    }
}
