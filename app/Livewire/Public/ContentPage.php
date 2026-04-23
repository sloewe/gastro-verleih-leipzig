<?php

namespace App\Livewire\Public;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContentPage extends Component
{
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::query()
            ->with('blocks')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.content-page');
    }
}
