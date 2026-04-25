@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Gastro-Verleih-Leipzig" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-12 items-center justify-center rounded-md bg-white">
            <img src="{{ asset('Logo_gastro-Verleih.png') }}" alt="Gastro-Verleih-Leipzig Logo" class="size-12 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Gastro-Verleih-Leipzig" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-12 items-center justify-center rounded-md bg-white">
            <img src="{{ asset('Logo_gastro-Verleih.png') }}" alt="Gastro-Verleih-Leipzig Logo" class="size-12 object-contain" />
        </x-slot>
    </flux:brand>
@endif
