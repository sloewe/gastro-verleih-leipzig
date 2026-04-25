<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('operationalOverviewOfInquiriesProductsAndRevenueDevelopment') }}</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('newInquiries24h') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['newInquiries24h'], 0, ',', '.') }}</flux:heading>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('activeInquiries') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['activeInquiryCount'], 0, ',', '.') }}</flux:heading>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:text class="text-zinc-500">{{ __('revenueMonth') }}</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($metrics['revenueMonth'], 2, ',', '.') }} EUR</flux:heading>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <div class="mb-4 flex items-center justify-between gap-2">
                <flux:heading size="lg">{{ __('latestInquiries') }}</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('admin.inquiries')">
                    {{ __('viewAll') }}
                </flux:button>
            </div>

            <div class="space-y-2">
                @forelse ($metrics['recentInquiries'] as $inquiry)
                    <button
                        wire:click="selectInquiry({{ $inquiry->id }})"
                        type="button"
                        class="flex w-full cursor-pointer items-center justify-between gap-3 rounded-md border border-zinc-200 px-3 py-2 text-left text-sm text-zinc-700 transition hover:border-zinc-300 hover:bg-zinc-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-zinc-600 dark:hover:bg-zinc-800/60 dark:focus-visible:ring-zinc-500"
                    >
                        <div>
                            <div class="font-medium">{{ trim($inquiry->first_name.' '.$inquiry->last_name) }}</div>
                            <div class="text-xs text-zinc-500">{{ $inquiry->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-zinc-500">{{ __('items') }}: {{ $inquiry->products_count }}</div>
                            <flux:badge size="sm">{{ __($this->statusLabel($inquiry->status)) }}</flux:badge>
                        </div>
                    </button>
                @empty
                    <flux:text>{{ __('noInquiriesAvailableYet') }}</flux:text>
                @endforelse
            </div>
        </div>

        <div class="grid gap-6 xl:grid-rows-2">
            <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                <flux:heading size="lg">{{ __('activeInquiriesByStatus') }}</flux:heading>

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
                <flux:heading size="lg">{{ __('assortmentInsights') }}</flux:heading>
                <div class="mt-4 rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('productsWithoutDemand90Days') }}</flux:text>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse ($metrics['inactiveProducts'] as $productName)
                            <flux:badge>{{ $productName }}</flux:badge>
                        @empty
                            <flux:text>{{ __('allProductsHadDemandInTheLast90Days') }}</flux:text>
                        @endforelse
                    </div>
                </div>

                <div class="mt-3 rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('openQuoteVolume') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['openQuoteVolume'], 2, ',', '.') }} EUR</flux:heading>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('inquiriesPerMonth12Months') }}</flux:heading>

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
            <flux:heading size="lg">{{ __('topProducts30Days') }}</flux:heading>
            <div class="mt-4 space-y-2">
                @forelse ($metrics['topProducts'] as $product)
                    <div class="flex items-start justify-between gap-3 rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                        <div class="font-medium">{{ $product->product_name }}</div>
                        <div class="text-right text-xs text-zinc-500">
                            <div>{{ __('inquiries') }}: {{ $product->inquiry_count }}</div>
                            <div>{{ __('quantity') }}: {{ $product->total_quantity }}</div>
                        </div>
                    </div>
                @empty
                    <flux:text>{{ __('noProductInquiriesInTheSelectedPeriod') }}</flux:text>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('revenueMetrics') }}</flux:heading>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-md border border-zinc-200 p-3 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                    <flux:text size="sm" class="text-zinc-500">{{ __('today') }}</flux:text>
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
                    <flux:text size="sm" class="text-zinc-500">{{ __('avgOrderValue') }}</flux:text>
                    <flux:heading size="lg">{{ number_format($metrics['averageOrderValue'], 2, ',', '.') }} EUR</flux:heading>
                </div>
            </div>
            <flux:text size="sm" class="mt-3 text-zinc-500">{{ __($metrics['revenueDefinition']) }}</flux:text>
        </div>

        <div class="rounded-lg border border-zinc-200 p-5 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
            <flux:heading size="lg">{{ __('revenuePerMonth12Months') }}</flux:heading>
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

    <flux:modal name="dashboard-inquiry-details-modal" class="max-w-5xl">
        @if ($selectedInquiry)
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4 pr-12">
                    <div>
                        <flux:heading size="lg">{{ __('inquiryNumberId', ['id' => $selectedInquiry->id]) }}</flux:heading>
                        <flux:subheading>{{ __('Eingegangen am :date', ['date' => $selectedInquiry->created_at->format('d.m.Y H:i')]) }}</flux:subheading>
                    </div>
                    <flux:badge class="shrink-0">{{ __($this->statusLabel($selectedInquiry->status)) }}</flux:badge>
                </div>

                <flux:separator text="{{ __('customerData') }}" />

                <dl class="space-y-2 text-sm text-zinc-700 dark:text-zinc-200">
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('Name') }}</dt>
                        <dd>{{ trim($selectedInquiry->first_name.' '.$selectedInquiry->last_name) }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('company') }}</dt>
                        <dd>{{ $selectedInquiry->company ?: '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('email') }}</dt>
                        <dd>{{ $selectedInquiry->email }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('phone') }}</dt>
                        <dd>{{ $selectedInquiry->phone ?: '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('inquiryPeriod') }}</dt>
                        <dd>
                            @if ($selectedInquiry->start_date && $selectedInquiry->end_date)
                                {{ $selectedInquiry->start_date->format('d.m.Y') }} - {{ $selectedInquiry->end_date->format('d.m.Y') }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('message') }}</dt>
                        <dd class="whitespace-pre-line">{{ $selectedInquiry->message ?: '—' }}</dd>
                    </div>
                </dl>

                <flux:separator text="{{ __('requestedProducts') }}" />

                <div class="space-y-3">
                    @forelse ($selectedInquiry->products as $product)
                        <div wire:key="dashboard-inquiry-product-{{ $selectedInquiry->id }}-{{ $product->id }}" class="rounded-md border border-zinc-200 px-4 py-3 dark:border-zinc-700">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-zinc-700 dark:text-zinc-200">{{ $product->name }}</div>
                                    @if ($product->pivot->feature_value)
                                        <div class="text-xs text-zinc-500">
                                            {{ __('Merkmal: :value', ['value' => $product->pivot->feature_value]) }}
                                        </div>
                                    @endif
                                </div>
                                <flux:badge size="sm" inset="top bottom">
                                    {{ __('Menge: :quantity', ['quantity' => $product->pivot->quantity]) }}
                                </flux:badge>
                            </div>
                        </div>
                    @empty
                        <flux:text>{{ __('noItemsAvailable') }}</flux:text>
                    @endforelse
                </div>
            </div>
        @endif
    </flux:modal>

</div>
