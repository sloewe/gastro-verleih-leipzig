<?php

namespace App\Livewire\Public;

use Livewire\Attributes\On;
use Livewire\Component;

class InquiryListBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('inquiry-list-updated')]
    public function refreshCount(): void
    {
        $this->count = collect(session('inquiry_list.items', []))
            ->sum(fn (array $item): int => max(1, (int) ($item['quantity'] ?? 1)));
    }

    public function render()
    {
        return view('livewire.public.inquiry-list-badge');
    }
}
