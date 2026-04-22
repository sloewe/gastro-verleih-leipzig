<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryProducts extends Component
{
    public Category $category;

    /** @var array<int, string> */
    public array $selectedFeatureValues = [];

    public function mount(string $slug): void
    {
        $this->category = Category::query()
            ->with('products')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function addToInquiryList(int $productId): void
    {
        $product = Product::query()
            ->where('category_id', $this->category->id)
            ->findOrFail($productId);

        $featureValue = trim((string) ($this->selectedFeatureValues[$product->id] ?? ''));

        if ($product->feature_name && ! empty($product->feature_values)) {
            if ($featureValue === '' || ! in_array($featureValue, $product->feature_values, true)) {
                Flux::toast(__('Bitte wählen Sie eine gültige Option aus.'), variant: 'warning');

                return;
            }
        } else {
            $featureValue = '';
        }

        $this->storeSessionItem($product->id, $featureValue);

        Flux::toast(__('Produkt zur Anfrageliste hinzugefügt.'));
    }

    private function storeSessionItem(int $productId, string $featureValue): void
    {
        $key = $productId.'|'.$featureValue;
        $items = collect(session('inquiry_list.items', []));

        $existingIndex = $items->search(
            fn (array $item): bool => ((string) $item['key']) === $key
        );

        if ($existingIndex === false) {
            $items->push([
                'key' => $key,
                'product_id' => $productId,
                'feature_value' => $featureValue,
                'quantity' => 1,
            ]);
        } else {
            $existingItem = $items->get($existingIndex);
            $existingItem['quantity'] = ((int) $existingItem['quantity']) + 1;
            $items->put($existingIndex, $existingItem);
        }

        session()->put('inquiry_list.items', $items->values()->all());
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.category-products', [
            'products' => $this->category->products()->orderBy('name')->get(),
        ]);
    }
}
