<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Pages extends Component
{
    public string $search = '';

    public ?Page $page = null;

    public string $title = '';

    public string $slug = '';

    public string $meta_title = '';

    public string $meta_description = '';

    public bool $show_in_navigation = false;

    public string $navigation_label = '';

    /**
     * @var array<int, array{id:int|null,type:string,content_markdown:string}>
     */
    public array $blocks = [];

    public bool $editing = false;

    public function updatedTitle(string $value): void
    {
        if (! $this->editing) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    public function create(): void
    {
        $this->resetEditor();
        $this->addBlock();
        $this->dispatch('modal-show', name: 'page-editor-modal');
    }

    public function edit(Page $page): void
    {
        $this->page = $page;
        $this->editing = true;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->show_in_navigation = $page->show_in_navigation;
        $this->navigation_label = $page->navigation_label ?? '';
        $this->meta_title = $page->meta_title ?? '';
        $this->meta_description = $page->meta_description ?? '';
        $this->blocks = $page->blocks
            ->map(fn ($block): array => [
                'id' => $block->id,
                'type' => $block->type,
                'content_markdown' => $block->content_markdown ?? '',
            ])
            ->values()
            ->all();

        if ($this->blocks === []) {
            $this->addBlock();
        }

        $this->dispatch('modal-show', name: 'page-editor-modal');
    }

    public function delete(Page $page): void
    {
        $page->delete();
        Flux::toast(__('Seite wurde gelöscht.'));

        if ($this->page?->is($page)) {
            $this->resetEditor();
        }
    }

    public function addBlock(): void
    {
        $this->blocks[] = [
            'id' => null,
            'type' => 'markdown',
            'content_markdown' => '',
        ];
    }

    public function removeBlock(int $index): void
    {
        if (! array_key_exists($index, $this->blocks)) {
            return;
        }

        unset($this->blocks[$index]);
        $this->blocks = array_values($this->blocks);

        if ($this->blocks === []) {
            $this->addBlock();
        }
    }

    public function moveBlockUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->blocks[$index])) {
            return;
        }

        [$this->blocks[$index - 1], $this->blocks[$index]] = [$this->blocks[$index], $this->blocks[$index - 1]];
    }

    public function moveBlockDown(int $index): void
    {
        if (! isset($this->blocks[$index], $this->blocks[$index + 1])) {
            return;
        }

        [$this->blocks[$index + 1], $this->blocks[$index]] = [$this->blocks[$index], $this->blocks[$index + 1]];
    }

    public function save(): void
    {
        $wasEditing = $this->editing;

        $validated = $this->validate([
            'title' => ['required', 'min:3', 'max:255'],
            'slug' => [
                'required',
                'min:2',
                'max:255',
                'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($this->page?->id),
            ],
            'meta_title' => ['nullable', 'max:255'],
            'meta_description' => ['nullable', 'max:500'],
            'show_in_navigation' => ['boolean'],
            'navigation_label' => ['nullable', 'string', 'max:255'],
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*.type' => ['required', Rule::in(['markdown'])],
            'blocks.*.content_markdown' => ['nullable', 'string'],
        ]);

        $page = Page::query()->updateOrCreate(
            ['id' => $this->page?->id],
            [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'show_in_navigation' => $validated['show_in_navigation'],
                'navigation_label' => $validated['show_in_navigation']
                    ? ($validated['navigation_label'] ?: null)
                    : null,
                'meta_title' => $validated['meta_title'] ?: null,
                'meta_description' => $validated['meta_description'] ?: null,
            ]
        );

        $page->blocks()->delete();

        foreach ($validated['blocks'] as $index => $block) {
            $page->blocks()->create([
                'type' => $block['type'],
                'content_markdown' => $block['content_markdown'],
                'sort_order' => $index + 1,
            ]);
        }

        $this->edit($page->fresh('blocks'));
        Flux::toast($wasEditing ? __('Seite wurde gespeichert.') : __('Seite wurde erstellt.'));
    }

    /**
     * @return Collection<int, Page>
     */
    public function pages()
    {
        return Page::query()
            ->withCount('blocks')
            ->when(
                $this->search !== '',
                fn ($query) => $query
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%')
            )
            ->latest()
            ->get();
    }

    private function resetEditor(): void
    {
        $this->reset([
            'page',
            'title',
            'slug',
            'show_in_navigation',
            'navigation_label',
            'meta_title',
            'meta_description',
            'blocks',
            'editing',
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.pages');
    }
}
