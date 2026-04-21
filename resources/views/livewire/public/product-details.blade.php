@push('meta')
    @if (! empty($product->keywords))
        <meta name="keywords" content="{{ implode(', ', $product->keywords) }}">
    @endif
@endpush

<div class="space-y-10">
    <x-slot:title>{{ $product->name }}</x-slot:title>

    <div class="flex flex-wrap items-center gap-2 text-sm text-gtc-muted">
        <a href="{{ route('home') }}" class="hover:text-gtc-green">{{ __('Home') }}</a>
        <span>/</span>
        <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-gtc-green">{{ $product->category->name }}</a>
        <span>/</span>
        <span>{{ $product->name }}</span>
    </div>

    <article class="overflow-hidden rounded-apple border border-zinc-200 bg-white shadow-sm">
        <div class="grid gap-8 p-6 lg:grid-cols-2 lg:p-8">
            <div class="overflow-hidden rounded-2xl bg-zinc-100">
                @if ($product->image_path)
                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex min-h-[20rem] items-center justify-center bg-gtc-mint/30">
                        <flux:icon name="photo" class="size-16 text-gtc-green/25" />
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="space-y-3">
                    <flux:heading size="2xl" level="1" class="text-gtc-green">{{ $product->name }}</flux:heading>
                    <flux:text class="text-2xl font-semibold text-gtc-ink">
                        {{ number_format((float) $product->price, 2, ',', '.') }} €
                    </flux:text>
                </div>

                <flux:text class="leading-relaxed text-gtc-muted">
                    {{ $product->description ?: __('Für dieses Produkt ist aktuell keine ausführliche Beschreibung hinterlegt.') }}
                </flux:text>

                @if ($product->feature_name && ! empty($product->feature_values))
                    <flux:field>
                        <flux:label>{{ $product->feature_name }}</flux:label>
                        <flux:select>
                            @foreach ($product->feature_values as $featureValue)
                                <flux:select.option value="{{ $featureValue }}">{{ $featureValue }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endif

                @if (! empty($product->keywords))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($product->keywords as $keyword)
                            <flux:badge size="sm" inset="top bottom">{{ $keyword }}</flux:badge>
                        @endforeach
                    </div>
                @endif

                <flux:button variant="primary">{{ __('Zur Anfrage hinzufügen') }}</flux:button>
            </div>
        </div>
    </article>
</div>
