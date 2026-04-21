<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Categories extends Component
{
    use WithFileUploads;

    public $search = '';

    public ?Category $category = null;

    public $name = '';

    public $slug = '';

    public $image = null;

    public $editing = false;

    public function updatedName($value)
    {
        if (! $this->editing) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->reset(['name', 'slug', 'image', 'editing', 'category']);
        $this->slug = '';
        $this->dispatch('modal-show', name: 'category-modal');
    }

    public function edit(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->editing = true;
        $this->image = null;
        $this->dispatch('modal-show', name: 'category-modal');
    }

    public function save()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'slug' => 'required|min:3|max:255|unique:categories,slug'.($this->editing ? ','.$this->category->id : ''),
            'image' => 'nullable|image|max:1024',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
        ];

        if ($this->image) {
            if ($this->editing && $this->category->image_path) {
                Storage::disk('public')->delete($this->category->image_path);
            }
            $data['image_path'] = $this->image->store('categories', 'public');
        }

        if ($this->editing) {
            $this->category->update($data);
            Flux::toast(__('Kategorie wurde aktualisiert.'));
        } else {
            Category::create($data);
            Flux::toast(__('Kategorie wurde erstellt.'));
        }

        $this->dispatch('modal-close', name: 'category-modal');
        $this->reset(['name', 'slug', 'image', 'editing', 'category']);
    }

    public function delete(Category $category)
    {
        $this->category = $category;
        $this->dispatch('modal-show', name: 'delete-confirmation');
    }

    public function confirmDelete()
    {
        if ($this->category->image_path) {
            Storage::disk('public')->delete($this->category->image_path);
        }

        $this->category->delete();
        Flux::toast(__('Kategorie wurde gelöscht.'));

        $this->dispatch('modal-close', name: 'delete-confirmation');
        $this->reset(['category']);
    }

    public function categories()
    {
        return Category::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->latest()
            ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.categories');
    }
}
