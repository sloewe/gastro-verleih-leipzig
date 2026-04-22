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

    <article class="space-y-10">
        <div class="grid gap-10 xl:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] xl:items-start">
            <div class="xl:pr-8">
                <div class="mx-auto w-full max-w-xl overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100">
                    @if ($product->image_path)
                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="mx-auto h-auto max-h-[28rem] w-auto max-w-full object-contain p-4 sm:p-6">
                    @else
                        <div class="flex min-h-[18rem] items-center justify-center bg-gtc-mint/30">
                            <flux:icon name="photo" class="size-16 text-gtc-green/25" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.14em] text-gtc-muted">
                        {{ $product->category->name }}
                    </p>
                    <flux:heading size="2xl" level="1" class="text-gtc-ink">{{ $product->name }}</flux:heading>
                    <flux:text class="text-4xl font-semibold text-gtc-ink">
                        {{ number_format((float) $product->price, 2, ',', '.') }} €
                    </flux:text>
                    <flux:text class="max-w-2xl text-sm leading-relaxed text-gtc-muted">
                        {{ __('Preise sind netto zzgl. :rate% MwSt. und beziehen sich auf den ersten Ausleihtag ab Lager. Jeder weitere Tag kostet 50% des Angebotspreises. Eine Lieferung ist nach Absprache möglich.', ['rate' => number_format((float) ($product->vat_rate ?? 19), 0, ',', '.')]) }}
                    </flux:text>
                </div>

                @if ($product->feature_name && ! empty($product->feature_values))
                    <flux:field class="max-w-xs">
                        <flux:label>{{ $product->feature_name }}</flux:label>
                        <flux:select wire:model.live="selectedFeatureValue">
                            <flux:select.option value="">{{ __('Bitte auswählen') }}</flux:select.option>
                            @foreach ($product->feature_values as $featureValue)
                                <flux:select.option value="{{ $featureValue }}">{{ $featureValue }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endif

                @if (! empty($product->keywords))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($product->keywords as $keyword)
                            <flux:badge size="sm" inset="top bottom" class="!bg-zinc-200 !text-zinc-900 dark:!bg-zinc-300 dark:!text-zinc-900 font-medium">{{ $keyword }}</flux:badge>
                        @endforeach
                    </div>
                @endif

                <flux:button wire:click="addToInquiryList" class="btn-primary-inquiry">{{ __('Zur Anfrage hinzufügen') }}</flux:button>
            </div>
        </div>

        <div class="border-t border-zinc-200 pt-8">
            <div class="grid gap-4 md:grid-cols-[12rem_minmax(0,1fr)] md:gap-8">
                <p class="text-sm uppercase tracking-[0.14em] text-gtc-muted">{{ __('Beschreibung') }}</p>
                <div class="space-y-4 text-base leading-relaxed text-gtc-ink">
                    @if ($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <p>{{ __('Für dieses Produkt ist aktuell keine ausführliche Beschreibung hinterlegt.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </article>
</div>
