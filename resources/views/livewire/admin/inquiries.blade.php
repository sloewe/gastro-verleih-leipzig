<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('inquiries') }}</flux:heading>
        <flux:subheading>{{ __('manageIncomingCustomerInquiriesAndTheirProcessingStatus') }}</flux:subheading>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('searchInquiry') }}" icon="magnifying-glass" clearable />

            <flux:select wire:model.live="statusFilter" placeholder="{{ __('filterStatus') }}" clearable>
                @foreach ($this->statusOptions() as $statusValue => $statusLabel)
                    <flux:select.option :value="$statusValue">{{ __($statusLabel) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:table :paginate="$inquiries">
            <flux:table.columns>
                <flux:table.column sortable wire:click="sortBy('id')">ID</flux:table.column>
                <flux:table.column sortable wire:click="sortBy('created_at')">{{ __('received') }}</flux:table.column>
                <flux:table.column sortable wire:click="sortBy('start_date')">{{ __('inquiryPeriod') }}</flux:table.column>
                <flux:table.column sortable wire:click="sortBy('first_name')">{{ __('customer') }}</flux:table.column>
                <flux:table.column sortable wire:click="sortBy('email')">{{ __('contact') }}</flux:table.column>
                <flux:table.column>{{ __('items') }}</flux:table.column>
                <flux:table.column sortable wire:click="sortBy('status')">{{ __('status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($inquiries as $inquiry)
                    <flux:table.row :key="$inquiry->id">
                        <flux:table.cell>#{{ $inquiry->id }}</flux:table.cell>
                        <flux:table.cell>{{ $inquiry->created_at->format('d.m.Y H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($inquiry->start_date && $inquiry->end_date)
                                {{ $inquiry->start_date->format('d.m.Y') }} - {{ $inquiry->end_date->format('d.m.Y') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">
                            {{ trim($inquiry->first_name.' '.$inquiry->last_name) }}
                            @if ($inquiry->company)
                                <div class="text-xs text-zinc-500">{{ $inquiry->company }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div>{{ $inquiry->email }}</div>
                            @if ($inquiry->phone)
                                <div class="text-xs text-zinc-500">{{ $inquiry->phone }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $inquiry->products->count() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:select
                                size="sm"
                                :value="$inquiry->status"
                                wire:change="updateStatus({{ $inquiry->id }}, $event.target.value)"
                            >
                                @foreach ($this->statusOptions() as $statusValue => $statusLabel)
                                    <flux:select.option :value="$statusValue">{{ __($statusLabel) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button wire:click="selectInquiry({{ $inquiry->id }})" variant="ghost" size="sm" icon="eye">
                                {{ __('Details') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal name="inquiry-details-modal" class="max-w-5xl md:min-w-[48rem]!">
        @if ($selectedInquiry)
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4 pr-12">
                    <div>
                        <flux:heading size="lg">{{ __('inquiryNumberId', ['id' => $selectedInquiry->id]) }}</flux:heading>
                        <flux:subheading>{{ __('Eingegangen am :date', ['date' => $selectedInquiry->created_at->format('d.m.Y H:i')]) }}</flux:subheading>
                    </div>
                    <flux:badge class="shrink-0" :variant="$this->statusBadgeVariant($selectedInquiry->status)">
                        {{ __($this->statusLabel($selectedInquiry->status)) }}
                    </flux:badge>
                </div>

                <flux:field>
                    <flux:label>{{ __('changeStatus') }}</flux:label>
                    <flux:select
                        :value="$selectedInquiry->status"
                        wire:change="updateStatus({{ $selectedInquiry->id }}, $event.target.value)"
                    >
                        @foreach ($this->statusOptions() as $statusValue => $statusLabel)
                            <flux:select.option :value="$statusValue">{{ __($statusLabel) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:separator text="{{ __('customerData') }}" />

                <dl class="space-y-2 text-sm text-zinc-700 dark:text-zinc-200">
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('salutation') }}</dt>
                        <dd>{{ $selectedInquiry->salutation ?: '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('Name') }}</dt>
                        <dd class="text-zinc-700 dark:text-zinc-200">{{ trim($selectedInquiry->first_name.' '.$selectedInquiry->last_name) }}</dd>
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
                        <dt class="text-zinc-500">{{ __('street') }}</dt>
                        <dd class="text-zinc-700 dark:text-zinc-200">{{ $selectedInquiry->street ?: '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-[10rem_1fr] gap-3">
                        <dt class="text-zinc-500">{{ __('postalCodeCity') }}</dt>
                        <dd>{{ trim(($selectedInquiry->postal_code ?? '').' '.($selectedInquiry->city ?? '')) ?: '—' }}</dd>
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
                        <div wire:key="inquiry-product-{{ $selectedInquiry->id }}-{{ $product->id }}" class="rounded-md border border-zinc-200 px-4 py-3 dark:border-zinc-700">
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
