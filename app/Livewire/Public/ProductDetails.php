<?php

namespace App\Livewire\Public;

use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductDetails extends Component
{
    public Product $product;

    public string $selectedFeatureValue = '';

    public function mount(string $slug): void
    {
        $this->product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function addToInquiryList(): void
    {
        $featureValue = trim($this->selectedFeatureValue);

        if ($this->product->feature_name && ! empty($this->product->feature_values)) {
            if ($featureValue === '' || ! in_array($featureValue, $this->product->feature_values, true)) {
                Flux::toast(__('Bitte wählen Sie eine gültige Option aus.'), variant: 'warning');

                return;
            }
        } else {
            $featureValue = '';
        }

        $key = $this->product->id.'|'.$featureValue;
        $items = collect(session('inquiry_list.items', []));

        $existingIndex = $items->search(
            fn (array $item): bool => ((string) $item['key']) === $key
        );

        if ($existingIndex === false) {
            $items->push([
                'key' => $key,
                'product_id' => $this->product->id,
                'feature_value' => $featureValue,
                'quantity' => 1,
            ]);
        } else {
            $existingItem = $items->get($existingIndex);
            $existingItem['quantity'] = ((int) $existingItem['quantity']) + 1;
            $items->put($existingIndex, $existingItem);
        }

        session()->put('inquiry_list.items', $items->values()->all());
        $this->dispatch('inquiry-list-updated');

        Flux::toast(__('Produkt zur Anfrageliste hinzugefügt.'));
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.product-details');
    }
}
