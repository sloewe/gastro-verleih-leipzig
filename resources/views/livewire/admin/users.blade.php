
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Benutzer') }}</flux:heading>
            <flux:subheading>{{ __('Verwalten Sie die Benutzer, die Zugriff auf das Backend haben.') }}</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Neuer Benutzer') }}</flux:button>
    </div>

    <div class="flex items-center space-x-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Suche...') }}" icon="magnifying-glass" clearable />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column sortable wire:click="sortBy('name')">{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('E-Mail') }}</flux:table.column>
            <flux:table.column>{{ __('Erstellt am') }}</flux:table.column>
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
                <flux:heading size="lg">{{ $editing ? __('Benutzer bearbeiten') : __('Neuer Benutzer') }}</flux:heading>
                <flux:subheading>{{ __('Geben Sie die Details des Benutzers ein.') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('E-Mail') }}</flux:label>
                <flux:input type="email" wire:model="email" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Passwort') }}</flux:label>
                <flux:input type="password" wire:model="password" />
                <flux:subheading>
                    @if ($editing)
                        {{ __('Leer lassen, um das aktuelle Passwort zu behalten.') }}
                    @endif
                </flux:subheading>
                <flux:error name="password" />
            </flux:field>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Abbrechen') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Speichern') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-confirmation" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Benutzer löschen') }}</flux:heading>
                <flux:subheading>{{ __('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten? Dieser Vorgang kann nicht rückgängig gemacht werden.') }}</flux:subheading>
            </div>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Abbrechen') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="confirmDelete" variant="danger">{{ __('Löschen') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
