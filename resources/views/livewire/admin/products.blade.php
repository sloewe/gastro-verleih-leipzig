
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('products') }}</flux:heading>
            <flux:subheading>{{ __('manageYourProductsAndTheirDetails') }}</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('newProduct') }}</flux:button>
    </div>

    <div class="flex items-center space-x-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('search') }}" icon="magnifying-glass" clearable />

        <flux:select wire:model.live="categoryFilter" placeholder="{{ __('filterCategory') }}" clearable>
            @foreach($this->categories() as $category)
                <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column>{{ __('image') }}</flux:table.column>
            <flux:table.column sortable wire:click="sortBy('name')">{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('category') }}</flux:table.column>
            <flux:table.column>{{ __('price') }}</flux:table.column>
            <flux:table.column>{{ __('createdAt') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>
                        @if ($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon name="photo" class="text-zinc-400" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        {{ $product->name }}
                        <div class="text-xs text-zinc-500">{{ $product->slug }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" inset="top bottom">{{ $product->category->name }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ number_format($product->price, 2, ',', '.') }} €</flux:table.cell>
                    <flux:table.cell>{{ $product->created_at->format('d.m.Y') }}</flux:table.cell>
                    <flux:table.cell class="flex justify-end space-x-2">
                        <flux:button wire:click="showInquiries({{ $product->id }})" variant="ghost" size="sm" icon="document-text" />
                        <flux:button wire:click="edit({{ $product->id }})" variant="ghost" size="sm" icon="pencil-square" />
                        <flux:button wire:click="delete({{ $product->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="product-modal" class="min-w-[32rem]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editing ? __('editProduct') : __('newProduct') }}</flux:heading>
                <flux:subheading>{{ __('enterTheProductDetails') }}</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field class="col-span-2">
                    <flux:label>{{ __('category') }}</flux:label>
                    <flux:select wire:model="category_id" placeholder="{{ __('selectCategory') }}">
                        @foreach($this->categories() as $category)
                            <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model.live="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="slug" />
                    <flux:error name="slug" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('description') }}</flux:label>
                    <flux:textarea wire:model="description" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('keywordsCommaSeparated') }}</flux:label>
                    <flux:input wire:model="keywords" placeholder="Deko, Verleih, Hochzeit" />
                    <flux:error name="keywords" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('price2') }}</flux:label>
                    <flux:input type="number" step="0.01" wire:model="price" />
                    <flux:error name="price" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('vatPercent') }}</flux:label>
                    <flux:input type="number" step="0.01" min="0" max="99.99" wire:model="vat_rate" />
                    <flux:error name="vat_rate" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('image') }}</flux:label>
                    <flux:input type="file" wire:model="image" />
                    <flux:error name="image" />

                    @if ($image)
                        <div class="mt-2">
                            <img src="{{ $image->temporaryUrl() }}" class="h-32 w-32 rounded-lg object-cover">
                        </div>
                    @elseif ($editing && $product?->image_path)
                        <div class="mt-2">
                            <img src="{{ Storage::url($product->image_path) }}" class="h-32 w-32 rounded-lg object-cover">
                        </div>
                    @endif
                </flux:field>

                <flux:separator class="col-span-2" text="{{ __('optionalFeatures') }}" />

                <flux:field class="col-span-2">
                    <flux:label>{{ __('featureName') }}</flux:label>
                    <flux:input wire:model="feature_name" placeholder="z.B. Farbe" />
                    <flux:error name="feature_name" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>{{ __('valuesCommaSeparated') }}</flux:label>
                    <flux:input wire:model="feature_values" placeholder="Schwarz, Weiß, Silber" />
                    <flux:error name="feature_values" />
                </flux:field>
            </div>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-confirmation" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('deleteProduct') }}</flux:heading>
                <flux:subheading>{{ __('areYouSureYouWantToDeleteThisProduct') }}</flux:subheading>
            </div>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="confirmDelete" variant="danger">{{ __('delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="product-inquiries-modal" class="min-w-[42rem]">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('productInquiries') }}</flux:heading>
                @if ($inquiryHistoryProduct)
                    <flux:subheading>
                        {{ __(':name (ID: :id)', ['name' => $inquiryHistoryProduct->name, 'id' => $inquiryHistoryProduct->id]) }}
                    </flux:subheading>
                @endif
            </div>

            @if ($productInquiries !== [])
                <div class="max-h-[24rem] overflow-y-auto rounded-md border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">{{ __('date') }}</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">{{ __('customer') }}</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">{{ __('quantity') }}</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-300">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($productInquiries as $inquiry)
                                <tr wire:key="product-inquiry-history-{{ $inquiry['inquiry_id'] }}-{{ $loop->index }}">
                                    <td class="px-4 py-3">
                                        <div>{{ $inquiry['inquiry_date'] }}</div>
                                        <div class="text-xs text-zinc-500">{{ __('inquiryNumberId', ['id' => $inquiry['inquiry_id']]) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $inquiry['customer_name'] }}</div>
                                        @if ($inquiry['customer_company'])
                                            <div class="text-xs text-zinc-500">{{ $inquiry['customer_company'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $inquiry['quantity'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:modal.close>
                                            <a
                                                href="{{ route('admin.inquiries', ['inquiry' => $inquiry['inquiry_id']]) }}"
                                                class="inline-flex items-center text-sm font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-200 dark:hover:text-zinc-100"
                                            >
                                                {{ __('toInquiry') }}
                                            </a>
                                        </flux:modal.close>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <flux:text>{{ __('noInquiriesAvailableForThisProduct') }}</flux:text>
            @endif

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('close') }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
