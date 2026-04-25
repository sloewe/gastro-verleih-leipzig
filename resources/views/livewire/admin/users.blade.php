
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('users') }}</flux:heading>
            <flux:subheading>{{ __('manageTheUsersWhoHaveAccessToTheBackend') }}</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('newUser') }}</flux:button>
    </div>

    <div class="flex items-center space-x-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('search') }}" icon="magnifying-glass" clearable />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column sortable wire:click="sortBy('name')">{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('email') }}</flux:table.column>
            <flux:table.column>{{ __('createdAt') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->users() as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('d.m.Y H:i') }}</flux:table.cell>
                    <flux:table.cell class="flex justify-end space-x-2">
                        <flux:button wire:click="edit({{ $user->id }})" variant="ghost" size="sm" icon="pencil-square" />
                        @if ($user->id !== auth()->id())
                            <flux:button wire:click="delete({{ $user->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="user-modal" class="min-w-[24rem]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editing ? __('editUser') : __('newUser') }}</flux:heading>
                <flux:subheading>{{ __('enterTheUserDetails') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('email') }}</flux:label>
                <flux:input type="email" wire:model="email" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('password') }}</flux:label>
                <flux:input type="password" wire:model="password" />
                <flux:subheading>
                    @if ($editing)
                        {{ __('leaveBlankToKeepTheCurrentPassword') }}
                    @endif
                </flux:subheading>
                <flux:error name="password" />
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
                <flux:heading size="lg">{{ __('deleteUser') }}</flux:heading>
                <flux:subheading>{{ __('areYouSureYouWantToDeleteThisUserThisActionCannotBeUndone') }}</flux:subheading>
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
