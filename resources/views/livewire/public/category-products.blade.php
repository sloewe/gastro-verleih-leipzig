<div class="space-y-8">
    <x-slot:title>{{ $category->name }}</x-slot:title>

    <section class="space-y-3">
        <flux:subheading size="sm" class="uppercase tracking-wider text-gtc-muted">
            {{ __('products') }}
        </flux:subheading>
        <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
            {{ $category->name }}
        </flux:heading>
    </section>

    <div class="space-y-5">
        @forelse ($products as $product)
            <article class="overflow-hidden rounded-apple border border-zinc-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                <div class="grid gap-6 p-6 md:grid-cols-[220px_1fr]">
                    <a href="{{ route('product.show', $product->slug) }}" class="block overflow-hidden rounded-2xl bg-zinc-100">
                        @if ($product->image_path)
                            <img
                                src="{{ Storage::url($product->image_path) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                            >
                        @else
                            <div class="flex min-h-44 items-center justify-center bg-gtc-mint/30">
                                <flux:icon name="photo" class="size-10 text-gtc-green/25" />
                            </div>
                        @endif
                    </a>

                    <div class="flex h-full flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <flux:heading size="lg" level="2" class="hover:text-gtc-green transition-colors">
                                    {{ $product->name }}
                                </flux:heading>
                            </a>
                            <flux:text class="line-clamp-2 text-gtc-muted">
                                {{ $product->description ?: __('furtherDetailsCanBeFoundInTheProductView') }}
                            </flux:text>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <flux:text class="text-lg font-semibold text-gtc-green">
                                {{ number_format((float) $product->price, 2, ',', '.') }} €
                            </flux:text>
                            <flux:text class="text-xs text-gtc-muted">
                                {{ __('zzgl. MwSt. (:rate%)', ['rate' => number_format((float) ($product->vat_rate ?? 19), 2, ',', '.')]) }}
                            </flux:text>

                            @if ($product->feature_name && ! empty($product->feature_values))
                                <flux:field>
                                    <flux:label>{{ $product->feature_name }}</flux:label>
                                    <flux:select wire:model.live="selectedFeatureValues.{{ $product->id }}">
                                        <flux:select.option value="">{{ __('pleaseSelect') }}</flux:select.option>
                                        @foreach ($product->feature_values as $featureValue)
                                            <flux:select.option value="{{ $featureValue }}">{{ $featureValue }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            @endif

                            <flux:button wire:click="addToInquiryList({{ $product->id }})" class="btn-primary-inquiry">
                                {{ __('addToInquiry') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-apple border border-dashed border-zinc-300 bg-white p-8 text-center">
                <flux:text class="text-gtc-muted">
                    {{ __('noProductsAreCurrentlyAvailableInThisCategory') }}
                </flux:text>
            </div>
        @endforelse
    </div>
</div>
