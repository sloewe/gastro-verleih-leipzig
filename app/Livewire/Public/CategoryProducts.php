<?php

namespace App\Livewire\Public;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryProducts extends Component
{
    public Category $category;

    public function mount(string $slug): void
    {
        $this->category = Category::query()
            ->with('products')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.category-products', [
            'products' => $this->category->products()->orderBy('name')->get(),
        ]);
    }
}
