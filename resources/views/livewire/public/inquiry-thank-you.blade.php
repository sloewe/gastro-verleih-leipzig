<div class="mx-auto max-w-3xl space-y-6 text-center">
    <x-slot:title>{{ __('Vielen Dank') }}</x-slot:title>

    <flux:heading size="2xl" level="1" class="text-gtc-green tracking-tight font-bold">
        {{ __('Vielen Dank fuer Ihre Anfrage') }}
    </flux:heading>

    <flux:text class="text-gtc-muted">
        {{ __('Wir haben Ihre Anfrage erhalten und melden uns zeitnah bei Ihnen.') }}
    </flux:text>

    <div class="pt-2">
        <flux:button :href="route('home')" variant="primary">
            {{ __('Zur Startseite') }}
        </flux:button>
    </div>
</div>
