<div class="mx-auto max-w-3xl space-y-6 text-center">
    <x-slot:title>{{ __('thankYou') }}</x-slot:title>

    <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
        {{ __('thankYouForYourInquiry') }}
    </flux:heading>

    <flux:text class="text-gtc-muted">
        {{ __('weHaveReceivedYourInquiryAndWillGetBackToYouShortly') }}
    </flux:text>

    <div class="pt-2">
        <flux:button :href="route('home')" variant="primary">
            {{ __('backToHome') }}
        </flux:button>
    </div>
</div>
