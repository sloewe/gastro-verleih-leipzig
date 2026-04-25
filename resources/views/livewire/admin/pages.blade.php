<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('pages') }}</flux:heading>
            <flux:subheading>{{ __('manageStaticContentPagesWithMarkdownBlocks') }}</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('newPage') }}</flux:button>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
        <div class="space-y-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('searchPage') }}" icon="magnifying-glass" clearable />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('title') }}</flux:table.column>
                    <flux:table.column>{{ __('Slug') }}</flux:table.column>
                    <flux:table.column>{{ __('blocks') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->pages() as $listedPage)
                        <flux:table.row :key="$listedPage->id">
                            <flux:table.cell class="font-medium">{{ $listedPage->title }}</flux:table.cell>
                            <flux:table.cell>/{{ $listedPage->slug }}</flux:table.cell>
                            <flux:table.cell>{{ $listedPage->blocks_count }}</flux:table.cell>
                            <flux:table.cell class="flex justify-end gap-2">
                                <flux:button wire:click="edit({{ $listedPage->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $listedPage->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
            <form wire:submit="save" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ $editing ? __('editPage') : __('createNewPage') }}</flux:heading>
                    <flux:subheading>{{ __('definePageDataAndContentBlocks') }}</flux:subheading>
                </div>

                <div class="grid gap-4">
                    <flux:field>
                        <flux:label>{{ __('title') }}</flux:label>
                        <flux:input wire:model.live="title" />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Slug') }}</flux:label>
                        <flux:input wire:model="slug" placeholder="impressum" />
                        <flux:error name="slug" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Meta Title (optional)') }}</flux:label>
                        <flux:input wire:model="meta_title" />
                        <flux:error name="meta_title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Meta Description (optional)') }}</flux:label>
                        <flux:textarea wire:model="meta_description" rows="2" />
                        <flux:error name="meta_description" />
                    </flux:field>
                </div>

                <flux:separator text="{{ __('contentBlocks') }}" />

                <div class="space-y-4">
                    @foreach ($blocks as $index => $block)
                        <div wire:key="content-block-{{ $index }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="mb-3 flex items-center justify-between">
                                <flux:text class="font-medium">
                                    {{ __('Block #:position', ['position' => $index + 1]) }}
                                </flux:text>

                                <div class="flex items-center gap-2">
                                    <flux:button type="button" variant="ghost" size="sm" icon="arrow-up" wire:click="moveBlockUp({{ $index }})" />
                                    <flux:button type="button" variant="ghost" size="sm" icon="arrow-down" wire:click="moveBlockDown({{ $index }})" />
                                    <flux:button type="button" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" wire:click="removeBlock({{ $index }})" />
                                </div>
                            </div>

                            <flux:field>
                                <flux:label>{{ __('blockType') }}</flux:label>
                                <flux:input value="markdown" disabled />
                                <flux:error name="blocks.{{ $index }}.type" />
                            </flux:field>

                            <flux:field class="mt-4">
                                <flux:label>{{ __('Markdown') }}</flux:label>
                                <flux:textarea wire:model.live="blocks.{{ $index }}.content_markdown" rows="8" />
                                <flux:error name="blocks.{{ $index }}.content_markdown" />
                            </flux:field>

                            <div class="mt-4 space-y-2 rounded-md bg-zinc-50 p-3 dark:bg-zinc-800/70">
                                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">{{ __('livePreview') }}</flux:text>
                                <div class="prose prose-sm max-w-none dark:prose-invert">
                                    {!! \Illuminate\Support\Str::markdown($block['content_markdown'] ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between gap-4">
                    <flux:button type="button" variant="ghost" icon="plus" wire:click="addBlock">{{ __('addBlock') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('save') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
