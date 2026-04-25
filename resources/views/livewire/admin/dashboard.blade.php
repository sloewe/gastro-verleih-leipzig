<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('Operativer Ueberblick zu Anfragen, Produkten und Umsatzentwicklung.') }}</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('Neue Anfragen (24h)') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['newInquiries24h'], 0, ',', '.') }}</flux:heading>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('Aktive Anfragen') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['activeInquiryCount'], 0, ',', '.') }}</flux:heading>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('Umsatz (Monat)') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['revenueMonth'], 2, ',', '.') }} EUR</flux:heading>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <div class="mb-4 flex items-center justify-between gap-2">
                <flux:heading size="lg">{{ __('Neueste Anfragen') }}</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('admin.inquiries')">
                    {{ __('Alle anzeigen') }}
                </flux:button>
            </div>

            <div class="space-y-2">
                @forelse ($metrics['recentInquiries'] as $inquiry)
                    <div class="flex items-center justify-between gap-3 rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                        <div>
                            <div class="font-medium">{{ trim($inquiry->first_name.' '.$inquiry->last_name) }}</div>
                            <div class="text-xs text-zinc-500">{{ $inquiry->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-zinc-500">{{ __('Positionen') }}: {{ $inquiry->products_count }}</div>
                            <flux:badge size="sm">{{ __($this->statusLabel($inquiry->status)) }}</flux:badge>
                        </div>
                    </div>
                @empty
                    <flux:text>{{ __('Noch keine Anfragen vorhanden.') }}</flux:text>
                @endforelse
            </div>
        </div>

        <div class="grid gap-6 xl:grid-rows-2">
            <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                <flux:heading size="lg">{{ __('Aktive Anfragen nach Status') }}</flux:heading>

                <div class="mt-4 space-y-3">
                    @foreach ($this->statusLabels() as $status => $label)
                        @php
                            $count = $metrics['activeStatuses'][$status] ?? 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-300">{{ __($label) }}</span>
                                <span class="text-zinc-500">{{ $count }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div
                                    class="h-2 rounded-full bg-zinc-500 dark:bg-zinc-300"
                                    style="width: {{ $metrics['activeInquiryCount'] > 0 ? ($count / $metrics['activeInquiryCount']) * 100 : 0 }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                <flux:heading size="lg">{{ __('Sortiment-Impulse') }}</flux:heading>
                <div class="mt-4 rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Produkte ohne Nachfrage (90 Tage)') }}</flux:text>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse ($metrics['inactiveProducts'] as $productName)
                            <flux:badge>{{ $productName }}</flux:badge>
                        @empty
                            <flux:text>{{ __('Alle Produkte hatten Nachfrage in den letzten 90 Tagen.') }}</flux:text>
                        @endforelse
                    </div>
                </div>

                <div class="mt-3 rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Offenes Angebotsvolumen') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['openQuoteVolume'], 2, ',', '.') }} EUR</flux:heading>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('Anfragen pro Monat (12 Monate)') }}</flux:heading>

            <div class="mt-4 space-y-2">
                @php
                    $maxMonthlyInquiryCount = max(array_column($metrics['monthlyInquiries'], 'count')) ?: 1;
                @endphp

                @foreach ($metrics['monthlyInquiries'] as $monthRow)
                    <div wire:key="monthly-inquiries-{{ $monthRow['month'] }}" class="grid grid-cols-[4.5rem_1fr_auto] items-center gap-3 text-sm text-zinc-700 dark:text-zinc-200">
                        <span class="text-zinc-500">{{ $monthRow['month'] }}</span>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div
                                class="h-2 rounded-full bg-zinc-600 dark:bg-zinc-300"
                                style="width: {{ ($monthRow['count'] / $maxMonthlyInquiryCount) * 100 }}%"
                            ></div>
                        </div>
                        <span>{{ $monthRow['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('Top Produkte (30 Tage)') }}</flux:heading>
            <div class="mt-4 space-y-2">
                @forelse ($metrics['topProducts'] as $product)
                    <div class="flex items-start justify-between gap-3 rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                        <div class="font-medium">{{ $product->product_name }}</div>
                        <div class="text-right text-xs text-zinc-500">
                            <div>{{ __('Anfragen') }}: {{ $product->inquiry_count }}</div>
                            <div>{{ __('Menge') }}: {{ $product->total_quantity }}</div>
                        </div>
                    </div>
                @empty
                    <flux:text>{{ __('Keine Produktanfragen im gewaehlten Zeitraum.') }}</flux:text>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('Umsatzkennzahlen') }}</flux:heading>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Heute') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['revenueToday'], 2, ',', '.') }} EUR</flux:heading>
                </div>
                <div class="rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Monat') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['revenueMonth'], 2, ',', '.') }} EUR</flux:heading>
                </div>
                <div class="rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('YTD') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['yearToDateRevenue'], 2, ',', '.') }} EUR</flux:heading>
                </div>
                <div class="rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Ø Auftragswert') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['averageOrderValue'], 2, ',', '.') }} EUR</flux:heading>
                </div>
            </div>
            <flux:text size="sm" class="mt-3 text-zinc-500">{{ __($metrics['revenueDefinition']) }}</flux:text>
        </div>

        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('Umsatz pro Monat (12 Monate)') }}</flux:heading>
            <div class="mt-4 space-y-2">
                @php
                    $maxMonthlyRevenue = max(array_column($metrics['monthlyRevenue'], 'amount')) ?: 1;
                @endphp

                @foreach ($metrics['monthlyRevenue'] as $monthRow)
                    <div wire:key="monthly-revenue-{{ $monthRow['month'] }}" class="grid grid-cols-[4.5rem_1fr_auto] items-center gap-3 text-sm text-zinc-700 dark:text-zinc-200">
                        <span class="text-zinc-500">{{ $monthRow['month'] }}</span>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div
                                class="h-2 rounded-full bg-emerald-600 dark:bg-emerald-300"
                                style="width: {{ ($monthRow['amount'] / $maxMonthlyRevenue) * 100 }}%"
                            ></div>
                        </div>
                        <span>{{ number_format($monthRow['amount'], 0, ',', '.') }} EUR</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
