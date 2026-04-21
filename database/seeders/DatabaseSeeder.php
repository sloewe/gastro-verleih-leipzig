<?php

namespace Database\Seeders;

use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
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

        $categories = Category::factory(5)
            ->has(Product::factory(10))
            ->create();

        Inquiry::factory(10)->create()->each(function ($inquiry) {
            $products = Product::inRandomOrder()->limit(rand(1, 5))->get();
            foreach ($products as $product) {
                $inquiry->products()->attach($product->id, ['quantity' => rand(1, 10)]);
            }
        });
    }
}
