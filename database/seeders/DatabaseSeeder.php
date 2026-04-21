<?php

namespace Database\Seeders;

use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * @var array<string, array{image: string, products: list<array{name: string, description: string, keywords: list<string>, price: float, feature_name?: string, feature_values?: list<string>}>}>
     */
    private const CATEGORY_PRODUCT_DATA = [
        'Kuehltechnik' => [
            'image' => 'categories/kuehltechnik.jpg',
            'products' => [
                [
                    'name' => 'Getraenkekuehlschrank 350 Liter',
                    'description' => 'Kuehlschrank fuer Flaschen und Dosen bei Veranstaltungen und Caterings.',
                    'keywords' => ['kuehlung', 'getraenke', 'catering', 'event'],
                    'price' => 69.90,
                ],
                [
                    'name' => 'Kuehltheke mit Glastueren',
                    'description' => 'Mobile Theke zur gekuehlten Ausgabe von Speisen und Getraenken.',
                    'keywords' => ['kuehltheke', 'ausgabe', 'buffet', 'gastro'],
                    'price' => 89.00,
                ],
                [
                    'name' => 'Starkstromkabel CEE 16A',
                    'description' => 'Robustes Starkstromkabel fuer Kuehltechnik und mobile Kuechen.',
                    'keywords' => ['starkstrom', 'cee', 'strom', 'kueche'],
                    'price' => 24.50,
                    'feature_name' => 'Laenge',
                    'feature_values' => ['1,5 m', '5 m', '10 m', '20 m'],
                ],
                [
                    'name' => 'Kuehlcontainer 5 m3',
                    'description' => 'Grosses Kuehlmodul fuer Events mit hohem Lagerbedarf.',
                    'keywords' => ['kuehlcontainer', 'lagerung', 'event', 'grossveranstaltung'],
                    'price' => 189.00,
                ],
            ],
        ],
        'Buffet und Ausgabe' => [
            'image' => 'categories/buffet-und-ausgabe.jpg',
            'products' => [
                [
                    'name' => 'Chafing Dish Edelstahl',
                    'description' => 'Warmhaltegeraet fuer Buffets, Brunch und Catering.',
                    'keywords' => ['chafing dish', 'warmhalten', 'buffet', 'catering'],
                    'price' => 12.90,
                ],
                [
                    'name' => 'Warmhaltebehaelter elektrisch',
                    'description' => 'Elektrischer Warmhaltebehaelter fuer Suppen und Saucen.',
                    'keywords' => ['warmhalten', 'suppe', 'ausgabe', 'elektrisch'],
                    'price' => 19.50,
                ],
                [
                    'name' => 'Verlaengerungskabel Schuko',
                    'description' => 'Sichere Stromversorgung fuer Ausgabe- und Buffetstationen.',
                    'keywords' => ['verlaengerung', 'strom', 'buffet', 'schuko'],
                    'price' => 9.90,
                    'feature_name' => 'Laenge',
                    'feature_values' => ['1,5 m', '5 m', '10 m'],
                ],
                [
                    'name' => 'Servierwagen 3 Ebenen',
                    'description' => 'Mobiler Wagen fuer Geschirr, Speisen und Serviceablaeufe.',
                    'keywords' => ['servierwagen', 'service', 'transport', 'gastro'],
                    'price' => 29.00,
                ],
            ],
        ],
        'Mobiliar' => [
            'image' => 'categories/mobiliar.jpg',
            'products' => [
                [
                    'name' => 'Bierzeltgarnitur Standard',
                    'description' => 'Klassische Garnitur fuer Biergaerten, Feste und Firmenfeiern.',
                    'keywords' => ['bierzelt', 'sitzplaetze', 'fest', 'moebel'],
                    'price' => 14.90,
                ],
                [
                    'name' => 'Klapptisch rechteckig',
                    'description' => 'Stabiler Tisch fuer Buffetflaechen und Sitzbereiche.',
                    'keywords' => ['klapptisch', 'buffet', 'event', 'mobiliar'],
                    'price' => 11.50,
                ],
                [
                    'name' => 'LED-Lichterkette Outdoor',
                    'description' => 'Stimmungsbeleuchtung fuer Aussenbereiche und Zelte.',
                    'keywords' => ['beleuchtung', 'outdoor', 'deko', 'event'],
                    'price' => 16.00,
                    'feature_name' => 'Laenge',
                    'feature_values' => ['5 m', '10 m', '20 m'],
                ],
                [
                    'name' => 'Barhocker gepolstert',
                    'description' => 'Bequemer Hocker fuer Theken und Stehbereiche.',
                    'keywords' => ['barhocker', 'theke', 'sitzmoebel', 'event'],
                    'price' => 8.90,
                ],
            ],
        ],
        'Eventtechnik' => [
            'image' => 'categories/eventtechnik.jpg',
            'products' => [
                [
                    'name' => 'Kabelbruecke 3-Kanal',
                    'description' => 'Sicherheitsbruecke fuer laufende Kabelwege im Publikumsbereich.',
                    'keywords' => ['kabelbruecke', 'sicherheit', 'event', 'technik'],
                    'price' => 7.50,
                ],
                [
                    'name' => 'Powercon Stromkabel',
                    'description' => 'Zuverlaessiges Verbindungskabel fuer Licht- und Tontechnik.',
                    'keywords' => ['powercon', 'strom', 'licht', 'ton'],
                    'price' => 13.90,
                    'feature_name' => 'Laenge',
                    'feature_values' => ['1,5 m', '5 m', '10 m', '20 m'],
                ],
                [
                    'name' => 'Stehtisch mit Husse',
                    'description' => 'Eleganter Stehtisch fuer Empfangs- und Loungebereiche.',
                    'keywords' => ['stehtisch', 'empfang', 'event', 'mobiliar'],
                    'price' => 10.50,
                ],
                [
                    'name' => 'Audio-Multicore Kabel',
                    'description' => 'Signalkabel zur Verbindung von Stagebox und Mischpult.',
                    'keywords' => ['audio', 'multicore', 'tontechnik', 'buehne'],
                    'price' => 22.00,
                    'feature_name' => 'Laenge',
                    'feature_values' => ['5 m', '10 m', '20 m'],
                ],
            ],
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        foreach (self::CATEGORY_PRODUCT_DATA as $categoryName => $categoryData) {
            $category = Category::query()->create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'image_path' => $categoryData['image'],
            ]);

            foreach ($categoryData['products'] as $productData) {
                Product::query()->create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($categoryName.'-'.$productData['name']),
                    'description' => $productData['description'],
                    'keywords' => $productData['keywords'],
                    'image_path' => 'products/'.Str::slug($productData['name']).'.jpg',
                    'price' => $productData['price'],
                    'feature_name' => $productData['feature_name'] ?? null,
                    'feature_values' => $productData['feature_values'] ?? null,
                ]);
            }
        }

        Inquiry::factory(10)->create()->each(function ($inquiry) {
            $products = Product::inRandomOrder()->limit(rand(1, 5))->get();
            foreach ($products as $product) {
                $inquiry->products()->attach($product->id, ['quantity' => rand(1, 10)]);
            }
        });
    }
}
