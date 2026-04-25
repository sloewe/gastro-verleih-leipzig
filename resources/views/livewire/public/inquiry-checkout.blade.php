<div class="space-y-8">
    <x-slot:title>{{ __('Anfrage absenden') }}</x-slot:title>

    @if (session()->has('checkout_error'))
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('checkout_error') }}
        </div>
    @endif

    <section class="space-y-2">
        <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
            {{ __('Anfrage absenden') }}
        </flux:heading>
        <flux:text class="text-gtc-muted">
            {{ __('Bitte hinterlegen Sie Ihre Kontaktdaten. Wir melden uns zeitnah mit einem Angebot.') }}
        </flux:text>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(20rem,1fr)]">
        <form wire:submit="submit" class="inquiry-checkout-form space-y-5 rounded-apple border border-zinc-200 bg-white p-6 shadow-sm">
            <flux:text class="text-xs text-gtc-muted">{{ __('Pflichtfelder sind mit * markiert.') }}</flux:text>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Anrede *') }}</flux:label>
                    <flux:select wire:model="salutation">
                        <flux:select.option value="">{{ __('Bitte auswaehlen') }}</flux:select.option>
                        <flux:select.option value="Herr">{{ __('Herr') }}</flux:select.option>
                        <flux:select.option value="Frau">{{ __('Frau') }}</flux:select.option>
                        <flux:select.option value="Divers">{{ __('Divers') }}</flux:select.option>
                    </flux:select>
                    @error('salutation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Vorname *') }}</flux:label>
                    <flux:input wire:model="first_name" />
                    @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Nachname *') }}</flux:label>
                    <flux:input wire:model="last_name" />
                    @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('E-Mail *') }}</flux:label>
                    <flux:input type="email" wire:model="email" />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Telefonnummer (optional)') }}</flux:label>
                    <flux:input wire:model="phone" />
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Firma (optional)') }}</flux:label>
                <flux:input wire:model="company" />
                @error('company') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </flux:field>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Strasse *') }}</flux:label>
                    <flux:input wire:model="street" />
                    @error('street') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </flux:field>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('PLZ *') }}</flux:label>
                        <flux:input wire:model="postal_code" />
                        @error('postal_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Ort *') }}</flux:label>
                        <flux:input wire:model="city" />
                        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </flux:field>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Nachricht (optional)') }}</flux:label>
                <flux:textarea rows="5" wire:model="message" />
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </flux:field>

            <div class="pt-2">
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                    {{ __('Anfrage senden') }}
                </flux:button>
            </div>
        </form>

        <aside class="h-fit rounded-apple border border-zinc-200 bg-white p-6 shadow-sm xl:sticky xl:top-24">
            <flux:heading size="md" level="2" class="mb-4 uppercase tracking-wide text-zinc-600">
                {{ __('Ihre Positionen') }}
            </flux:heading>

            <div class="space-y-3">
                @foreach ($items as $item)
                    <div wire:key="checkout-item-{{ $item['key'] }}" class="rounded-lg border border-zinc-200 p-3">
                        <flux:text class="text-sm font-semibold text-gtc-ink">{{ $item['product_name'] }}</flux:text>
                        @if ($item['feature_value'])
                            <flux:text class="text-xs text-zinc-600">{{ __('Auswahl') }}: {{ $item['feature_value'] }}</flux:text>
                        @endif
                        <flux:text class="text-xs text-zinc-600">{{ __('Menge') }}: {{ $item['quantity'] }}</flux:text>
                        <div class="mt-2 space-y-1 border-t border-zinc-100 pt-2">
                            <div class="flex items-center justify-between">
                                <flux:text class="text-xs text-gtc-muted">{{ __('Netto') }}</flux:text>
                                <flux:text class="text-xs text-gtc-ink">{{ number_format($item['line_net'], 2, ',', '.') }} €</flux:text>
                            </div>
                            <div class="flex items-center justify-between">
                                <flux:text class="text-xs text-gtc-muted">{{ __('MwSt.') }} ({{ number_format($item['vat_rate'], 2, ',', '.') }}%)</flux:text>
                                <flux:text class="text-xs text-gtc-ink">{{ number_format($item['line_vat'], 2, ',', '.') }} €</flux:text>
                            </div>
                            <div class="flex items-center justify-between">
                                <flux:text class="text-xs font-semibold text-gtc-ink">{{ __('Brutto') }}</flux:text>
                                <flux:text class="text-xs font-semibold text-gtc-ink">{{ number_format($item['line_gross'], 2, ',', '.') }} €</flux:text>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 border-t border-zinc-200 pt-4">
                <div class="flex items-center justify-between">
                    <flux:text class="text-sm text-gtc-muted">{{ __('Nettosumme') }}</flux:text>
                    <flux:text class="text-sm font-semibold text-gtc-ink">{{ number_format($summary['subtotal_net'], 2, ',', '.') }} €</flux:text>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <flux:text class="text-sm text-gtc-muted">{{ __('Mehrwertsteuer') }}</flux:text>
                    <flux:text class="text-sm font-semibold text-gtc-ink">{{ number_format($summary['subtotal_vat'], 2, ',', '.') }} €</flux:text>
                </div>
                <div class="mt-2 flex items-center justify-between border-t border-zinc-100 pt-2">
                    <flux:text class="text-sm font-semibold text-gtc-ink">{{ __('Bruttosumme') }}</flux:text>
                    <flux:text class="text-sm font-semibold text-gtc-ink">{{ number_format($summary['subtotal_gross'], 2, ',', '.') }} €</flux:text>
                </div>
            </div>
        </aside>
    </div>
</div>
