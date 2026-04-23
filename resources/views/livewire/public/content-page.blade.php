<div class="space-y-8">
    <x-slot:title>{{ $page->meta_title ?: $page->title }}</x-slot:title>

    <section class="space-y-2">
        <flux:subheading size="sm" class="uppercase tracking-wider text-gtc-muted">
            {{ __('Information') }}
        </flux:subheading>
        <flux:heading size="2xl" level="1" class="tracking-tight text-gtc-green font-bold">
            {{ $page->title }}
        </flux:heading>
    </section>

    <div class="space-y-6">
        @foreach ($page->blocks as $block)
            <article class="rounded-apple border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="prose max-w-none">
                    {!! \Illuminate\Support\Str::markdown($block->content_markdown ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                </div>
            </article>
        @endforeach
    </div>
</div>
