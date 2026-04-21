<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.home', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
