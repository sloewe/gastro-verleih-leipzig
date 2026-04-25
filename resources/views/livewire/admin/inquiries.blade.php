<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('inquiries') }}</flux:heading>
        <flux:subheading>{{ __('manageIncomingCustomerInquiriesAndTheirProcessingStatus') }}</flux:subheading>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
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
                    <flux:table.column>{{ __('received') }}</flux:table.column>
                    <flux:table.column>{{ __('customer') }}</flux:table.column>
                    <flux:table.column>{{ __('contact') }}</flux:table.column>
                    <flux:table.column>{{ __('items') }}</flux:table.column>
                    <flux:table.column>{{ __('status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($inquiries as $inquiry)
                        <flux:table.row :key="$inquiry->id">
                            <flux:table.cell>{{ $inquiry->created_at->format('d.m.Y H:i') }}</flux:table.cell>
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

        <div class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
            @if ($selectedInquiry)
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading size="lg">{{ __('inquiryNumberId', ['id' => $selectedInquiry->id]) }}</flux:heading>
                            <flux:subheading>{{ __('Eingegangen am :date', ['date' => $selectedInquiry->created_at->format('d.m.Y H:i')]) }}</flux:subheading>
                        </div>
                        <flux:badge :variant="$this->statusBadgeVariant($selectedInquiry->status)">
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

                    <dl class="space-y-2 text-sm">
                        <div class="grid grid-cols-[10rem_1fr] gap-3">
                            <dt class="text-zinc-500">{{ __('salutation') }}</dt>
                            <dd>{{ $selectedInquiry->salutation ?: '—' }}</dd>
                        </div>
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
                            <dt class="text-zinc-500">{{ __('street') }}</dt>
                            <dd>{{ $selectedInquiry->street ?: '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-[10rem_1fr] gap-3">
                            <dt class="text-zinc-500">{{ __('postalCodeCity') }}</dt>
                            <dd>{{ trim(($selectedInquiry->postal_code ?? '').' '.($selectedInquiry->city ?? '')) ?: '—' }}</dd>
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
                                        <div class="font-medium">{{ $product->name }}</div>
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
            @else
                <div class="flex min-h-[20rem] items-center justify-center">
                    <flux:text>{{ __('selectAnInquiryOnTheLeftToViewTheDetails') }}</flux:text>
                </div>
            @endif
        </div>
    </div>
</div>
