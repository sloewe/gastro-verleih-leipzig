<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /** @var list<string> */
    private const CATEGORY_NAMES = [
        'Kuehltechnik',
        'Buffet und Ausgabe',
        'Mobiliar',
        'Eventtechnik',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(self::CATEGORY_NAMES);
        $slug = Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999);

        return [
            'name' => $name,
            'slug' => $slug,
            'image_path' => 'categories/'.$slug.'.jpg',
        ];
    }
}
