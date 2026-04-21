<?php

namespace App\Livewire\Public;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductDetails extends Component
{
    public Product $product;

    public function mount(string $slug): void
    {
        $this->product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.product-details');
    }
}
