
<div class="space-y-12">
    <section class="text-center space-y-4">
        <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
            {{ __('discoverOurAssortment') }}
        </flux:heading>
        <flux:subheading size="lg" class="max-w-2xl mx-auto text-gtc-muted">
            {{ __('highQualityEquipmentForYourEventInLeipzigAndTheSurroundingAreaEasyRentalRelaxedCelebration') }}
        </flux:subheading>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="group relative overflow-hidden rounded-apple bg-white shadow-sm transition-all duration-300 hover:shadow-md hover:scale-[1.02]">
                <div class="aspect-4/3 w-full overflow-hidden">
                    @if ($category->image_path)
                        <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gtc-mint/30">
                            <flux:icon name="photo" class="size-12 text-gtc-green/20" />
                        </div>
                    @endif
                </div>

                <div class="absolute inset-0 bg-linear-to-t from-gtc-ink/80 via-gtc-ink/20 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-6 w-full">
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg" class="text-white font-semibold!">
                            {{ $category->name }}
                        </flux:heading>

                        <div class="rounded-full bg-white/20 p-2 backdrop-blur-md transition-colors group-hover:bg-gtc-green">
                            <flux:icon name="arrow-right" class="size-5 text-white" />
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
