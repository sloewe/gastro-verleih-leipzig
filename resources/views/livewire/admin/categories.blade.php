
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('categories') }}</flux:heading>
            <flux:subheading>{{ __('manageYourProductCategories') }}</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('newCategory') }}</flux:button>
    </div>

    <div class="flex items-center space-x-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('search') }}" icon="magnifying-glass" clearable />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('image') }}</flux:table.column>
            <flux:table.column sortable wire:click="sortBy('name')">{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('createdAt') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->categories() as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>
                        @if ($category->image_path)
                            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-lg object-cover">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon name="photo" class="text-zinc-400" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->slug }}</flux:table.cell>
                    <flux:table.cell>{{ $category->created_at->format('d.m.Y H:i') }}</flux:table.cell>
                    <flux:table.cell class="flex justify-end space-x-2">
                        <flux:button wire:click="edit({{ $category->id }})" variant="ghost" size="sm" icon="pencil-square" />
                        <flux:button wire:click="delete({{ $category->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="category-modal" class="min-w-[24rem]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editing ? __('editCategory') : __('newCategory') }}</flux:heading>
                <flux:subheading>{{ __('enterTheCategoryDetails') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model.live="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Slug') }}</flux:label>
                <flux:input wire:model="slug" />
                <flux:error name="slug" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('image') }}</flux:label>
                <flux:input type="file" wire:model="image" />
                <flux:error name="image" />

                @if ($image)
                    <div class="mt-2">
                        <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 rounded-lg object-cover">
                    </div>
                @elseif ($editing && $category?->image_path)
                    <div class="mt-2">
                        <img src="{{ Storage::url($category->image_path) }}" class="h-24 w-24 rounded-lg object-cover">
                    </div>
                @endif
            </flux:field>

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
                <flux:heading size="lg">{{ __('deleteCategory') }}</flux:heading>
                <flux:subheading>{{ __('areYouSureYouWantToDeleteThisCategoryThisActionCannotBeUndone') }}</flux:subheading>
            </div>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="confirmDelete" variant="danger">{{ __('delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
