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
    /** @var list<string> */
    private const PRODUCT_BASE_NAMES = [
        'Starkstromkabel',
        'Verlaengerungskabel',
        'LED-Lichterkette',
        'Getraenkekuehlschrank',
        'Klapptisch',
        'Bierzeltbank',
        'Warmhaltebehaelter',
        'Servierwagen',
    ];

    /** @var list<string> */
    private const LENGTH_OPTIONS = [
        '1,5 m',
        '5 m',
        '10 m',
        '20 m',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseName = $this->faker->randomElement(self::PRODUCT_BASE_NAMES);
        $name = $baseName.' '.$this->faker->unique()->numberBetween(100, 999);
        $hasLengthFeature = (bool) $this->faker->numberBetween(0, 1);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Vermietartikel fuer Veranstaltungen in der Gastronomie und im Eventbereich.',
            'keywords' => ['gastro', 'vermietung', 'event', 'technik', 'service'],
            'image_path' => 'products/'.$this->faker->word().'.jpg',
            'price' => $this->faker->randomFloat(2, 5, 500),
            'feature_name' => $hasLengthFeature ? 'Laenge' : null,
            'feature_values' => $hasLengthFeature ? self::LENGTH_OPTIONS : null,
        ];
    }
}
