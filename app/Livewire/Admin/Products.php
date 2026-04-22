<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Products extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $categoryFilter = '';

    public ?Product $product = null;

    public $category_id = '';

    public $name = '';

    public $slug = '';

    public $description = '';

    public $keywords = '';

    public $price = '';

    public $vat_rate = '19';

    public $image = null;

    public $feature_name = '';

    public $feature_values = '';

    public $editing = false;

    public function updatedName($value)
    {
        if (! $this->editing) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->reset(['category_id', 'name', 'slug', 'description', 'keywords', 'price', 'vat_rate', 'image', 'feature_name', 'feature_values', 'editing', 'product']);
        $this->vat_rate = '19';
        $this->slug = '';
        $this->dispatch('modal-show', name: 'product-modal');
    }

    public function edit(Product $product)
    {
        $this->product = $product;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->keywords = is_array($product->keywords) ? implode(', ', $product->keywords) : $product->keywords;
        $this->price = $product->price;
        $this->vat_rate = (string) $product->vat_rate;
        $this->feature_name = $product->feature_name;
        $this->feature_values = is_array($product->feature_values) ? implode(', ', $product->feature_values) : $product->feature_values;

        $this->editing = true;
        $this->image = null;
        $this->dispatch('modal-show', name: 'product-modal');
    }

    public function save()
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|min:3|max:255',
            'slug' => 'required|min:3|max:255|unique:products,slug'.($this->editing ? ','.$this->product->id : ''),
            'description' => 'nullable',
            'keywords' => 'nullable',
            'price' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:99.99',
            'image' => 'nullable|image|max:2048',
            'feature_name' => 'nullable|max:255',
            'feature_values' => 'nullable',
        ];

        $this->validate($rules);

        $keywordsArray = $this->keywords ? array_map('trim', explode(',', $this->keywords)) : [];
        $featureValuesArray = $this->feature_values ? array_map('trim', explode(',', $this->feature_values)) : [];

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'keywords' => $keywordsArray,
            'price' => $this->price,
            'vat_rate' => $this->vat_rate,
            'feature_name' => $this->feature_name,
            'feature_values' => $featureValuesArray,
        ];

        if ($this->image) {
            if ($this->editing && $this->product->image_path) {
                Storage::disk('public')->delete($this->product->image_path);
            }
            $data['image_path'] = $this->image->store('products', 'public');
        }

        if ($this->editing) {
            $this->product->update($data);
            Flux::toast(__('Produkt wurde aktualisiert.'));
        } else {
            Product::create($data);
            Flux::toast(__('Produkt wurde erstellt.'));
        }

        $this->dispatch('modal-close', name: 'product-modal');
        $this->reset(['category_id', 'name', 'slug', 'description', 'keywords', 'price', 'vat_rate', 'image', 'feature_name', 'feature_values', 'editing', 'product']);
        $this->vat_rate = '19';
    }

    public function delete(Product $product)
    {
        $this->product = $product;
        $this->dispatch('modal-show', name: 'delete-confirmation');
    }

    public function confirmDelete()
    {
        if ($this->product->image_path) {
            Storage::disk('public')->delete($this->product->image_path);
        }

        $this->product->delete();
        Flux::toast(__('Produkt wurde gelöscht.'));

        $this->dispatch('modal-close', name: 'delete-confirmation');
        $this->reset(['product']);
    }

    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.products', [
            'products' => Product::query()
                ->with('category')
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
                ->latest()
                ->paginate(10),
        ]);
    }
}
