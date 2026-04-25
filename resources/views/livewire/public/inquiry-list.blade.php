<div class="space-y-8">
    <x-slot:title>{{ __('Anfrageliste') }}</x-slot:title>

    @if (session()->has('checkout_error'))
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('checkout_error') }}
        </div>
    @endif

    <section class="space-y-3">
        <flux:subheading size="sm" class="uppercase tracking-wider text-gtc-muted">
            {{ __('Anfrage') }}
        </flux:subheading>
        <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
            {{ __('Ihre Anfrageliste') }}
        </flux:heading>
    </section>

    @if (empty($items))
        <div class="rounded-apple border border-dashed border-zinc-300 bg-white p-8 text-center space-y-4">
            <flux:text class="text-gtc-muted">
                {{ __('Ihre Anfrageliste ist aktuell leer.') }}
            </flux:text>

            <flux:button :href="route('home')" variant="primary">
                {{ __('Produkte entdecken') }}
            </flux:button>
        </div>
    @else
        <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(18rem,1fr)]">
            <section class="overflow-hidden rounded-apple border border-zinc-200 bg-white shadow-sm">
                <div class="hidden grid-cols-[minmax(0,1.6fr)_minmax(0,0.6fr)_minmax(0,0.9fr)_minmax(0,0.9fr)] gap-4 border-b border-zinc-200 px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 md:grid">
                    <span>{{ __('Produkt') }}</span>
                    <span class="text-right">{{ __('Preis') }}</span>
                    <span class="text-center">{{ __('Anzahl') }}</span>
                    <span class="text-right">{{ __('Zwischensumme') }}</span>
                </div>

                <div class="divide-y divide-zinc-200">
                    @foreach ($items as $item)
                        <article class="grid gap-4 px-6 py-5 md:grid-cols-[minmax(0,1.6fr)_minmax(0,0.6fr)_minmax(0,0.9fr)_minmax(0,0.9fr)] md:items-center">
                            <div class="flex items-start gap-3">
                                <a href="{{ route('product.show', $item['product_slug']) }}" class="block h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-zinc-100">
                                    @if ($item['product_image_path'])
                                        <img
                                            src="{{ Storage::url($item['product_image_path']) }}"
                                            alt="{{ $item['product_name'] }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gtc-mint/30">
                                            <flux:icon name="photo" class="size-5 text-gtc-green/40" />
                                        </div>
                                    @endif
                                </a>

                                <div class="space-y-1">
                                    <a
                                        href="{{ route('product.show', $item['product_slug']) }}"
                                        class="group inline-block rounded-sm text-gtc-green transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:text-gtc-green/80"
                                    >
                                        <flux:heading size="sm" level="2" class="!text-gtc-green transition-colors duration-150 group-hover:!text-gtc-leaf group-active:!text-gtc-green/80">{{ $item['product_name'] }}</flux:heading>
                                    </a>

                                    <p class="text-xs leading-relaxed text-zinc-700">
                                        {{ $item['product_description'] ?: __('Weitere Details finden Sie in der Produktansicht.') }}
                                    </p>

                                    @if ($item['feature_value'])
                                        <flux:text class="text-xs text-zinc-600">
                                            {{ __('Auswahl') }}: {{ $item['feature_value'] }}
                                        </flux:text>
                                    @endif

                                    <flux:text class="text-xs text-zinc-600">
                                        {{ __('MwSt.') }} {{ number_format($item['vat_rate'], 2, ',', '.') }} %
                                    </flux:text>
                                </div>
                            </div>

                            <flux:text class="text-sm font-semibold text-gtc-ink md:text-right">
                                {{ number_format($item['price'], 2, ',', '.') }} €
                            </flux:text>

                            <div class="flex items-center justify-start gap-2 md:justify-center">
                                <flux:button wire:click="decreaseQuantity('{{ $item['key'] }}')" variant="ghost" size="sm" icon="minus" class="!text-gtc-ink" />
                                <input
                                    type="number"
                                    min="1"
                                    inputmode="numeric"
                                    value="{{ $item['quantity'] }}"
                                    wire:change="updateQuantity('{{ $item['key'] }}', $event.target.value)"
                                    class="w-14 rounded-lg border border-zinc-300 bg-white px-2 py-1 text-center text-sm font-semibold text-gtc-ink focus:border-gtc-green focus:outline-none focus:ring-2 focus:ring-gtc-green/25"
                                    aria-label="{{ __('Anzahl für :product', ['product' => $item['product_name']]) }}"
                                >
                                <flux:button wire:click="increaseQuantity('{{ $item['key'] }}')" variant="ghost" size="sm" icon="plus" class="!text-gtc-ink" />
                            </div>

                            <div class="flex items-center justify-between gap-3 md:justify-end">
                                <flux:text class="text-sm font-semibold text-gtc-ink">
                                    {{ number_format($item['line_net'], 2, ',', '.') }} €
                                </flux:text>
                                <flux:button
                                    wire:click="removeItem('{{ $item['key'] }}')"
                                    variant="ghost"
                                    size="sm"
                                    icon="x-mark"
                                    class="!text-zinc-500 hover:!text-red-600"
                                />
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <aside class="h-fit rounded-apple border border-zinc-200 bg-white p-6 shadow-sm xl:sticky xl:top-24">
                <flux:heading size="md" level="2" class="mb-4 uppercase tracking-wide text-zinc-600">
                    {{ __('Anfrage-Summe') }}
                </flux:heading>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-4 border-b border-zinc-200 pb-2">
                        <flux:text class="text-sm text-gtc-muted">{{ __('Nettopreis') }}</flux:text>
                        <flux:text class="text-sm font-semibold text-gtc-ink">{{ number_format($summary['subtotal_net'], 2, ',', '.') }} €</flux:text>
                    </div>

                    <div class="flex items-center justify-between gap-4 border-b border-zinc-200 pb-2">
                        <flux:text class="text-sm text-gtc-muted">{{ __('MwSt.') }}</flux:text>
                        <flux:text class="text-sm font-semibold text-gtc-ink">{{ number_format($summary['vat_total'], 2, ',', '.') }} €</flux:text>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-1">
                        <flux:text class="text-base font-semibold text-gtc-ink">{{ __('Gesamtpreis') }}</flux:text>
                        <flux:heading size="lg" level="3" class="text-gtc-green">
                            {{ number_format($summary['grand_total'], 2, ',', '.') }} €
                        </flux:heading>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <flux:button :href="route('home')" variant="ghost" class="w-full">
                        {{ __('Einkauf fortsetzen') }}
                    </flux:button>

                    <flux:button :href="route('inquiry.checkout')" variant="primary" class="w-full">
                        {{ __('Weiter zur Anfrage') }}
                    </flux:button>
                </div>
            </aside>
        </div>
    @endif
</div>
